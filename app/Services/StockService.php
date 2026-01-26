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
    public function recordIn($productId, $qty, $costPrice, $stockLocationId = null, $referenceType = null, $referenceId = null, $notes = null)
    {
        return DB::transaction(function () use ($productId, $qty, $costPrice, $stockLocationId, $referenceType, $referenceId, $notes) {
            $product = Product::find($productId);
            if (!$product) {
                throw new \Exception("Product not found");
            }

            $mutation = StockMutation::create([
                'office_id' => $product->office_id,
                'product_id' => $productId,
                'stock_location_id' => $stockLocationId,
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
    public function recordOut($productId, $qty, $stockLocationId = null, $referenceType = null, $referenceId = null, $notes = null)
    {
        return DB::transaction(function () use ($productId, $qty, $stockLocationId, $referenceType, $referenceId, $notes) {
            $product = Product::find($productId);
            if (!$product) {
                throw new \Exception("Product not found");
            }

            $remainingToDeduct = $qty;

            // Find oldest IN mutations with remaining stock IN THE SAME LOCATION
            $query = StockMutation::where('product_id', $productId)
                ->where('type', 'IN')
                ->where('remaining_qty', '>', 0);
            
            if ($stockLocationId) {
                $query->where('stock_location_id', $stockLocationId);
            }

            $batches = $query->orderBy('created_at', 'asc')
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
                'office_id' => $product->office_id,
                'product_id' => $productId,
                'stock_location_id' => $stockLocationId,
                'type' => 'OUT',
                'qty' => $qty,
                'remaining_qty' => 0,
                'cost_price' => ($qty > 0) ? ($totalCost / $qty) : 0, 
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
    public function adjustStock($productId, $newTotalQty, $stockLocationId = null, $notes = null)
    {
        // For total calculation, we need to know the current qty in that specific location
        $currentQty = StockMutation::where('product_id', $productId)
            ->where('stock_location_id', $stockLocationId)
            ->selectRaw('SUM(CASE WHEN type = "IN" THEN qty ELSE -qty END) as total')
            ->first()->total ?? 0;

        $diff = $newTotalQty - $currentQty;

        if ($diff > 0) {
            $product = Product::find($productId);
            return $this->recordIn($productId, $diff, $product->harga_beli, $stockLocationId, 'Adjustment', null, $notes);
        } elseif ($diff < 0) {
            return $this->recordOut($productId, abs($diff), $stockLocationId, 'Adjustment', null, $notes);
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
