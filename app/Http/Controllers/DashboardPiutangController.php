<?php

namespace App\Http\Controllers;

class DashboardPiutangController extends Controller
{
    private $views = 'DashboardPiutang.';

    public function index()
    {
        return view($this->views.'index');
    }
}
