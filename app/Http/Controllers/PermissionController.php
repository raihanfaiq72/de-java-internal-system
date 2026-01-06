<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Prefix;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $prefixes = Prefix::with('permissions')->orderBy('name', 'asc')->get();
        return view('Permission.index', compact('prefixes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Label rute berhasil diperbarui'
        ]);
    }
}