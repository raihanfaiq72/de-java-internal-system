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

    public function receipt()
    {
        // Ambil Mitra untuk pilihan pelanggan
        $mitras = \App\Models\Mitra::whereIn('tipe_mitra', ['Client', 'Both'])->get();
        
        // Ambil Akun Kas & Bank (Berdasarkan is_kas_bank yang ada di skema SQL kamu)
        $accounts = \Illuminate\Support\Facades\DB::table('chart_of_accounts')
                    ->where('is_kas_bank', 1)
                    ->get();

        return view($this->views.'receipt', compact('mitras', 'accounts'));
    }
}

