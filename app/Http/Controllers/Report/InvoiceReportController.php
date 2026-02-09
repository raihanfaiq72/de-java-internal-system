<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceReportController extends Controller
{
    public function index(Request $request)
    {
        $officeId = session('active_office_id');
        
        // Base Query for Stats & Charts (Sales Invoices Only)
        $query = Invoice::query()
            ->where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId);

        // Apply Date Filter if present
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_invoice', [$request->start_date, $request->end_date]);
        }

        // Apply Search Filter (affects stats too? Usually stats reflect the filtered view)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mitra', function($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            });
        }

        // --- 1. Summary Cards ---
        // We clone the query to avoid modifying the base reference for subsequent usages if needed,
        // but here we might need to aggregate differently.
        
        // For accurate stats including payments, we need to join or subquery payments.
        // Let's use a separate aggregation query for efficiency.
        
        // We need: Total Sales, Total Paid (to calc AR), Overdue AR.
        
        // Get all relevant invoice IDs first to scope payments? 
        // Or just use the query builder with aggregates.
        
        $statsQuery = clone $query;
        $stats = $statsQuery->selectRaw('
            SUM(total_akhir) as total_sales,
            SUM((SELECT COALESCE(SUM(jumlah_bayar), 0) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL)) as total_paid,
            SUM(CASE WHEN tgl_jatuh_tempo < ? AND (total_akhir - (SELECT COALESCE(SUM(jumlah_bayar), 0) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL)) > 0 THEN (total_akhir - (SELECT COALESCE(SUM(jumlah_bayar), 0) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL)) ELSE 0 END) as total_overdue
        ', [now()->format('Y-m-d')])->first();

        $totalSales = $stats->total_sales ?? 0;
        $totalPaid = $stats->total_paid ?? 0;
        $totalAR = $totalSales - $totalPaid;
        $totalOverdue = $stats->total_overdue ?? 0;
        $collectionRatio = $totalSales > 0 ? ($totalPaid / $totalSales) * 100 : 0;


        // --- 2. Monthly Sales Trend ---
        // Group by Month of tgl_invoice
        $trendQuery = clone $query;
        $trendData = $trendQuery->selectRaw("DATE_FORMAT(tgl_invoice, '%Y-%m') as month, SUM(total_akhir) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $trendLabels = $trendData->pluck('month')->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'));
        $trendSeries = $trendData->pluck('total');


        // --- 3. Top 5 Customers ---
        // Group by Mitra
        $topQuery = clone $query;
        $topClients = $topQuery->select('mitra_id', DB::raw('SUM(total_akhir) as total_sales'))
            ->with('mitra:id,nama')
            ->groupBy('mitra_id')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();
        
        $topClientLabels = $topClients->map(fn($item) => $item->mitra->nama ?? 'Unknown');
        $topClientSeries = $topClients->pluck('total_sales');


        // --- 4. Receivables List (Daftar Invoice Terhutang) ---
        // Filter: Sales Invoices with Balance > 0
        // We need to apply the same filters (date/search) PLUS the Balance > 0 check.
        
        $receivablesQuery = Invoice::query()
            ->where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->with(['mitra', 'payment']); // Eager load for display

        // Apply same filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $receivablesQuery->whereBetween('tgl_invoice', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $receivablesQuery->where(function($q) use ($search) {
                 $q->where('nomor_invoice', 'like', "%$search%")
                   ->orWhereHas('mitra', function($m) use ($search) {
                       $m->where('nama', 'like', "%$search%");
                   });
            });
        }

        // Apply Balance > 0 filter
        // Using whereRaw for performance
        $receivablesQuery->whereRaw('(total_akhir - (SELECT COALESCE(SUM(jumlah_bayar), 0) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL)) > 0');
        
        // Order by Due Date (closest first)
        $receivablesQuery->orderBy('tgl_jatuh_tempo', 'asc');

        $invoices = $receivablesQuery->paginate(10)->withQueryString();

        return view('Report.Invoice.index', compact(
            'totalSales', 'totalAR', 'totalOverdue', 'collectionRatio',
            'trendLabels', 'trendSeries',
            'topClientLabels', 'topClientSeries',
            'invoices'
        ));
    }
}
