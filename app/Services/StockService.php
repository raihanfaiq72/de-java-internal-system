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
            if (! $product) {
                throw new \Exception('Product not found');
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
            if (! $product) {
                throw new \Exception('Product not found');
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
                if ($remainingToDeduct <= 0) {
                    break;
                }

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
        // Calculate current quantity for the specific location to ensure local accuracy
        $query = StockMutation::where('product_id', $productId);
        
        // Filter by location (required by new UI rules)
        $query->where('stock_location_id', $stockLocationId);
        
        $currentQty = (float) $query->selectRaw('SUM(CASE 
                WHEN type = "IN" THEN qty 
                WHEN type = "ADJUSTMENT" THEN qty 
                WHEN type = "OUT" THEN -qty 
                ELSE 0 END) as total')
            ->value('total') ?? 0;
    
        $diff = (float) $newTotalQty - $currentQty;

        if ($diff > 0) {
            $product = Product::find($productId);

            return $this->recordIn($productId, $diff, $product->harga_beli, $stockLocationId, 'Adjustment', null, $notes);
        } elseif ($diff < 0) {
            return $this->recordOut($productId, abs($diff), $stockLocationId, 'Adjustment', null, $notes);
        }

        return null;
    }

    /**
     * Bulk adjust stock across multiple locations
     */
    /**
     * Bulk adjust stock across multiple locations
     */
    public function adjustStockByLocations($productId, array $adjustments, $notes = null)
    {
        return DB::transaction(function () use ($productId, $adjustments, $notes) {
            $results = [];
            
            // Find unlocated adjustment
            $unlocatedAdjIndex = -1;
            foreach ($adjustments as $index => $adj) {
                if (!isset($adj['location_id']) || $adj['location_id'] === null || $adj['location_id'] === 'null' || $adj['location_id'] === '') {
                    $unlocatedAdjIndex = $index;
                    break;
                }
            }

            // Handle moving unlocated stock to Gudang ID 1 (Primary Warehouse)
            if ($unlocatedAdjIndex !== -1) {
                $unlocatedAdj = $adjustments[$unlocatedAdjIndex];
                unset($adjustments[$unlocatedAdjIndex]);

                $targetForUnlocated = (float)($unlocatedAdj['qty'] ?? 0);

                // 1. Get current NULL stock to clear it
                $currentNull = StockMutation::where('product_id', $productId)
                    ->whereNull('stock_location_id')
                    ->selectRaw('SUM(CASE 
                        WHEN type = "IN" THEN qty 
                        WHEN type = "ADJUSTMENT" THEN qty 
                        WHEN type = "OUT" THEN -qty 
                        ELSE 0 END) as total')
                    ->value('total') ?? 0;

                if ($currentNull != 0) {
                    // Set NULL location to 0
                    $this->adjustStock($productId, 0, null, 'Auto-consolidation: Moving to primary warehouse. ' . ($notes ?? ''));
                }

                // 2. Add the target amount for unlocated stock to Gudang ID 1
                $foundGudang1 = false;
                foreach ($adjustments as &$adj) {
                    if ($adj['location_id'] == 1) {
                        $adj['qty'] = (float)$adj['qty'] + $targetForUnlocated;
                        $foundGudang1 = true;
                        break;
                    }
                }
                if (!$foundGudang1) {
                    // Get current Gudang 1 stock to calculate the new total
                    $currentG1 = StockMutation::where('product_id', $productId)
                        ->where('stock_location_id', 1)
                        ->selectRaw('SUM(CASE 
                            WHEN type = "IN" THEN qty 
                            WHEN type = "ADJUSTMENT" THEN qty 
                            WHEN type = "OUT" THEN -qty 
                            ELSE 0 END) as total')
                        ->value('total') ?? 0;
                        
                    $adjustments[] = [
                        'location_id' => 1,
                        'qty' => (float)$currentG1 + $targetForUnlocated
                    ];
                }
            }

            foreach ($adjustments as $adj) {
                $locationId = $adj['location_id'] ?? null;
                $newQty = $adj['qty'] ?? 0;
                $results[] = $this->adjustStock($productId, $newQty, $locationId, $notes);
            }
            return $results;
        });
    }

    /**
     * Recalculate and update product total quantity based on mutations
     */
    public function recalculateProductStock($productId)
    {
        $this->updateProductTotalQty($productId);

        return Product::find($productId)->qty;
    }

    /**
     * Sync Product table QTY column with Mutations
     */
    private function updateProductTotalQty($productId)
    {
        $in = StockMutation::where('product_id', $productId)->where('type', 'IN')->sum('qty');
        $out = StockMutation::where('product_id', $productId)->where('type', 'OUT')->sum('qty');
        $adj = StockMutation::where('product_id', $productId)->where('type', 'ADJUSTMENT')->sum('qty');

        $total = ($in + $adj) - $out;

        Product::where('id', $productId)->update(['qty' => $total]);
    }
}
