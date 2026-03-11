<?php

namespace App\Http\Controllers;

class ReportController extends Controller
{
    private $views = 'Report.';

    public function salesReport()
    {
        return view($this->views.'sales');
    }
}
