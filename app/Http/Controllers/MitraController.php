<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MitraController extends Controller
{
    private $views = 'Mitra.';

    public function index()
    {
        return view($this->views.'index');
    }
}
