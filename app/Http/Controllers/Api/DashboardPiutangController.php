<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\COA;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\Partner;
use Throwable;

class DashboardPiutangController extends Controller
{
    public function summary()
    {
        try {
            $year = date('Y');
            $officeId = session('active_office_id');

            // Calculate total saldo using Eloquent
            $totalSaldo = JournalDetail::whereHas('coa', function ($query) {
                    $query->where('is_kas_bank', true);
                })
                ->whereHas('journal', function ($query) use ($officeId) {
                    $query->where('office_id', $officeId);
                })
                ->get()
                ->sum(function ($detail) {
                    return $detail->debit - $detail->kredit;
                });

            // Calculate piutang data using Eloquent collections
            $piutangInvoices = Invoice::with(['mitra'])
                ->where('tipe_invoice', 'Sales')
                ->where('office_id', $officeId)
                ->whereHas('mitra', function ($query) {
                    $query->where('is_cash_customer', false);
                })
                ->get();

            $piutangData = (object) [
                'total_nilai' => $piutangInvoices->sum('total_akhir'),
                'count_unpaid' => $piutangInvoices->where('status_pembayaran', 'Unpaid')->count(),
                'count_partial' => $piutangInvoices->where('status_pembayaran', 'Partially Paid')->count(),
                'count_overdue' => $piutangInvoices->filter(function ($invoice) {
                    return $invoice->status_pembayaran === 'Overdue' || 
                           ($invoice->tgl_jatuh_tempo < now() && $invoice->status_pembayaran !== 'Paid');
                })->count()
            ];

            // Calculate utang data using Eloquent collections
            $utangInvoices = Invoice::where('tipe_invoice', 'Purchase')
                ->where('office_id', $officeId)
                ->get();

            $utangData = (object) [
                'total_nilai' => $utangInvoices->sum('total_akhir'),
                'count_unpaid' => $utangInvoices->where('status_pembayaran', 'Unpaid')->count(),
                'count_partial' => $utangInvoices->where('status_pembayaran', 'Partially Paid')->count(),
                'count_overdue' => $utangInvoices->filter(function ($invoice) {
                    return $invoice->status_pembayaran === 'Overdue' || 
                           ($invoice->tgl_jatuh_tempo < now() && $invoice->status_pembayaran !== 'Paid');
                })->count()
            ];

            $monthlyIncome = [];
            $monthlyExpense = [];

            for ($m = 1; $m <= 12; $m++) {
                // Calculate monthly income using Eloquent
                $income = Invoice::where('tipe_invoice', 'Sales')
                    ->where('office_id', $officeId)
                    ->whereYear('tgl_invoice', $year)
                    ->whereMonth('tgl_invoice', $m)
                    ->sum('total_akhir');

                // Calculate monthly expenses using Eloquent
                $expenseTable = Expense::where('office_id', $officeId)
                    ->whereYear('tgl_biaya', $year)
                    ->whereMonth('tgl_biaya', $m)
                    ->sum('jumlah');

                $purchaseInvoice = Invoice::where('tipe_invoice', 'Purchase')
                    ->where('office_id', $officeId)
                    ->whereYear('tgl_invoice', $year)
                    ->whereMonth('tgl_invoice', $m)
                    ->sum('total_akhir');

                $monthlyIncome[] = (float) $income;
                $monthlyExpense[] = (float) ($expenseTable + $purchaseInvoice);
            }

            $totalLabaRugi = array_sum($monthlyIncome) - array_sum($monthlyExpense);

            return response()->json([
                'success' => true,
                'data' => [
                    'totalSaldo' => (float) $totalSaldo,
                    'piutang' => $piutangData,
                    'utang' => $utangData,
                    'monthlyIncome' => $monthlyIncome,
                    'monthlyExpense' => $monthlyExpense,
                    'totalLabaRugi' => (float) $totalLabaRugi,
                ],
            ]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

