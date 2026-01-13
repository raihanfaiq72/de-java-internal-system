<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ActivityLog;
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
        $query = Product::with(['category', 'unit']);

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
        $data = $query->latest()->paginate(10);

        return apiResponse(true, 'Data stok produk', $data);
    }

    public function dashboard(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        // Base product query
        $productQuery = Product::where('track_stock', true);
        
        // Base Invoice query for pending orders
        $pendingReceiveQuery = \App\Models\Invoice::where('tipe_invoice', 'Purchase')
            ->where('status_perjalanan', '!=', 'Diterima');
            
        $pendingShipQuery = \App\Models\Invoice::where('tipe_invoice', 'Sales')
            ->where('status_perjalanan', '!=', 'Terkirim')
            ->where('status_perjalanan', '!=', 'Diterima');

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
        $totalQty = $productQuery->sum('qty');
        $inventoryValue = $productQuery->selectRaw('SUM(qty * harga_beli) as total_value')->first()->total_value ?? 0;

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

            $incoming = StockMutation::where('type', 'IN')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('SUM(qty) as q, SUM(qty * cost_price) as v')
                ->first();

            $outgoing = StockMutation::where('type', 'OUT')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('SUM(qty) as q, SUM(qty * cost_price) as v')
                ->first();
            
            $qtyIncoming[] = (float)($incoming->q ?? 0);
            $qtyOutgoing[] = (float)($outgoing->q ?? 0);
            $valIncoming[] = (float)($incoming->v ?? 0);
            $valOutgoing[] = (float)($outgoing->v ?? 0);
        }

        // Top & Bottom Products (based on OUT mutations)
        $topProducts = Product::where('track_stock', true)
            ->withSum(['stock_mutations as sent_qty' => function($q) {
                $q->where('type', 'OUT');
            }], 'qty')
            ->orderByDesc('sent_qty')
            ->limit(5)
            ->get();

        $bottomProducts = Product::where('track_stock', true)
            ->withSum(['stock_mutations as sent_qty' => function($q) {
                $q->where('type', 'OUT');
            }], 'qty')
            ->orderBy('sent_qty', 'asc')
            ->limit(5)
            ->get();

        return apiResponse(true, 'Statistik stok detail', [
            'overview' => [
                'pending_receive' => (float)$pendingReceive,
                'pending_ship' => (float)$pendingShip,
                'total_qty' => (float)$totalQty,
                'inventory_value' => (float)$inventoryValue,
            ],
            'charts' => [
                'labels' => $months,
                'qty' => [
                    'incoming' => $qtyIncoming,
                    'outgoing' => $qtyOutgoing
                ],
                'value' => [
                    'incoming' => $valIncoming,
                    'outgoing' => $valOutgoing
                ]
            ],
            'top_products' => $topProducts,
            'bottom_products' => $bottomProducts
        ]);
    }

    public function mutations(Request $request)
    {
        $query = StockMutation::with('product');

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        $data = $query->latest()->paginate(10);

        return apiResponse(true, 'Data mutasi stok', $data);
    }

    public function updateStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'qty' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        try {
            $this->stockService->adjustStock($id, $request->qty, $request->notes);
            return apiResponse(true, 'Stok berhasil diperbarui');
        } catch (\Exception $e) {
            return apiResponse(false, 'Gagal memperbarui stok: ' . $e->getMessage(), null, null, 500);
        }
    }
    private function logActivity($tindakan, $tabel, $dataId, $before, $after)
    {
        ActivityLog::create([
            'user_id' => 1, // Default user
            'tindakan' => $tindakan,
            'tabel_terkait' => $tabel,
            'data_id' => $dataId,
            'data_sebelum' => $before,
            'data_sesudah' => $after,
            'ip_address' => request()->ip()
        ]);
    }
}
