<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMutation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    private $views = 'Stock.';

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
        $officeId = session('active_office_id');

        $query = Product::with(['category', 'brand'])
            ->where('office_id', $officeId);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_produk', 'like', "%{$request->search}%")
                    ->orWhere('sku_kode', 'like', "%{$request->search}%");
            });
        }

        if ($request->category) {
            $query->where('product_category_id', $request->category);
        }

        $query->where('track_stock', true);

        if ($request->location_id) {
            $locationId = $request->location_id;
            $query->addSelect([
                'location_qty' => StockMutation::selectRaw('SUM(CASE WHEN type = "IN" THEN qty ELSE -qty END)')
                    ->whereColumn('product_id', 'products.id')
                    ->where('stock_location_id', $locationId),
            ]);
        }

        return $query->orderBy('nama_produk')->get();
    }
}
