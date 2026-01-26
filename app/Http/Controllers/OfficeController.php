<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        $office = null;
        DB::transaction(function() use ($request, &$office) {
            $office = Office::create($request->all());
            
            // Assign creator as superadmin for this office
            $role = DB::table('roles')->where('name', 'superadmin')->first();
            if ($role) {
                DB::table('user_office_roles')->insert([
                    'user_id' => Auth::id(),
                    'office_id' => $office->id,
                    'role_id' => $role->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kantor berhasil ditambahkan',
                'data' => $office
            ]);
        }

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