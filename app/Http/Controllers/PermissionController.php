<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Prefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PermissionController extends Controller
{
    public function index()
    {
        $this->syncPermissionsFromRoutes();
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
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Label rute berhasil diperbarui',
        ]);
    }

    private function syncPermissionsFromRoutes(): void
    {
        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if (! $name) {
                continue;
            }
            if (str_starts_with($name, 'ignition') || str_starts_with($name, 'sanctum') || str_starts_with($name, 'generated::')) {
                continue;
            }
            $parts = explode('.', $name);
            $prefixName = count($parts) > 1 ? $parts[0] : 'General';
            $prefix = Prefix::firstOrCreate(['name' => ucfirst($prefixName)]);
            if (! Permission::where('name', $name)->exists()) {
                Permission::create([
                    'prefix_id' => $prefix->id,
                    'name' => $name,
                    'action' => $parts[1] ?? 'index',
                    'description' => null,
                ]);
            }
        }
    }
}
