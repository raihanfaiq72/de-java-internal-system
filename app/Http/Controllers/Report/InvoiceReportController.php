<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceReportController extends Controller
{
    public function index(Request $request)
    {
        // --- Tab 1: Rekap Umum (Charts) ---

        // 1. Line Chart: Pemasukan (Sales Payments) vs Pengeluaran (Purchase Payments) per bulan (Last 12 months)
        $months = collect([]);
        $incomes = collect([]);
        $expenses = collect([]);

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->translatedFormat('M Y');
            $year = $date->year;
            $month = $date->month;

            $months->push($monthLabel);

            // Income: Payments for Sales Invoices
            $income = Payment::whereHas('invoice', function ($q) {
                $q->where('office_id', session('active_office_id'))
                  ->where('tipe_invoice', 'Sales');
            })
            ->whereMonth('tgl_pembayaran', $month)
            ->whereYear('tgl_pembayaran', $year)
            ->sum('jumlah_bayar');

            $incomes->push($income);

            // Expense: Payments for Purchase Invoices
            $expense = Payment::whereHas('invoice', function ($q) {
                $q->where('office_id', session('active_office_id'))
                  ->where('tipe_invoice', 'Purchase');
            })
            ->whereMonth('tgl_pembayaran', $month)
            ->whereYear('tgl_pembayaran', $year)
            ->sum('jumlah_bayar');

            $expenses->push($expense);
        }

        // 2. Pie Chart: Komposisi Piutang Penjualan (Top 5 Mitra with highest Unpaid Sales)
        // Piutang = Total Akhir - Total Paid
        // We need to fetch all Unpaid/Partial Sales Invoices, group by Mitra, and sum the outstanding.
        // This can be heavy, so let's optimize.
        
        $receivables = Invoice::where('office_id', session('active_office_id'))
            ->where('tipe_invoice', 'Sales')
            ->where('status_pembayaran', '!=', 'Paid') // Optimization
            ->with(['payment'])
            ->get()
            ->groupBy('mitra_id')
            ->map(function ($invoices) {
                $mitraName = $invoices->first()->mitra->nama ?? 'Unknown';
                $totalDebt = 0;
                foreach ($invoices as $inv) {
                    $paid = $inv->payment->sum('jumlah_bayar');
                    $totalDebt += ($inv->total_akhir - $paid);
                }
                return [
                    'mitra' => $mitraName,
                    'amount' => $totalDebt
                ];
            })
            ->sortByDesc('amount')
            ->take(5);
        
        $pieLabels = $receivables->pluck('mitra');
        $pieSeries = $receivables->pluck('amount');


        // --- Tab 2: Rekap Pembayaran (Table) ---
        
        $query = Payment::with(['invoice.mitra', 'akun_keuangan'])
            ->whereHas('invoice', function($q) {
                $q->where('office_id', session('active_office_id'));
            });

        // Filters
        if ($request->filled('mitra_id')) {
            $query->whereHas('invoice', function($q) use ($request) {
                $q->where('mitra_id', $request->mitra_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%$search%")
                  ->orWhereHas('invoice', function($qInv) use ($search) {
                      $qInv->where('nomor_invoice', 'like', "%$search%")
                           ->orWhereHas('mitra', function($qMitra) use ($search) {
                               $qMitra->where('nama', 'like', "%$search%")
                                      ->orWhere('nomor_mitra', 'like', "%$search%");
                           });
                  });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_pembayaran', [$request->start_date, $request->end_date]);
        }

        $payments = $query->latest('tgl_pembayaran')->paginate(10)->withQueryString();

        // Data for Filters
        $mitras = Partner::where('office_id', session('active_office_id'))->orderBy('nama')->get();

        return view('Report.Invoice.index', compact(
            'months', 'incomes', 'expenses', 
            'pieLabels', 'pieSeries',
            'payments', 'mitras'
        ));
    }
}
