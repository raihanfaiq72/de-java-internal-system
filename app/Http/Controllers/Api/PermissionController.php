<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        return Permission::with('prefix')->get();
    }

    public function store(Request $request)
    {
        $permission = Permission::create($request->only('prefix_id','action','name','description'));
        return response()->json($permission->load('prefix'));
    }

    public function show($id)
    {
        return Permission::with('prefix')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->update($request->only('prefix_id','action','name','description'));
        return response()->json($permission->load('prefix'));
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return response()->json(['message'=>'Permission deleted']);
    }
}
