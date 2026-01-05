<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    private $views = 'Purchase.';

    public function index()
    {
        return view($this->views.'index');
    }
}
