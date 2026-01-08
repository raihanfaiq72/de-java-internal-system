<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mitra;
use App\Models\Account;

class PurchaseController extends Controller
{
    private $views = 'Purchase.';

    public function index()
    {
        return view($this->views.'index');
    }

    public function receipt()
    {
        $mitras = Mitra::get();

        return view($this->views . 'receipt', compact('mitras'));
    }
}
