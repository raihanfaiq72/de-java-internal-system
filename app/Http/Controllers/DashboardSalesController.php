<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardSalesController extends Controller
{
    private $views = 'DashboardSales.';

    public function index(Request $request)
    {
        // Gunakan hari ini secara dinamis
        $today = now()->format('Y-m-d');
        
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $mitraId = $request->input('mitra_id');
        $status = $request->input('status');

        $query = DB::table('invoices')
            ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->where('invoices.tipe_invoice', 'Sales')
            ->whereBetween('invoices.tgl_invoice', [$startDate, $endDate])
            ->whereNull('invoices.deleted_at');

        // Filter Mitra
        if ($mitraId) {
            $query->where('invoices.mitra_id', $mitraId);
        }

        // Filter Status: Tambahkan logika "Bukan PAID"
        if ($status) {
            if ($status === 'NOT_PAID') {
                $query->where('invoices.status_pembayaran', '!=', 'Paid');
            } else {
                $query->where('invoices.status_pembayaran', $status);
            }
        }

        // Ambil Ringkasan & Rata-rata Aging menggunakan SQL (Biar Akurat)
        $summary = (clone $query)->select(
            DB::raw('SUM(total_akhir) as total_sales'),
            DB::raw('SUM(CASE WHEN status_pembayaran != "Paid" THEN total_akhir ELSE 0 END) as total_piutang'),
            // Hitung rata-rata umur nota hanya untuk yang belum lunas
            DB::raw('ROUND(AVG(CASE WHEN status_pembayaran != "Paid" THEN DATEDIFF(NOW(), tgl_invoice) ELSE NULL END)) as avg_aging')
        )->first();

        // Ambil Data Tabel dengan Pagination
        $invoices = $query->select(
            'invoices.*',
            'mitras.nama as nama_pelanggan',
            DB::raw('DATEDIFF(NOW(), invoices.tgl_invoice) as umur_nota')
        )
        ->orderBy('invoices.tgl_invoice', 'desc')
        ->paginate(10)
        ->withQueryString();

        // Ambil SEMUA Mitra (Client & Both) agar PT Lazuardi muncul
        $listMitra = DB::table('mitras')
            ->whereIn('tipe_mitra', ['Client', 'Both'])
            ->orderBy('nama', 'asc')
            ->get();

        return view($this->views . 'index', compact('summary', 'invoices', 'listMitra', 'startDate', 'endDate'));
    }

    public function detail($id)
    {
        $items = DB::table('invoice_items')
            ->leftJoin('products', 'invoice_items.produk_id', '=', 'products.id')
            ->where('invoice_id', $id)
            ->select('invoice_items.*', 'products.nama_produk', 'products.sku_kode')
            ->get();

        return response()->json($items);
    }
}