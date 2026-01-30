<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mitra;
use App\Models\Invoice;
use App\Models\Partner;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    private $views = 'Purchase.';

    public function index()
    {
        return view($this->views . 'index');
    }

    public function show($id)
    {
        return view($this->views . 'detail', compact('id'));
    }

    public function receipt()
    {
        // Ambil Mitra untuk pilihan vendor/supplier
        $mitras = Partner::whereIn('tipe_mitra', ['Supplier', 'Both'])
            ->where('office_id', session('active_office_id'))
            ->get();

        // Ambil Akun Kas & Bank
        $accounts = \Illuminate\Support\Facades\DB::table('chart_of_accounts')
            ->where('is_kas_bank', 1)
            ->get();

        return view($this->views . 'receipt', compact('mitras', 'accounts'));
    }

    public function printInvoice($id)
    {
        $invoice = Invoice::with(['mitra', 'items.product'])->find($id);
        if (!$invoice) abort(404);

        if ($invoice->office_id != session('active_office_id')) {
            abort(404);
        }

        return view($this->views . 'Nota.PurchaseNota', compact('invoice', 'id'));
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