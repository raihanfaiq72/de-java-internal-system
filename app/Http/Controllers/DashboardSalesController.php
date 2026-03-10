<?php

namespace App\Http\Controllers;

class DashboardSalesController extends Controller
{
    private $views = 'DashboardSales.';

    public function index()
    {
        return view($this->views.'index');
    }

    public function detail($id)
    {
        return response()->json(['success' => false, 'message' => 'Use API endpoint'], 410);
    }
}
