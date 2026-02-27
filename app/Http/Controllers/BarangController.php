<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    private $views = 'Barang.';

    public function index()
    {
        return view($this->views.'index');
    }

    public function export(Request $request)
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
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->get();

        return view($this->views.'export', compact('products'));
    }
}
