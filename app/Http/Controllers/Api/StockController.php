<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMutation;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier', 'brand'])
            ->where('office_id', session('active_office_id'));

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_produk', 'LIKE', "%{$request->search}%")
                    ->orWhere('sku_kode', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->category) {
            $query->where('product_category_id', $request->category);
        }

        $query->where('track_stock', true);

        if ($request->location_id) {
            $query->withSum(['stock_mutations as location_qty' => function ($q) use ($request) {
                $q->where('stock_location_id', $request->location_id);
            }], 'qty'); // Note: This sums ALL qty. For FIFO, we need IN - OUT.

            // Actually, we need to sum IN as positive and OUT as negative.
            // Eloquent withSum doesn't easily support CASE WHEN. I'll use a raw subquery.

            $query->select('*')->addSelect([
                'location_qty' => StockMutation::selectRaw('SUM(CASE WHEN type = "IN" THEN qty ELSE -qty END)')
                    ->whereColumn('product_id', 'products.id')
                    ->where('stock_location_id', $request->location_id),
            ]);
        }

        $perPage = $request->get('per_page', 10);
        if ($perPage >= 1000) {
            $data = $query->latest()->get();
        } else {
            $data = $query->latest()->paginate($perPage)->withQueryString();
        }

        return apiResponse(true, 'Data stok produk', $data);
    }

    public function dashboard(Request $request)
    {
        $officeId = session('active_office_id');
        $locationId = $request->location_id ?: $request->warehouse_id;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        // Base product query
        $productQuery = Product::where('track_stock', true)
            ->where('office_id', $officeId);

        // Base Invoice query for pending orders (These might be office-based, not location-based)
        $pendingReceiveQuery = \App\Models\Invoice::where('tipe_invoice', 'Purchase')
            ->where('office_id', $officeId)
            ->where('status_perjalanan', '!=', 'Diterima');

        $pendingShipQuery = \App\Models\Invoice::where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->where('status_perjalanan', '!=', 'Terkirim')
            ->where('status_perjalanan', '!=', 'Diterima');

        if ($locationId) {
            // If location specific logic is needed, add here.
            // But invoices are usually office-level until allocated?
            // Assuming locationId is within the office.
        }

        if ($dateFrom) {
            $pendingReceiveQuery->where('tgl_invoice', '>=', $dateFrom);
            $pendingShipQuery->where('tgl_invoice', '>=', $dateFrom);
        }
        if ($dateTo) {
            $pendingReceiveQuery->where('tgl_invoice', '<=', $dateTo);
            $pendingShipQuery->where('tgl_invoice', '<=', $dateTo);
        }

        // Stats
        $pendingReceive = $pendingReceiveQuery->sum('total_akhir');
        $pendingShip = $pendingShipQuery->sum('total_akhir');

        if ($locationId) {
            $totalQty = StockMutation::where('stock_location_id', $locationId)
                ->where('office_id', $officeId)
                ->selectRaw('SUM(CASE WHEN type = "IN" THEN qty ELSE -qty END) as total')
                ->first()->total ?? 0;

            // Valuation is harder per location if not tracking remaining cost per location batch
            // For now, use global product cost
            $inventoryValue = Product::where('track_stock', true)
                ->where('office_id', $officeId)
                ->selectRaw('SUM((SELECT SUM(CASE WHEN type = "IN" THEN qty ELSE -qty END) FROM stock_mutations WHERE product_id = products.id AND stock_location_id = ?) * harga_beli) as total_value', [$locationId])
                ->first()->total_value ?? 0;

            // Location-specific counts are expensive without materialized views or better schema
            // We'll skip low/out stock counts for location-specific view for now or estimate
            $lowStockCount = 0;
            $outOfStockCount = 0;
            $totalItems = Product::where('track_stock', true)->where('office_id', $officeId)
                ->whereHas('stock_mutations', function ($q) use ($locationId) {
                    $q->where('stock_location_id', $locationId);
                })->count();

        } else {
            $totalQty = $productQuery->sum('qty');
            $inventoryValue = $productQuery->selectRaw('SUM(qty * harga_beli) as total_value')->first()->total_value ?? 0;

            $totalItems = $productQuery->count();
            $lowStockCount = (clone $productQuery)->where('qty', '>', 0)->where('qty', '<', 5)->count();
            $outOfStockCount = (clone $productQuery)->where('qty', '<=', 0)->count();
        }

        // Chart Data (Last 6 Months)
        $months = [];
        $qtyIncoming = [];
        $qtyOutgoing = [];
        $valIncoming = [];
        $valOutgoing = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M');
            $months[] = $monthName;

            $start = $date->startOfMonth()->toDateTimeString();
            $end = $date->endOfMonth()->toDateTimeString();

            $incomingQuery = StockMutation::where('type', 'IN')
                ->where('office_id', $officeId)
                ->whereBetween('created_at', [$start, $end]);
            $outgoingQuery = StockMutation::where('type', 'OUT')
                ->where('office_id', $officeId)
                ->whereBetween('created_at', [$start, $end]);

            if ($locationId) {
                $incomingQuery->where('stock_location_id', $locationId);
                $outgoingQuery->where('stock_location_id', $locationId);
            }

            $incoming = $incomingQuery->selectRaw('SUM(qty) as q, SUM(qty * cost_price) as v')->first();
            $outgoing = $outgoingQuery->selectRaw('SUM(qty) as q, SUM(qty * cost_price) as v')->first();

            $qtyIncoming[] = (float) ($incoming->q ?? 0);
            $qtyOutgoing[] = (float) ($outgoing->q ?? 0);
            $valIncoming[] = (float) ($incoming->v ?? 0);
            $valOutgoing[] = (float) ($outgoing->v ?? 0);
        }

        // Top & Bottom Products (based on OUT mutations)
        $topProducts = Product::where('track_stock', true)
            ->where('office_id', $officeId)
            ->withSum(['stock_mutations as sent_qty' => function ($q) use ($locationId, $officeId) {
                $q->where('type', 'OUT')->where('office_id', $officeId);
                if ($locationId) {
                    $q->where('stock_location_id', $locationId);
                }
            }], 'qty')
            ->orderByDesc('sent_qty')
            ->limit(5)
            ->get();

        $bottomProducts = Product::where('track_stock', true)
            ->where('office_id', $officeId)
            ->withSum(['stock_mutations as sent_qty' => function ($q) use ($locationId, $officeId) {
                $q->where('type', 'OUT')->where('office_id', $officeId);
                if ($locationId) {
                    $q->where('stock_location_id', $locationId);
                }
            }], 'qty')
            ->orderBy('sent_qty', 'asc')
            ->limit(5)
            ->get();

        return apiResponse(true, 'Statistik stok detail', [
            'overview' => [
                'pending_receive' => (float) $pendingReceive,
                'pending_ship' => (float) $pendingShip,
                'total_qty' => (float) $totalQty,
                'inventory_value' => (float) $inventoryValue,
                'total_items' => (int) $totalItems,
                'low_stock_count' => (int) $lowStockCount,
                'out_of_stock_count' => (int) $outOfStockCount,
            ],
            'charts' => [
                'labels' => $months,
                'qty' => [
                    'incoming' => $qtyIncoming,
                    'outgoing' => $qtyOutgoing,
                ],
                'value' => [
                    'incoming' => $valIncoming,
                    'outgoing' => $valOutgoing,
                ],
            ],
            'top_products' => $topProducts,
            'bottom_products' => $bottomProducts,
        ]);
    }

    public function mutations(Request $request)
    {
        $query = StockMutation::with([
            'product:id,nama_produk,sku_kode',
            'stock_location:id,name',
        ])
            ->where('office_id', session('active_office_id'));

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('sku_kode', 'like', "%{$search}%");
            });
        }

        if ($request->location_id) {
            $query->where('stock_location_id', $request->location_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $data = $query->latest()->paginate($request->get('per_page', 10))->withQueryString();

        $mutationIds = $data->getCollection()->pluck('id')->all();
        $actors = [];
        if (! empty($mutationIds)) {
            $logs = DB::table('activity_logs')
                ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
                ->whereNull('activity_logs.deleted_at')
                ->where('activity_logs.office_id', session('active_office_id'))
                ->where('activity_logs.tabel_terkait', 'stock_mutations')
                ->where('activity_logs.tindakan', 'Create')
                ->whereIn('activity_logs.data_id', $mutationIds)
                ->select('activity_logs.data_id', 'users.name as user_name', 'users.username as username')
                ->get();

            foreach ($logs as $l) {
                $actors[(int) $l->data_id] = [
                    'name' => $l->user_name,
                    'username' => $l->username,
                ];
            }
        }

        $data->getCollection()->transform(function ($m) use ($actors) {
            $ref = $m->reference_type;
            $actionLabel = match ($ref) {
                'Opening Stock' => 'Persediaan Awal',
                'Adjustment' => 'Penyesuaian',
                'Purchase' => 'Pembelian',
                'Sales' => 'Penjualan',
                default => $ref ?: 'Mutasi Stok',
            };

            $actor = $actors[(int) $m->id] ?? null;
            $m->action_label = $actionLabel;
            $m->actor = $actor;

            return $m;
        });

        return apiResponse(true, 'Data mutasi stok', $data);
    }

    public function updateStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'qty' => 'required|numeric|min:0',
            'stock_location_id' => 'nullable|exists:stock_locations,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        // Validate Product ownership
        $product = Product::where('id', $id)->where('office_id', session('active_office_id'))->first();
        if (! $product) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        // Validate Location ownership if provided
        if ($request->stock_location_id) {
            $location = \App\Models\StockLocation::where('id', $request->stock_location_id)
                ->where('office_id', session('active_office_id'))
                ->first();
            if (! $location) {
                return apiResponse(false, 'Lokasi stok tidak valid', null, null, 422);
            }
        }

        try {
            $this->stockService->adjustStock($id, $request->qty, $request->stock_location_id, $request->notes);

            return apiResponse(true, 'Stok berhasil diperbarui');
        } catch (\Exception $e) {
            return apiResponse(false, 'Gagal memperbarui stok: '.$e->getMessage(), null, null, 500);
        }
    }

    public function openingStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'stock_location_id' => 'required|exists:stock_locations,id',
            'qty' => 'required|numeric|min:1',
            'cost_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        // Validate Product ownership
        $product = Product::where('id', $request->product_id)->where('office_id', session('active_office_id'))->first();
        if (! $product) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        // Validate Location ownership
        $location = \App\Models\StockLocation::where('id', $request->stock_location_id)
            ->where('office_id', session('active_office_id'))
            ->first();
        if (! $location) {
            return apiResponse(false, 'Lokasi stok tidak valid', null, null, 422);
        }

        try {
            $this->stockService->recordIn(
                $request->product_id,
                $request->qty,
                $request->cost_price,
                $request->stock_location_id,
                'Opening Stock',
                null,
                'Initial stock entry'
            );

            return apiResponse(true, 'Persediaan awal berhasil dicatat');
        } catch (\Exception $e) {
            return apiResponse(false, 'Gagal mencatat persediaan awal: '.$e->getMessage(), null, null, 500);
        }
    }

    public function stockOpname(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'stock_location_id' => 'required|exists:stock_locations,id',
            'qty_physical' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        // Validate Product ownership
        $product = Product::where('id', $request->product_id)->where('office_id', session('active_office_id'))->first();
        if (! $product) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        // Validate Location ownership
        $location = \App\Models\StockLocation::where('id', $request->stock_location_id)
            ->where('office_id', session('active_office_id'))
            ->first();
        if (! $location) {
            return apiResponse(false, 'Lokasi stok tidak valid', null, null, 422);
        }

        try {
            $this->stockService->adjustStock(
                $request->product_id,
                $request->qty_physical,
                $request->stock_location_id,
                $request->notes ?: 'Stock Opname'
            );

            return apiResponse(true, 'Stok opname berhasil dicatat');
        } catch (\Exception $e) {
            return apiResponse(false, 'Gagal mencatat stok opname: '.$e->getMessage(), null, null, 500);
        }
    }

    public function fifo($id)
    {
        $product = Product::where('id', $id)->where('office_id', session('active_office_id'))->first();
        if (! $product) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        $locationId = request('location_id');

        $query = StockMutation::where('product_id', $id)
            ->where('type', 'IN')
            ->where('remaining_qty', '>', 0)
            ->with('stock_location');

        if ($locationId) {
            $query->where('stock_location_id', $locationId);
        }

        $batches = $query->orderBy('created_at', 'asc')->get();
        $totalStock = $batches->sum('remaining_qty');

        return apiResponse(true, 'Data FIFO', [
            'product' => $product,
            'batches' => $batches,
            'total_stock' => $totalStock,
        ]);
    }
}
