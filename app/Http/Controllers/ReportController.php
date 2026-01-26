<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    private $views = 'Report.';

    public function salesReport()
    {
        $year = date('Y');
        $officeId = session('active_office_id');
        
        // Stats
        $stats = DB::table('invoices')
            ->where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->whereNull('deleted_at')
            ->select(
                DB::raw('SUM(total_akhir) as total_revenue'),
                DB::raw('SUM(CASE WHEN status_pembayaran != "Paid" THEN (total_akhir - COALESCE((SELECT SUM(jumlah_bayar) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL), 0)) ELSE 0 END) as total_ar'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN tgl_jatuh_tempo < CURDATE() AND status_pembayaran != "Paid" THEN (total_akhir - COALESCE((SELECT SUM(jumlah_bayar) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL), 0)) ELSE 0 END) as total_overdue')
            )->first();

        // Monthly Data
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = DB::table('invoices')
                ->where('tipe_invoice', 'Sales')
                ->where('office_id', $officeId)
                ->whereYear('tgl_invoice', $year)
                ->whereMonth('tgl_invoice', $m)
                ->whereNull('deleted_at')
                ->sum('total_akhir');
        }

        // Top Clients
        $topClients = DB::table('invoices')
            ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->where('invoices.tipe_invoice', 'Sales')
            ->where('invoices.office_id', $officeId)
            ->whereNull('invoices.deleted_at')
            ->select('mitras.nama', DB::raw('SUM(total_akhir) as value'))
            ->groupBy('mitras.id', 'mitras.nama')
            ->orderByDesc('value')
            ->limit(5)
            ->get();

        return view($this->views . 'sales', compact('stats', 'monthlyData', 'topClients'));
    }

    public function purchaseReport()
    {
        $year = date('Y');
        $officeId = session('active_office_id');
        
        // Stats
        $stats = DB::table('invoices')
            ->where('tipe_invoice', 'Purchase')
            ->where('office_id', $officeId)
            ->whereNull('deleted_at')
            ->select(
                DB::raw('SUM(total_akhir) as total_spending'),
                DB::raw('SUM(CASE WHEN status_pembayaran != "Paid" THEN (total_akhir - COALESCE((SELECT SUM(jumlah_bayar) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL), 0)) ELSE 0 END) as total_ap'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN tgl_jatuh_tempo < CURDATE() AND status_pembayaran != "Paid" THEN (total_akhir - COALESCE((SELECT SUM(jumlah_bayar) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL), 0)) ELSE 0 END) as total_overdue')
            )->first();

        // Monthly Data
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = DB::table('invoices')
                ->where('tipe_invoice', 'Purchase')
                ->where('office_id', $officeId)
                ->whereYear('tgl_invoice', $year)
                ->whereMonth('tgl_invoice', $m)
                ->whereNull('deleted_at')
                ->sum('total_akhir');
        }

        // Top Suppliers
        $topSuppliers = DB::table('invoices')
            ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->where('invoices.tipe_invoice', 'Purchase')
            ->where('invoices.office_id', $officeId)
            ->whereNull('invoices.deleted_at')
            ->select('mitras.nama', DB::raw('SUM(total_akhir) as value'))
            ->groupBy('mitras.id', 'mitras.nama')
            ->orderByDesc('value')
            ->limit(5)
            ->get();

        return view($this->views . 'purchase', compact('stats', 'monthlyData', 'topSuppliers'));
    }

    public function stockReport()
    {
        $officeId = session('active_office_id');
        $products = DB::table('products')
            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
            ->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->where('products.office_id', $officeId)
            ->whereNull('products.deleted_at')
            ->select(
                'products.id',
                'products.nama_produk',
                'products.sku_kode',
                'products.harga_beli',
                'units.nama_unit',
                'product_categories.nama_kategori',
                DB::raw('(SELECT SUM(qty) FROM stock_mutations WHERE product_id = products.id) as current_stock')
            )
            ->get();

        $stats = (object)[
            'total_items' => $products->count(),
            'low_stock' => $products->where('current_stock', '<=', 10)->count(),
            'total_valuation' => $products->sum(fn($p) => ($p->current_stock ?? 0) * $p->harga_beli)
        ];

        return view($this->views . 'stock', compact('products', 'stats'));
    }

    public function arAging()
    {
        $officeId = session('active_office_id');
        $invoices = DB::table('invoices')
            ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->where('tipe_invoice', 'Sales')
            ->where('invoices.office_id', $officeId)
            ->where('status_pembayaran', '!=', 'Paid')
            ->whereNull('invoices.deleted_at')
            ->select(
                'invoices.id',
                'invoices.nomor_invoice',
                'mitras.nama as client_name',
                'invoices.tgl_invoice',
                'invoices.tgl_jatuh_tempo',
                'invoices.total_akhir',
                DB::raw('(invoices.total_akhir - COALESCE((SELECT SUM(jumlah_bayar) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL), 0)) as balance'),
                DB::raw('DATEDIFF(CURDATE(), invoices.tgl_jatuh_tempo) as days_overdue')
            )
            ->get();

        $agingBuckets = [
            'current' => $invoices->where('days_overdue', '<=', 0)->sum('balance'),
            '1_30' => $invoices->whereBetween('days_overdue', [1, 30])->sum('balance'),
            '31_60' => $invoices->whereBetween('days_overdue', [31, 60])->sum('balance'),
            '61_90' => $invoices->whereBetween('days_overdue', [61, 90])->sum('balance'),
            '90_plus' => $invoices->where('days_overdue', '>', 90)->sum('balance'),
        ];

        return view($this->views . 'ar-aging', compact('invoices', 'agingBuckets'));
    }

    public function balanceSheet()
    {
        $date = date('Y-m-d');
        $officeId = session('active_office_id');
        
        $accounts = DB::table('chart_of_accounts')
            ->select('id', 'kode_akun', 'nama_akun', 'is_kas_bank')
            ->get()
            ->map(function($acc) use ($officeId) {
                // Calculation of balance from journal entries
                $balance = DB::table('journal_details')
                    ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
                    ->where('journal_details.akun_id', $acc->id)
                    ->where('journals.office_id', $officeId)
                    ->whereNull('journal_details.deleted_at')
                    ->whereNull('journals.deleted_at')
                    ->select(DB::raw('SUM(debit) - SUM(kredit) as bal'))
                    ->first()->bal ?? 0;
                
                $acc->balance = $balance;
                $acc->type = substr($acc->kode_akun, 0, 1);
                return $acc;
            });

        $aktiva = $accounts->filter(fn($a) => $a->type == '1');
        $kewajiban = $accounts->filter(fn($a) => ($a->type == '2'))->map(fn($a) => (object)['nama_akun' => $a->nama_akun, 'kode_akun' => $a->kode_akun, 'balance' => abs($a->balance)]);
        $modal = $accounts->filter(fn($a) => ($a->type == '3'))->map(fn($a) => (object)['nama_akun' => $a->nama_akun, 'kode_akun' => $a->kode_akun, 'balance' => abs($a->balance)]);

        // Calculate current year profit
        $revenue = DB::table('invoices')
            ->where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->whereYear('tgl_invoice', date('Y'))
            ->whereNull('deleted_at')
            ->sum('total_akhir');

        $cogs = DB::table('invoices')
            ->where('tipe_invoice', 'Purchase')
            ->where('office_id', $officeId)
            ->whereYear('tgl_invoice', date('Y'))
            ->whereNull('deleted_at')
            ->sum('total_akhir');

        $expenses = DB::table('expenses')
            ->where('office_id', $officeId)
            ->whereYear('tgl_biaya', date('Y'))
            ->whereNull('deleted_at')
            ->sum('jumlah');

        $netProfit = $revenue - ($cogs + $expenses);

        return view($this->views . 'balance-sheet', compact('aktiva', 'kewajiban', 'modal', 'netProfit', 'date'));
    }

    public function profitAndLoss()
    {
        $year = date('Y');
        $officeId = session('active_office_id');
        
        $revenue = DB::table('invoices')
            ->where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->whereYear('tgl_invoice', $year)
            ->whereNull('deleted_at')
            ->sum('total_akhir');

        $cogs = DB::table('invoices')
            ->where('tipe_invoice', 'Purchase')
            ->where('office_id', $officeId)
            ->whereYear('tgl_invoice', $year)
            ->whereNull('deleted_at')
            ->sum('total_akhir');

        $expenses = DB::table('expenses')
            ->where('office_id', $officeId)
            ->whereYear('tgl_biaya', $year)
            ->whereNull('deleted_at')
            ->select('nama_biaya', DB::raw('SUM(jumlah) as total'))
            ->groupBy('nama_biaya')
            ->get();

        $totalExpenses = $expenses->sum('total');
        $grossProfit = $revenue - $cogs;
        $netIncome = $grossProfit - $totalExpenses;

        return view($this->views . 'profit-loss', compact('revenue', 'cogs', 'expenses', 'totalExpenses', 'grossProfit', 'netIncome', 'year'));
    }
}
