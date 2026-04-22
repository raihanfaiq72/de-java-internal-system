<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\Roles;
use App\Models\UserOfficeRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOfficeController extends Controller
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
            'code' => 'required|unique:offices,code',
        ]);

        $office = null;
        \DB::transaction(function () use ($request, &$office) {
            $office = Office::create($request->all());

            $role = Roles::where('name', 'superadmin')->first();
            if ($role) {
                UserOfficeRole::create([
                    'user_id' => Auth::id(),
                    'office_id' => $office->id,
                    'role_id' => $role->id,
                ]);
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kantor berhasil ditambahkan',
                'data' => $office,
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
            'code' => 'required|unique:offices,code,'.$id,
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
