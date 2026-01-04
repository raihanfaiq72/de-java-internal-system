<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Roles;

class RoleController extends Controller
{
    public function index()
    {
        return Roles::with('permissions')->get();
    }

    public function store(Request $request)
    {
        $role = Roles::create($request->only('name', 'description'));

        if($request->permissions) {
            $role->permissions()->sync($request->permissions);
        }

        return response()->json($role->load('permissions'));
    }

    public function show($id)
    {
        return Roles::with('permissions')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $role = Roles::findOrFail($id);
        $role->update($request->only('name', 'description'));

        if($request->permissions !== null) {
            $role->permissions()->sync($request->permissions);
        }

        return response()->json($role->load('permissions'));
    }

    public function destroy($id)
    {
        $role = Roles::findOrFail($id);
        $role->delete();
        return response()->json(['message' => 'Role deleted']);
    }
}
