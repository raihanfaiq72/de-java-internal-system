<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Office;

class OfficeController extends Controller
{
    public function index()
    {
        $offices = Office::all();

        return apiResponse(true, 'Data kantor/gudang', $offices);
    }
}
