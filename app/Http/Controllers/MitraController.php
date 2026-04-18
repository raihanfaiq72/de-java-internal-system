<?php

namespace App\Http\Controllers;

use App\Models\User;

class MitraController extends Controller
{
    private $views = 'Mitra.';

    public function index()
    {
        $salesUsers = User::where('is_sales', true)->get();
        return view($this->views.'index', compact('salesUsers'));
    }
}
