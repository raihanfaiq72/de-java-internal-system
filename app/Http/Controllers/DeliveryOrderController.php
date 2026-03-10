<?php

namespace App\Http\Controllers;

class DeliveryOrderController extends Controller
{
    public function index()
    {
        return view('DeliveryOrder.index');
    }

    public function print($id)
    {
        return view('DeliveryOrder.print', ['doId' => $id]);
    }

    public function track($id)
    {
        return view('DeliveryOrder.track', ['doId' => $id]);
    }
}
