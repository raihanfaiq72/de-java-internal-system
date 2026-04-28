<?php

namespace App\Http\Controllers;

use App\Models\User;

class SalesController extends Controller
{
    private $views = 'Sales.';

    public function show($id)
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

        return view($this->views.'detail', compact('id', 'users'));
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

    public function printReceipt($id)
    {
        return view($this->views.'Nota.ReceiptNota', compact('id'));
    }
}
