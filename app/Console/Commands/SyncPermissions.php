<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use App\Models\Permission;
use App\Models\Prefix;

class SyncPermissions extends Command
{
    /**
     * Ini adalah file untuk membuat sinkronisasi pertama kali jika aplikasi ini fresh installation
     * jadi tujuannya untuk mengambil route name yang ada kemudian di daftarkan ke db
     * lakukan php artisan permission:sync
     */
    protected $signature = 'permissions:sync';
    protected $description = 'Sinkronisasi daftar route name ke tabel permissions';

    public function handle()
    {
        $routes = Route::getRoutes();
        $count = 0;

        foreach ($routes as $route) {
            $name = $route->getName();
            
            if ($name && !str_starts_with($name, 'ignition') && !str_starts_with($name, 'sanctum') && !str_starts_with($name, 'generated::')) {
                
                $parts = explode('.', $name);
                $prefixName = count($parts) > 1 ? $parts[0] : 'General';

                $prefix = Prefix::firstOrCreate(['name' => ucfirst($prefixName)]);

                $exists = Permission::where('name', $name)->exists();
                if (!$exists) {
                    Permission::create([
                        'prefix_id' => $prefix->id,
                        'name' => $name,
                        'action' => $parts[1] ?? 'index',
                        'description' => null
                    ]);
                    $count++;
                }
            }
        }

        $this->info("Berhasil menyinkronkan $count rute baru.");
    }
}