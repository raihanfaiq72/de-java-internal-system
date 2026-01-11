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

    public function show($id)
    {
        $office = Office::findOrFail($id);
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'data' => $office]);
        }
        return abort(404);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:offices,code,' . $id
        ]);

        Office::findOrFail($id)->update($request->all());
        return back()->with('success', 'Kantor berhasil diperbarui');
    }

    public function destroy($id)
    {
        Office::findOrFail($id)->delete();
        return back()->with('success', 'Kantor berhasil dihapus');
    }
}