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

            // Enforce location
            if (! $stockLocationId) {
                $stockLocationId = $this->getDefaultLocation($product->office_id);
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
            $deductions = [];

            // 1. Get all active warehouses for this office
            $locations = DB::table('stock_locations')
                ->where('office_id', $product->office_id)
                ->whereNull('deleted_at')
                ->orderBy('id', 'asc')
                ->pluck('id')
                ->toArray();

            // 2. Determine search order
            $searchLocations = $locations;
            if ($stockLocationId && $referenceType !== 'Sales') {
                // For Adjustments, only look at the requested warehouse
                $searchLocations = [$stockLocationId];
            } elseif ($stockLocationId) {
                // For Sales with preferred warehouse, put it first, then others
                $searchLocations = array_values(array_unique(array_merge([$stockLocationId], $locations)));
            }

            // 3. Sequential Warehouse Deduction Loop
            foreach ($searchLocations as $locId) {
                if ($remainingToDeduct <= 0) {
                    break;
                }

                // 3a. Calculate REAL current stock in this warehouse (Net sum of all movements)
                $currentWarehouseQty = (float) StockMutation::where('product_id', $productId)
                    ->where('stock_location_id', $locId)
                    ->selectRaw('SUM(CASE 
                        WHEN type = "IN" THEN qty 
                        WHEN type = "ADJUSTMENT" THEN qty 
                        WHEN type = "OUT" THEN -qty 
                        ELSE 0 END) as total')
                    ->value('total') ?? 0;

                if ($currentWarehouseQty <= 0) {
                    continue;
                }

                // How much we can take from THIS warehouse
                $toTakeFromThisWarehouse = min($currentWarehouseQty, $remainingToDeduct);

                // 3b. Find FIFO batches for THIS warehouse only
                $batches = StockMutation::where('product_id', $productId)
                    ->where('stock_location_id', $locId)
                    ->where('type', 'IN')
                    ->where('remaining_qty', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->lockForUpdate()
                    ->get();

                $takenFromBatches = 0;
                foreach ($batches as $batch) {
                    if ($takenFromBatches >= $toTakeFromThisWarehouse) {
                        break;
                    }

                    $takeFromBatch = min($batch->remaining_qty, $toTakeFromThisWarehouse - $takenFromBatches);
                    $batch->decrement('remaining_qty', $takeFromBatch);

                    if (! isset($deductions[$locId])) {
                        $deductions[$locId] = ['qty' => 0, 'total_cost' => 0];
                    }
                    $deductions[$locId]['qty'] += $takeFromBatch;
                    $deductions[$locId]['total_cost'] += ($takeFromBatch * $batch->cost_price);

                    $takenFromBatches += $takeFromBatch;
                }

                // Fallback for batches (in case of legacy data inconsistency)
                if ($takenFromBatches < $toTakeFromThisWarehouse) {
                    $extra = $toTakeFromThisWarehouse - $takenFromBatches;
                    if (! isset($deductions[$locId])) {
                        $deductions[$locId] = ['qty' => 0, 'total_cost' => 0];
                    }
                    $deductions[$locId]['qty'] += $extra;
                    $deductions[$locId]['total_cost'] += ($extra * ($product->harga_beli ?? 0));
                    $takenFromBatches += $extra;
                }

                $remainingToDeduct -= $takenFromBatches;
            }

            // 4. Fallback for "unlocated" (NULL) stock if still needed
            if ($remainingToDeduct > 0) {
                // Calculate REAL current unlocated stock
                $currentNullQty = (float) StockMutation::where('product_id', $productId)
                    ->whereNull('stock_location_id')
                    ->selectRaw('SUM(CASE 
                        WHEN type = "IN" THEN qty 
                        WHEN type = "ADJUSTMENT" THEN qty 
                        WHEN type = "OUT" THEN -qty 
                        ELSE 0 END) as total')
                    ->value('total') ?? 0;

                if ($currentNullQty > 0) {
                    $toTakeFromNull = min($currentNullQty, $remainingToDeduct);

                    $nullBatches = StockMutation::where('product_id', $productId)
                        ->whereNull('stock_location_id')
                        ->where('type', 'IN')
                        ->where('remaining_qty', '>', 0)
                        ->orderBy('created_at', 'asc')
                        ->lockForUpdate()
                        ->get();

                    $takenFromNullBatches = 0;
                    foreach ($nullBatches as $batch) {
                        if ($takenFromNullBatches >= $toTakeFromNull) {
                            break;
                        }

                        $takeFromBatch = min($batch->remaining_qty, $toTakeFromNull - $takenFromNullBatches);
                        $batch->decrement('remaining_qty', $takeFromBatch);

                        $primaryLocId = $this->getDefaultLocation($product->office_id);
                        if (! isset($deductions[$primaryLocId])) {
                            $deductions[$primaryLocId] = ['qty' => 0, 'total_cost' => 0];
                        }
                        $deductions[$primaryLocId]['qty'] += $takeFromBatch;
                        $deductions[$primaryLocId]['total_cost'] += ($takeFromBatch * $batch->cost_price);

                        $takenFromNullBatches += $takeFromBatch;
                    }

                    // Fallback for null batches
                    if ($takenFromNullBatches < $toTakeFromNull) {
                        $extra = $toTakeFromNull - $takenFromNullBatches;
                        $primaryLocId = $this->getDefaultLocation($product->office_id);
                        if (! isset($deductions[$primaryLocId])) {
                            $deductions[$primaryLocId] = ['qty' => 0, 'total_cost' => 0];
                        }
                        $deductions[$primaryLocId]['qty'] += $extra;
                        $deductions[$primaryLocId]['total_cost'] += ($extra * ($product->harga_beli ?? 0));
                        $takenFromNullBatches += $extra;
                    }

                    $remainingToDeduct -= $takenFromNullBatches;
                }
            }

            // 5. Create OUT mutations for actual deductions (Splitting per warehouse)
            foreach ($deductions as $locId => $data) {
                StockMutation::create([
                    'office_id' => $product->office_id,
                    'product_id' => $productId,
                    'stock_location_id' => $locId,
                    'type' => 'OUT',
                    'qty' => $data['qty'],
                    'remaining_qty' => 0,
                    'cost_price' => ($data['qty'] > 0) ? ($data['total_cost'] / $data['qty']) : 0,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'notes' => $notes,
                ]);
            }

            // Deficit handling: If still remaining, record against primary location instead of NULL
            if ($remainingToDeduct > 0) {
                $deficitLocId = $stockLocationId ?? $this->getDefaultLocation($product->office_id);
                StockMutation::create([
                    'office_id' => $product->office_id,
                    'product_id' => $productId,
                    'stock_location_id' => $deficitLocId,
                    'type' => 'OUT',
                    'qty' => $remainingToDeduct,
                    'remaining_qty' => 0,
                    'cost_price' => $product->harga_beli ?? 0,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'notes' => $notes.($qty > $remainingToDeduct ? ' (Defisit stok)' : ''),
                ]);
            }

            $this->updateProductTotalQty($productId);

            return true;
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
                if (! isset($adj['location_id']) || $adj['location_id'] === null || $adj['location_id'] === 'null' || $adj['location_id'] === '') {
                    $unlocatedAdjIndex = $index;
                    break;
                }
            }

            // Handle moving unlocated stock to Gudang ID 1 (Primary Warehouse)
            if ($unlocatedAdjIndex !== -1) {
                $unlocatedAdj = $adjustments[$unlocatedAdjIndex];
                unset($adjustments[$unlocatedAdjIndex]);

                $targetForUnlocated = (float) ($unlocatedAdj['qty'] ?? 0);

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
                    $this->adjustStock($productId, 0, null, 'Auto-consolidation: Moving to primary warehouse. '.($notes ?? ''));
                }

                // 2. Add the target amount for unlocated stock to Gudang ID 1
                $foundGudang1 = false;
                foreach ($adjustments as &$adj) {
                    if ($adj['location_id'] == 1) {
                        $adj['qty'] = (float) $adj['qty'] + $targetForUnlocated;
                        $foundGudang1 = true;
                        break;
                    }
                }
                if (! $foundGudang1) {
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
                        'qty' => (float) $currentG1 + $targetForUnlocated,
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

    private function getDefaultLocation($officeId)
    {
        return DB::table('stock_locations')
            ->where('office_id', $officeId)
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->value('id') ?? 1;
    }
}
