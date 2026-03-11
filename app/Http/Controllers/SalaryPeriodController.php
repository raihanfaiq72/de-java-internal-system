<?php

namespace App\Http\Controllers;

class SalaryPeriodController extends Controller
{
    public function index()
    {
        return view('Salary.periods.index');
    }
}
