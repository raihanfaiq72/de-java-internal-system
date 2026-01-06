<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Super Administrator',
            'username' => 'superadmin', 
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'),
            'created_at' => now(),
        ]);

        $staffId = DB::table('users')->insertGetId([
            'name' => 'Anton Staff',
            'username' => 'anton',
            'email' => 'anton@mail.com',
            'password' => Hash::make('password'),
            'created_at' => now(),
        ]);

        $officePusat = DB::table('offices')->insertGetId([
            'name' => 'Kantor Pusat Semarang',
            'code' => 'KCU-SMG',
            'created_at' => now(),
        ]);

        $officeCabang = DB::table('offices')->insertGetId([
            'name' => 'Kantor Cabang Bandung',
            'code' => 'KCP-BDG',
            'created_at' => now(),
        ]);

        $routes = Route::getRoutes();
        foreach ($routes as $route) {
            $routeName = $route->getName();
            
            if ($routeName && !str_starts_with($routeName, 'ignition') && !str_starts_with($routeName, 'sanctum')) {
                
                $parts = explode('.', $routeName);
                $prefixName = count($parts) > 1 ? ucfirst($parts[0]) : 'General';

                $prefixId = DB::table('prefixes')->updateOrInsert(
                    ['name' => $prefixName],
                    ['created_at' => now()]
                );
                
                $prefixId = DB::table('prefixes')->where('name', $prefixName)->value('id');

                $customName = str_replace(['.', '-'], ' ', $routeName);
                $customName = ucwords($customName);

                DB::table('permissions')->updateOrInsert(
                    ['name' => $routeName],
                    [
                        'prefix_id' => $prefixId,
                        'action' => $parts[1] ?? 'access',
                        'description' => 'Akses ' . $customName,
                        'created_at' => now(),
                    ]
                );
            }
        }

        $roleAdminId = DB::table('roles')->insertGetId([
            'name' => 'Superadmin',
            'description' => 'Memiliki akses penuh ke seluruh sistem',
            'created_at' => now(),
        ]);

        $roleStaffId = DB::table('roles')->insertGetId([
            'name' => 'Staff Penjualan',
            'description' => 'Hanya akses modul transaksi penjualan',
            'created_at' => now(),
        ]);

        $allPermissions = DB::table('permissions')->pluck('id');
        foreach ($allPermissions as $pId) {
            DB::table('role_permissions')->insert([
                'role_id' => $roleAdminId,
                'permission_id' => $pId,
            ]);
        }

        $staffPerms = DB::table('permissions')
            ->where('name', 'like', 'dashboard%')
            ->orWhere('name', 'like', 'sales%')
            ->pluck('id');
            
        foreach ($staffPerms as $pId) {
            DB::table('role_permissions')->insert([
                'role_id' => $roleStaffId,
                'permission_id' => $pId,
            ]);
        }

        DB::table('user_office_roles')->insert([
            'user_id' => $adminId,
            'office_id' => $officePusat,
            'role_id' => $roleAdminId,
            'created_at' => now(),
        ]);

        DB::table('user_office_roles')->insert([
            'user_id' => $staffId,
            'office_id' => $officeCabang,
            'role_id' => $roleStaffId,
            'created_at' => now(),
        ]);

        DB::table('user_office_roles')->insert([
            'user_id' => $staffId,
            'office_id' => $officeCabang,
            'role_id' => $roleAdminId,
            'created_at' => now(),
        ]);
    }
}
