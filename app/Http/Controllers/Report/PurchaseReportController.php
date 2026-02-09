<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            $query->whereHas('mitra', function($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            });
        }

        // --- 1. Summary Cards ---
        // We clone the query to avoid modifying the base reference
        
        // We need: Total Purchase, Total Paid (to calc AP), Overdue AP.
        
        $statsQuery = clone $query;
        $stats = $statsQuery->selectRaw('
            SUM(total_akhir) as total_purchase,
            SUM((SELECT COALESCE(SUM(jumlah_bayar), 0) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL)) as total_paid,
            SUM(CASE WHEN tgl_jatuh_tempo < ? AND (total_akhir - (SELECT COALESCE(SUM(jumlah_bayar), 0) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL)) > 0 THEN (total_akhir - (SELECT COALESCE(SUM(jumlah_bayar), 0) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL)) ELSE 0 END) as total_overdue
        ', [now()->format('Y-m-d')])->first();

        $totalPurchase = $stats->total_purchase ?? 0;
        $totalPaid = $stats->total_paid ?? 0;
        $totalAP = $totalPurchase - $totalPaid; // AP = Hutang
        $totalOverdue = $stats->total_overdue ?? 0;
        $paymentRatio = $totalPurchase > 0 ? ($totalPaid / $totalPurchase) * 100 : 0;


        // --- 2. Monthly Purchase Trend ---
        // Group by Month of tgl_invoice
        $trendQuery = clone $query;
        $trendData = $trendQuery->selectRaw("DATE_FORMAT(tgl_invoice, '%Y-%m') as month, SUM(total_akhir) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $trendLabels = $trendData->pluck('month')->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'));
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
        
        $topSupplierLabels = $topSuppliers->map(fn($item) => $item->mitra->nama ?? 'Unknown');
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
            $payablesQuery->whereHas('mitra', function($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            });
        }

        // Only unpaid/partial
        // We can check status_pembayaran != 'Paid'
        $payablesQuery->where('status_pembayaran', '!=', 'Paid');
        
        $payables = $payablesQuery->latest('tgl_invoice')->get()->map(function($invoice) {
             $paid = $invoice->payment->sum('jumlah_bayar');
             $balance = $invoice->total_akhir - $paid;
             $invoice->sisa_tagihan = $balance;
             return $invoice;
        })->filter(function($invoice) {
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
}
