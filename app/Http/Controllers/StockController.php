<?php

namespace App\Http\Controllers;

class StockController extends Controller
{
    private $views = 'Stock.';

    public function index()
    {
        return view($this->views.'index');
    }
}
