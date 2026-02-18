<?php

namespace App\Http\Controllers;

use App\Models\Prefix;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Roles::withCount('permissions')->get();

        return view('Role.index', compact('roles'));
    }

    public function create()
    {
        $prefixes = Prefix::with('permissions')->get();

        return view('Role.create', compact('prefixes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $role = Roles::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $role->permissions()->sync($request->permissions);
        });

        return redirect()->route('roles.index')->with('success', 'Role berhasil dibuat');
    }

    public function show($id)
    {
        $role = Roles::with('permissions')->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $role,
                'permissions' => $role->permissions->pluck('id')->toArray(),
            ]);
        }

        return redirect()->route('roles.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$id,
            'permissions' => 'required|array',
        ]);

        DB::transaction(function () use ($request, $id) {
            $role = Roles::findOrFail($id);
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $role->permissions()->sync($request->permissions);
        });

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui');
    }

    public function destroy($id)
    {
        Roles::findOrFail($id)->delete();

        return back()->with('success', 'Role berhasil dihapus');
    }
}
