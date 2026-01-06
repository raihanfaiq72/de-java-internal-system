<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarangController extends Controller
{
    private $views = 'Barang.';

    public function index()
    {
        return view($this->views.'index');
    }
}
