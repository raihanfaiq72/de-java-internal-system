<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesController extends Controller
{
    private $views = 'Sales.';

    public function index()
    {
        return view($this->views.'index');
    }
}
