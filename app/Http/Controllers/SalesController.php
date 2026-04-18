<?php

namespace App\Http\Controllers;

class SalesController extends Controller
{
    private $views = 'Sales.';

    public function show($id)
    {
        return view($this->views.'detail', compact('id'));
    }

    public function receipt()
    {
        return view($this->views.'receipt');
    }

    public function showReceipt($id)
    {
        return view($this->views.'receipt-detail', compact('id'));
    }

    public function printInvoice($id)
    {
        return view($this->views.'Nota.SalesNota', compact('id'));
    }

    public function bulkPrintInvoice(\Illuminate\Http\Request $request)
    {
        $ids = explode(',', $request->ids);
        return view($this->views . 'Nota.SalesBulkNota', compact('ids'));
    }

    public function printReceipt($id)
    {
        return view($this->views.'Nota.ReceiptNota', compact('id'));
    }
}
