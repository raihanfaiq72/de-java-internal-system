<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FleetController extends Controller
{
    public function index()
    {
        return view('Fleet.index');
    }
}
