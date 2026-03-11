<?php

namespace App\Http\Controllers;

class PurchaseController extends Controller
{
    private $views = 'Purchase.';

    public function index()
    {
        return view($this->views.'index');
    }

    public function show($id)
    {
        return view($this->views.'detail', compact('id'));
    }

    public function showReceipt($id)
    {
        return view($this->views.'receipt-detail', compact('id'));
    }
}
