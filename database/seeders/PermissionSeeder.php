<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Prefix;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $routes = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter()
            ->values();

        $routesGrouped = $routes->map(function ($name) {
            $parts = explode('.', $name);

            return [
                'prefix' => $parts[0],
                'action' => $parts[1] ?? '',
                'name' => $name,
            ];
        })->groupBy('prefix');

        foreach ($routesGrouped as $prefixName => $perms) {
            $prefix = Prefix::firstOrCreate(['name' => $prefixName]);
            foreach ($perms as $perm) {
                Permission::firstOrCreate([
                    'prefix_id' => $prefix->id,
                    'action' => $perm['action'],
                    'name' => $perm['name'],
                ]);
            }
        }
    }
}
