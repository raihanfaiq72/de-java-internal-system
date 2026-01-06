<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index()
    {
        $offices = Office::all();
        return view('Office.index', compact('offices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:offices,code'
        ]);

        Office::create($request->all());
        return back()->with('success', 'Kantor berhasil ditambahkan');
    }
}