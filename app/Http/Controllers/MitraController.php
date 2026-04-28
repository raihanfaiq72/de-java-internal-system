<?php

namespace App\Http\Controllers;

use App\Models\User;

class MitraController extends Controller
{
    private $views = 'Mitra.';

    public function index()
    {
        $officeId = session('active_office_id');
        
        $salesUsers = collect();
        if ($officeId) {
            $salesUsers = User::where('is_sales', true)
                ->whereHas('plots', function ($query) use ($officeId) {
                    $query->where('office_id', $officeId);
                })
                ->orderBy('name')
                ->get();
        }

        return view($this->views.'index', compact('salesUsers'));
    }
}
