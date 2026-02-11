<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliveryOrderController extends Controller
{
    public function index()
    {
        return view('DeliveryOrder.index');
    }

    public function print($id)
    {
        $do = \App\Models\DeliveryOrder::with([
            'invoices.invoice.mitra', 
            'fleets.fleet', 
            'fleets.driver',
            'office'
        ])
        ->where('office_id', session('active_office_id'))
        ->find($id);

        if (!$do) abort(404);

        return view('DeliveryOrder.print', compact('do'));
    }

    public function track($id)
    {
        $do = \App\Models\DeliveryOrder::with([
            'invoices.invoice.mitra', 
            'fleets.fleet', 
            'fleets.driver',
            'office'
        ])
        ->where('office_id', session('active_office_id'))
        ->find($id);

        if (!$do) abort(404);

        return view('DeliveryOrder.track', compact('do'));
    }
}
