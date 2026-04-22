<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;

class PurchaseReportController extends Controller
{
    public function index(Request $request)
    {
        $officeId = session('active_office_id');

        // Base Query for Stats & Charts (Purchase Invoices Only)
        $query = Invoice::query()
            ->where('tipe_invoice', 'Purchase')
            ->where('office_id', $officeId);

        // Apply Date Filter if present
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_invoice', [$request->start_date, $request->end_date]);
        }

        // Apply Search Filter (Search by Supplier Name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mitra', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            });
        }

        // --- 1. Summary Cards ---
        $statsQuery = clone $query;
        $totalPurchase = $statsQuery->sum('total_akhir');
        
        $totalPaid = Payment::whereHas('invoice', function ($q) use ($officeId) {
            $q->where('office_id', $officeId)->where('tipe_invoice', 'Purchase');
        })->sum('jumlah_bayar');

        $totalOverdue = (clone $query)
            ->where('tgl_jatuh_tempo', '<', now()->format('Y-m-d'))
            ->where('status_pembayaran', '!=', 'Paid')
            ->withSum(['payment as paid_amount'], 'jumlah_bayar')
            ->get()
            ->sum(fn($inv) => max(0, $inv->total_akhir - ($inv->paid_amount ?? 0)));

        $totalAP = max(0, $totalPurchase - $totalPaid);
        $paymentRatio = $totalPurchase > 0 ? ($totalPaid / $totalPurchase) * 100 : 0;

        // --- 2. Monthly Purchase Trend ---
        // Group by Month of tgl_invoice
        $trendQuery = clone $query;
        $trendData = $trendQuery->selectRaw("DATE_FORMAT(tgl_invoice, '%Y-%m') as month, SUM(total_akhir) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $trendLabels = $trendData->pluck('month')->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'));
        $trendSeries = $trendData->pluck('total');

        // --- 3. Top 5 Suppliers ---
        // Group by Mitra (Supplier)
        $topQuery = clone $query;
        $topSuppliers = $topQuery->select('mitra_id', DB::raw('SUM(total_akhir) as total_purchase'))
            ->with('mitra:id,nama')
            ->groupBy('mitra_id')
            ->orderByDesc('total_purchase')
            ->limit(5)
            ->get();

        $topSupplierLabels = $topSuppliers->map(fn ($item) => $item->mitra->nama ?? 'Unknown');
        $topSupplierSeries = $topSuppliers->pluck('total_purchase');

        // --- 4. Payables List (Daftar Invoice Belum Lunas / Hutang) ---
        // Filter: Purchase Invoices with Balance > 0

        $payablesQuery = Invoice::query()
            ->where('tipe_invoice', 'Purchase')
            ->where('office_id', $officeId)
            ->with(['mitra', 'payment']); // Eager load for display

        // Apply same filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $payablesQuery->whereBetween('tgl_invoice', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $payablesQuery->whereHas('mitra', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            });
        }

        // Only unpaid/partial
        $payablesQuery->where('status_pembayaran', '!=', 'Paid')
            ->withSum(['payment as paid_amount'], 'jumlah_bayar');

        $payables = $payablesQuery->latest('tgl_invoice')->get()->map(function ($invoice) {
            $invoice->sisa_tagihan = max(0, $invoice->total_akhir - ($invoice->paid_amount ?? 0));
            return $invoice;
        })->filter(function ($invoice) {
            return $invoice->sisa_tagihan > 0;
        });

        return view('Report.Purchase.index', compact(
            'totalPurchase',
            'totalAP',
            'totalOverdue',
            'paymentRatio',
            'trendLabels',
            'trendSeries',
            'topSupplierLabels',
            'topSupplierSeries',
            'payables'
        ));
    }

    public function export(Request $request)
    {
        $officeId = session('active_office_id');
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $search = $request->search;

        // --- 4. Payables List (Daftar Invoice Belum Lunas / Hutang) ---
        $payablesQuery = Invoice::query()
            ->where('tipe_invoice', 'Purchase')
            ->where('office_id', $officeId)
            ->with(['mitra', 'payment']);

        if ($startDate && $endDate) {
            $payablesQuery->whereBetween('tgl_invoice', [$startDate, $endDate]);
        }

        if ($search) {
            $payablesQuery->whereHas('mitra', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            });
        }

        $payablesQuery->where('status_pembayaran', '!=', 'Paid')
            ->withSum(['payment as paid_amount'], 'jumlah_bayar');

        $payables = $payablesQuery->latest('tgl_invoice')->get()->map(function ($invoice) {
            $invoice->sisa_tagihan = max(0, $invoice->total_akhir - ($invoice->paid_amount ?? 0));
            return $invoice;
        })->filter(function ($invoice) {
            return $invoice->sisa_tagihan > 0;
        });

        return view('Report.Purchase.export_payables', compact('payables', 'startDate', 'endDate'));
    }
}
