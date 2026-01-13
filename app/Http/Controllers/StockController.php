<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockController extends Controller
{
    private $views = 'Stock.';

    public function index()
    {
        return view($this->views.'index');
    }
}
