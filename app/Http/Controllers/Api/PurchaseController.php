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

        return view($this->views.'Nota.ReceiptNota', compact('payment', 'id'));
    }
}
