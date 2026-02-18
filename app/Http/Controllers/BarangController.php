<?php

namespace App\Http\Controllers;

class BarangController extends Controller
{
    private $views = 'Barang.';

    public function index()
    {
        return view($this->views.'index');
    }
}
