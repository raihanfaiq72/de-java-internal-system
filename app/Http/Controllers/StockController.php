<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockController extends Controller
{
    private $views = 'Stock.';

    public function index()
    {
        return view($this->views.'index');
    }

    public function export(Request $request)
    {
        $products = $this->getStockData($request);
        $filename = 'Laporan_Stok_'.Carbon::now()->format('Ymd_His').'.xls';
        
        return response(view($this->views.'export', compact('products')), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function print(Request $request)
    {
        $products = $this->getStockData($request);
        return view($this->views.'print', compact('products'));
    }

    private function getStockData(Request $request)
    {
        // Use active_office_id from session or default if needed
        $officeId = session('active_office_id'); 
        
        $query = Product::with(['category', 'brand'])
            ->where('office_id', $officeId);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_produk', 'like', "%{$request->search}%")
                  ->orWhere('sku_kode', 'like', "%{$request->search}%");
            });
        }

        if ($request->category) {
            $query->where('product_category_id', $request->category);
        }
        
        // Always track stock products
        $query->where('track_stock', true);

        // Location filter
        if ($request->location_id) {
            $locationId = $request->location_id;
             $query->addSelect(['location_qty' => StockMutation::selectRaw('SUM(CASE WHEN type = "IN" THEN qty ELSE -qty END)')
                    ->whereColumn('product_id', 'products.id')
                    ->where('stock_location_id', $locationId)
            ]);
        }

        return $query->orderBy('nama_produk')->get();
    }
}
