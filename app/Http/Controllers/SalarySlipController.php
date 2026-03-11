<?php

namespace App\Http\Controllers;

class SalarySlipController extends Controller
{
    public function show()
    {
        return view('Salary.slips.show');
    }
}
