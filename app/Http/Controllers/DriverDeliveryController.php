<?php

namespace App\Http\Controllers;

class DriverDeliveryController extends Controller
{
    public function index()
    {
        return view('Driver.index');
    }

    public function show($id)
    {
        return view('Driver.show', ['deliveryOrderId' => $id]);
    }
}
