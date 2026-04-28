<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    private $views = 'Sales.';

    public function index()
    {
        $officeId = session('active_office_id');

        $users = collect();
        if ($officeId) {
            $users = User::where('is_sales', true)
                ->whereHas('plots', function ($query) use ($officeId) {
                    $query->where('office_id', $officeId);
                })
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        return view($this->views.'index', compact('users'));
    }

    public function export(Request $request)
    {
        $query = Invoice::with(['mitra', 'payment'])
            ->where('office_id', session('active_office_id'))
            ->where('tipe_invoice', 'Sales');

        if ($request->tab_status === 'trash') {
            $query->onlyTrashed();
        } elseif ($request->tab_status === 'archive') {
            $query->where('status_dok', 'Archived');
        }

        if ($request->exclude_delivered) {
            $query->whereDoesntHave('deliveryOrderInvoices', function ($q) {
                $q->whereNotIn('delivery_status', ['failed', 'rejected', 'returned']);
            });
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

    public function massPrint(Request $request)
    {
        $salesId = $request->sales_id;
        $period = $request->period;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        if (! $period) {
            return response()->json(['error' => 'Sales ID and Period are required'], 400);
        }

        if ($period === 'custom' && (! $dateFrom || ! $dateTo)) {
            return response()->json(['error' => 'Date from and Date to are required for custom period'], 400);
        }

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
                $quarter = ceil($now->month / 3);
                $dateFrom = $now->startOfQuarter()->format('Y-m-d');
                $dateTo = $now->endOfQuarter()->format('Y-m-d');
                break;
            case 'this_year':
                $dateFrom = $now->startOfYear()->format('Y-m-d');
                $dateTo = $now->endOfYear()->format('Y-m-d');
                break;
        }

        $salesPerson = null;
        if ($salesId != 0) {
            $salesPerson = User::where('id', $salesId)->first();
        }

        $invoices = Invoice::with(['mitra', 'payment'])
            ->where('office_id', session('active_office_id'))
            ->where('tipe_invoice', 'Sales')
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

        $totalInvoices = $invoices->sum('total_akhir');
        $totalPayments = $invoices->sum('payment_amount');
        $totalRemaining = $totalInvoices - $totalPayments;

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

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthInvoices = $invoices->filter(function ($inv) use ($i) {
                return date('n', strtotime($inv->tgl_invoice)) == $i;
            });
            $monthlyData[] = $monthInvoices->sum('total_akhir');
        }

        return view($this->views.'mass-print', compact(
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

    public function approval()
    {
        return view($this->views.'approval');
    }

    public function approvalDetail($id)
    {
        $invoice = Invoice::where('office_id', session('active_office_id'))->find($id);
        if (! $invoice) {
            abort(404);
        }

        return view($this->views.'approval-detail', compact('invoice', 'id'));
    }

    public function approvalOverdue()
    {
        return view($this->views.'approval-overdue');
    }

    public function approvalOverdueDetail($id)
    {
        $invoice = Invoice::where('office_id', session('active_office_id'))->find($id);
        if (! $invoice) {
            abort(404);
        }

        return view($this->views.'approval-overdue-detail', compact('invoice', 'id'));
    }
}
