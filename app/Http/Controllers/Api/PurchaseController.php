<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Partner;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $accounts = DB::table('chart_of_accounts')
            ->where('is_kas_bank', 1)
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
            $salesPerson = DB::table('users')->where('id', $salesId)->first();
        }

        // Get purchase invoices with payments
        $invoices = DB::table('invoices as i')
            ->leftJoin('mitras as m', 'i.mitra_id', '=', 'm.id')
            ->leftJoin('payments as p', 'i.id', '=', 'p.invoice_id')
            ->where('i.office_id', session('active_office_id'))
            ->where('i.tipe_invoice', 'Purchase')
            ->where('i.sales_id', $salesId)
            ->whereDate('i.tgl_invoice', '>=', $dateFrom)
            ->whereDate('i.tgl_invoice', '<=', $dateTo)
            ->select(
                'i.id',
                'i.nomor_invoice',
                'i.tgl_invoice',
                'i.total_akhir',
                'm.nama as mitra_nama',
                'p.tgl_pembayaran as payment_date',
                'p.jumlah_bayar as payment_amount',
                'p.catatan as payment_note',
                'p.nomor_pembayaran as no_receipt'
            )
            ->orderBy('i.tgl_invoice')
            ->get();

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

    public function bulkPrintInvoice(Request $request)
    {
        $ids = explode(',', $request->ids);
        return view($this->views . 'Nota.PurchaseBulkNota', compact('ids'));
    }
}
