<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mitra;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    private $views = 'Purchase.';

    public function index()
    {
        return view($this->views.'index');
    }

    public function receipt()
    {
        // Ambil Mitra untuk pilihan vendor/supplier
        $mitras = \App\Models\Mitra::whereIn('tipe_mitra', ['Supplier', 'Both'])->get();
        
        // Ambil Akun Kas & Bank
        $accounts = \Illuminate\Support\Facades\DB::table('chart_of_accounts')
                    ->where('is_kas_bank', 1)
                    ->get();

        return view($this->views . 'receipt', compact('mitras', 'accounts'));
    }
}
