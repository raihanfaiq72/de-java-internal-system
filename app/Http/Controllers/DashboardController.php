<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $views = 'Dashboard.';

    public function index()
    {
        return view($this->views.'index');
    }
}
