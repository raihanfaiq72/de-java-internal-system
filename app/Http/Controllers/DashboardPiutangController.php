<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardPiutangController extends Controller
{
    private $views = 'DashboardPiutang.';

    public function index()
    {
        $year = date('Y');
        $officeId = session('active_office_id');

        // 1. Total Saldo Aktif (Dari COA yang merupakan Kas/Bank)
        // Menghitung selisih debit dan kredit di journal_details
        $totalSaldo = DB::table('journal_details')
            ->join('chart_of_accounts', 'journal_details.akun_id', '=', 'chart_of_accounts.id')
            ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
            ->where('chart_of_accounts.is_kas_bank', true)
            ->where('journals.office_id', $officeId)
            ->whereNull('journals.deleted_at')
            ->select(DB::raw('SUM(journal_details.debit) - SUM(journal_details.kredit) as balance'))
            ->first()->balance ?? 0;

        // 2. Piutang Usaha (Invoice Penjualan)
        $piutangData = DB::table('invoices')
            ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->where('invoices.tipe_invoice', 'Sales')
            ->where('invoices.office_id', $officeId)
            ->whereNull('invoices.deleted_at')
            ->where('mitras.is_cash_customer', false)
            ->select(
                DB::raw('SUM(invoices.total_akhir) as total_nilai'),
                DB::raw("COUNT(CASE WHEN invoices.status_pembayaran = 'Unpaid' THEN 1 END) as count_unpaid"),
                DB::raw("COUNT(CASE WHEN invoices.status_pembayaran = 'Partially Paid' THEN 1 END) as count_partial"),
                DB::raw("COUNT(CASE WHEN invoices.status_pembayaran = 'Overdue' OR (invoices.tgl_jatuh_tempo < NOW() AND invoices.status_pembayaran != 'Paid') THEN 1 END) as count_overdue")
            )->first();

        // 3. Utang Usaha (Invoice Pembelian)
        $utangData = DB::table('invoices')
            ->where('tipe_invoice', 'Purchase')
            ->where('office_id', $officeId)
            ->whereNull('deleted_at')
            ->select(
                DB::raw('SUM(total_akhir) as total_nilai'),
                DB::raw("COUNT(CASE WHEN status_pembayaran = 'Unpaid' THEN 1 END) as count_unpaid"),
                DB::raw("COUNT(CASE WHEN status_pembayaran = 'Partially Paid' THEN 1 END) as count_partial"),
                DB::raw("COUNT(CASE WHEN status_pembayaran = 'Overdue' OR (tgl_jatuh_tempo < NOW() AND status_pembayaran != 'Paid') THEN 1 END) as count_overdue")
            )->first();

        // 4. Data Grafik Laba Rugi (Bulanan 2026)
        $monthlyIncome = [];
        $monthlyExpense = [];

        for ($m = 1; $m <= 12; $m++) {
            // Pendapatan dari Sales Invoice
            $income = DB::table('invoices')
                ->where('tipe_invoice', 'Sales')
                ->where('office_id', $officeId)
                ->whereYear('tgl_invoice', $year)
                ->whereMonth('tgl_invoice', $m)
                ->sum('total_akhir');
            
            // Pengeluaran dari Expense + Purchase Invoice
            $expenseTable = DB::table('expenses')
                ->where('office_id', $officeId)
                ->whereYear('tgl_biaya', $year)
                ->whereMonth('tgl_biaya', $m)
                ->sum('jumlah');
                
            $purchaseInvoice = DB::table('invoices')
                ->where('tipe_invoice', 'Purchase')
                ->where('office_id', $officeId)
                ->whereYear('tgl_invoice', $year)
                ->whereMonth('tgl_invoice', $m)
                ->sum('total_akhir');

            $monthlyIncome[] = $income;
            $monthlyExpense[] = $expenseTable + $purchaseInvoice;
        }

        $totalLabaRugi = array_sum($monthlyIncome) - array_sum($monthlyExpense);

        return view($this->views.'index', compact(
            'totalSaldo', 
            'piutangData', 
            'utangData', 
            'monthlyIncome', 
            'monthlyExpense',
            'totalLabaRugi'
        ));
    }
}