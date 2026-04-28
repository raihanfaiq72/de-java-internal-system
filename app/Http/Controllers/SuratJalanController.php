<?php

namespace App\Http\Controllers;

use App\Models\User;

class SuratJalanController extends Controller
{
    private $views = 'SuratJalan.';

    public function show($id)
    {
        $officeId = session('active_office_id');
        $users = collect();
        if ($officeId) {
            $users = User::where('is_sales', true)
                ->whereHas('plots', function ($query) use ($officeId) {
                    $query->where('office_id', $officeId);
                })
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        return view($this->views.'detail', compact('id', 'users'));
    }

    public function printSuratJalan($id)
    {
        return view($this->views.'Nota.SuratJalanNota', compact('id'));
    }
}
