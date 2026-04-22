<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\COA;
use App\Models\Invoice;
use App\Models\Partner;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    private $views = 'Purchase.';

    public function export(Request $request)
    {
        $query = Invoice::with(['mitra', 'payment'])
            ->where('office_id', session('active_office_id'))
            ->where('tipe_invoice', 'Purchase');

        if ($request->tab_status === 'trash') {
            $query->onlyTrashed();
        } elseif ($request->tab_status === 'archive') {
            $query->where('status_dok', 'Archived');
        }

        if ($request->status_dok) {
            $query->where('status_dok', $request->status_dok);
        }

        if ($request->status_pembayaran) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        if ($request->mitra_id) {
            $query->where('mitra_id', $request->mitra_id);
        }

        if ($request->tgl_invoice) {
            $query->whereDate('tgl_invoice', $request->tgl_invoice);
        }

        if ($request->tgl_jatuh_tempo) {
            $query->whereDate('tgl_jatuh_tempo', $request->tgl_jatuh_tempo);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_invoice', 'LIKE', "%{$request->search}%")
                    ->orWhere('ref_no', 'LIKE', "%{$request->search}%")
                    ->orWhereHas('mitra', function ($qMitra) use ($request) {
                        $qMitra->where('nama', 'LIKE', "%{$request->search}%");
                    });
            });
        }

        $invoices = $query->latest()->get();

        return view($this->views.'export', compact('invoices'));
    }

    public function receipt()
    {
        $mitras = Partner::whereIn('tipe_mitra', ['Supplier', 'Both'])
            ->where('office_id', session('active_office_id'))
            ->where(function($query) {
                $query->where('tipe_mitra', 'Supplier')
                      ->orWhere('tipe_mitra', 'Both');
            })
            ->get();

        $accounts = COA::where('is_kas_bank', true)
            ->get();

        return view($this->views.'receipt', compact('mitras', 'accounts'));
    }

    public function printInvoice($id)
    {
        $invoice = Invoice::with(['mitra', 'items.product'])->find($id);
        if (! $invoice) {
            abort(404);
        }

        if ($invoice->office_id != session('active_office_id')) {
            abort(404);
        }

        return view($this->views.'Nota.PurchaseNota', compact('invoice', 'id'));
    }

    public function printReceipt($id)
    {
        $payment = Payment::with(['invoice.mitra', 'invoice.items.product', 'akun_keuangan'])->find($id);
        if (! $payment) {
            abort(404);
        }

        if ($payment->invoice->office_id != session('active_office_id')) {
            abort(404);
        }

        return view($this->views . 'Nota.ReceiptNota', compact('payment', 'id'));
    }

    public function massPrint(Request $request)
    {
        $salesId = $request->sales_id;
        $period = $request->period;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        // Validate input
        if (!$period) {
            return response()->json(['error' => 'Period is required'], 400);
        }

        if ($period === 'custom' && (!$dateFrom || !$dateTo)) {
            return response()->json(['error' => 'Date range is required for custom period'], 400);
        }

        // Calculate date range based on period
        $now = now();
        switch ($period) {
            case 'this_month':
                $dateFrom = $now->startOfMonth()->format('Y-m-d');
                $dateTo = $now->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $dateFrom = $now->subMonth()->startOfMonth()->format('Y-m-d');
                $dateTo = $now->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_quarter':
                $dateFrom = $now->startOfQuarter()->format('Y-m-d');
                $dateTo = $now->endOfQuarter()->format('Y-m-d');
                break;
            case 'this_year':
                $dateFrom = $now->startOfYear()->format('Y-m-d');
                $dateTo = $now->endOfYear()->format('Y-m-d');
                break;
        }

        // Get staff/purchaser info
        $salesPerson = null;
        if ($salesId != 0) {
            $salesPerson = User::where('id', $salesId)->first();
        }

        // Get purchase invoices with payments using Eloquent
        $invoices = Invoice::with(['mitra', 'payment'])
            ->where('office_id', session('active_office_id'))
            ->where('tipe_invoice', 'Purchase')
            ->where('sales_id', $salesId)
            ->whereDate('tgl_invoice', '>=', $dateFrom)
            ->whereDate('tgl_invoice', '<=', $dateTo)
            ->orderBy('tgl_invoice')
            ->get()
            ->map(function ($invoice) {
                // Transform to match original structure
                $payment = $invoice->payment->first(); // Get first payment
                return (object) [
                    'id' => $invoice->id,
                    'nomor_invoice' => $invoice->nomor_invoice,
                    'tgl_invoice' => $invoice->tgl_invoice,
                    'total_akhir' => $invoice->total_akhir,
                    'mitra_nama' => $invoice->mitra ? $invoice->mitra->nama : null,
                    'payment_date' => $payment ? $payment->tgl_pembayaran : null,
                    'payment_amount' => $payment ? $payment->jumlah_bayar : null,
                    'payment_note' => $payment ? $payment->catatan : null,
                    'no_receipt' => $payment ? $payment->nomor_pembayaran : null,
                ];
            });

        if ($request->check && $invoices->isEmpty()) {
            return response()->json(['error' => 'Tidak ada data pembelian untuk periode ini.'], 400);
        }

        // Calculate totals
        $totalInvoices = $invoices->unique('id')->sum('total_akhir');
        $totalPayments = $invoices->sum('payment_amount');
        $totalRemaining = $totalInvoices - $totalPayments;

        // Group payments by date
        $paymentsByDate = $invoices
            ->whereNotNull('payment_date')
            ->groupBy('payment_date')
            ->map(function ($group) {
                return [
                    'date' => $group->first()->payment_date,
                    'total' => $group->sum('payment_amount'),
                    'customers' => $group->pluck('mitra_nama')->unique(),
                    'receipts' => $group->pluck('no_receipt')->filter(),
                ];
            })
            ->sortBy('date');

        // Get monthly data for charts
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthInvoices = $invoices->unique('id')->filter(function ($inv) use ($i) {
                return date('n', strtotime($inv->tgl_invoice)) == $i;
            });
            $monthlyData[] = $monthInvoices->sum('total_akhir');
        }

        return view($this->views . 'mass-print', compact(
            'salesPerson',
            'period',
            'dateFrom',
            'dateTo',
            'invoices',
            'paymentsByDate',
            'totalInvoices',
            'totalPayments',
            'totalRemaining',
            'monthlyData'
        ));
    }

}
