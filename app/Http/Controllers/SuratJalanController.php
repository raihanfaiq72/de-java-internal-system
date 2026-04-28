<?php

namespace App\Http\Controllers;

class SuratJalanController extends Controller
{
    private $views = 'SuratJalan.';

    public function show($id)
    {
        return view($this->views . 'detail', compact('id'));
    }

    public function printSuratJalan($id)
    {
        return view($this->views . 'Nota.SuratJalanNota', compact('id'));
    }
}
