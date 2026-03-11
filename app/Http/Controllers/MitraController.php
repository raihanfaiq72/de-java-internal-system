<?php

namespace App\Http\Controllers;

class MitraController extends Controller
{
    private $views = 'Mitra.';

    public function index()
    {
        return view($this->views.'index');
    }
}
