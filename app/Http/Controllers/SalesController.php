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

    public function show($id)
    {
        return view($this->views . 'detail', compact('id'));
    }

    public function receipt()
    {
        return view($this->views.'receipt');
    }

    public function printInvoice($id)
    {
        return view($this->views . 'Nota.SalesNota', compact('id'));
    }

    public function printReceipt($id)
    {
        return view($this->views . 'Nota.ReceiptNota', compact('id'));
    }
}
