<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;

class SalesController extends Controller
{
    private $views = 'Sales.';

    public function index()
    {
        return view($this->views.'index');
    }

    public function receipt()
    {
        // Ambil Mitra untuk pilihan pelanggan
        $mitras = \App\Models\Mitra::whereIn('tipe_mitra', ['Client', 'Both'])
            ->where('office_id', session('active_office_id'))
            ->get();
        
        // Ambil Akun Kas & Bank (Berdasarkan is_kas_bank yang ada di skema SQL kamu)
        $accounts = \Illuminate\Support\Facades\DB::table('chart_of_accounts')
                    ->where('is_kas_bank', 1)
                    ->get();

        return view($this->views.'receipt', compact('mitras', 'accounts'));
    }

    public function printInvoice($id)
    {
        $invoice = Invoice::with(['mitra', 'items.product'])->find($id);
        if (!$invoice) abort(404);

        if ($invoice->office_id != session('active_office_id')) {
            abort(404);
        }

        return view($this->views . 'Nota.SalesNota', compact('invoice'));
    }

    public function printReceipt($id)
    {
        $payment = Payment::with(['invoice.mitra', 'invoice.items.product', 'akun_keuangan'])->find($id);
        if (!$payment) abort(404);

        if ($payment->invoice->office_id != session('active_office_id')) {
            abort(404);
        }

        return view($this->views . 'Nota.ReceiptNota', compact('payment'));
    }
}

