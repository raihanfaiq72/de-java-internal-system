<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Record stock IN (Purchase or positive Adjustment)
     */
    public function recordIn($productId, $qty, $costPrice, $referenceType = null, $referenceId = null, $notes = null)
    {
        return DB::transaction(function () use ($productId, $qty, $costPrice, $referenceType, $referenceId, $notes) {
            $mutation = StockMutation::create([
                'product_id' => $productId,
                'type' => 'IN',
                'qty' => $qty,
                'remaining_qty' => $qty,
                'cost_price' => $costPrice,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);

            $this->updateProductTotalQty($productId);

            return $mutation;
        });
    }

    /**
     * Record stock OUT (Sales or negative Adjustment) using FIFO
     */
    public function recordOut($productId, $qty, $referenceType = null, $referenceId = null, $notes = null)
    {
        return DB::transaction(function () use ($productId, $qty, $referenceType, $referenceId, $notes) {
            $remainingToDeduct = $qty;

            // Find oldest IN mutations with remaining stock
            $batches = StockMutation::where('product_id', $productId)
                ->where('type', 'IN')
                ->where('remaining_qty', '>', 0)
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->get();

            $totalCost = 0;
            foreach ($batches as $batch) {
                if ($remainingToDeduct <= 0) break;

                $deduct = min($batch->remaining_qty, $remainingToDeduct);
                $totalCost += ($deduct * $batch->cost_price);
                
                $batch->decrement('remaining_qty', $deduct);
                $remainingToDeduct -= $deduct;
            }

            // Record the OUT mutation
            $mutation = StockMutation::create([
                'product_id' => $productId,
                'type' => 'OUT',
                'qty' => $qty,
                'remaining_qty' => 0,
                'cost_price' => ($qty > 0) ? ($totalCost / $qty) : 0, // Store unit cost for consistency
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);

            $this->updateProductTotalQty($productId);

            return $mutation;
        });
    }

    /**
     * Record an adjustment (positive or negative)
     */
    public function adjustStock($productId, $newTotalQty, $notes = null)
    {
        $product = Product::find($productId);
        $currentQty = $product->qty;
        $diff = $newTotalQty - $currentQty;

        if ($diff > 0) {
            return $this->recordIn($productId, $diff, $product->harga_beli, 'Adjustment', null, $notes);
        } elseif ($diff < 0) {
            return $this->recordOut($productId, abs($diff), 'Adjustment', null, $notes);
        }

        return null;
    }

    /**
     * Sync Product table QTY column with Mutations
     */
    private function updateProductTotalQty($productId)
    {
        $in = StockMutation::where('product_id', $productId)->where('type', 'IN')->sum('qty');
        $out = StockMutation::where('product_id', $productId)->where('type', 'OUT')->sum('qty');
        $adj = StockMutation::where('product_id', $productId)->where('type', 'ADJUSTMENT')->sum('qty');

        // Note: For simplicity, I'll count IN as positive and OUT as negative.
        // If ADJUSTMENT is added later as a separate type, we handle it too.
        
        $total = $in - $out;

        Product::where('id', $productId)->update(['qty' => $total]);
    }
}
