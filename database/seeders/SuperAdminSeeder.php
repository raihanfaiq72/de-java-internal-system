<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $roleId = DB::table('roles')->updateOrInsert(
            ['name' => 'superadmin'],
            [
                'description' => 'Ini role Super Admin',
                'created_at' => $now,
                'updated_at' => $now
            ]
        );

        $role = DB::table('roles')->where('name', 'superadmin')->first();

        $permissions = [
            ['prefix_id'=>1,'action'=>'csrf-cookie','name'=>'sanctum.csrf-cookie'],
            ['prefix_id'=>2,'action'=>'index','name'=>'roles.index'],
            ['prefix_id'=>2,'action'=>'store','name'=>'roles.store'],
            ['prefix_id'=>2,'action'=>'show','name'=>'roles.show'],
            ['prefix_id'=>2,'action'=>'update','name'=>'roles.update'],
            ['prefix_id'=>2,'action'=>'destroy','name'=>'roles.destroy'],
            ['prefix_id'=>3,'action'=>'index','name'=>'permissions.index'],
            ['prefix_id'=>3,'action'=>'store','name'=>'permissions.store'],
            ['prefix_id'=>3,'action'=>'show','name'=>'permissions.show'],
            ['prefix_id'=>3,'action'=>'update','name'=>'permissions.update'],
            ['prefix_id'=>3,'action'=>'destroy','name'=>'permissions.destroy'],
            ['prefix_id'=>4,'action'=>'index','name'=>'users.index'],
            ['prefix_id'=>5,'action'=>'local','name'=>'storage.local'],
        ];

        $permissionIds = [];
        foreach ($permissions as $perm) {
            $id = DB::table('permissions')->updateOrInsert(
                [
                    'prefix_id' => $perm['prefix_id'],
                    'name' => $perm['name']
                ],
                [
                    'action' => $perm['action'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            );

            $permission = DB::table('permissions')
                            ->where('name', $perm['name'])
                            ->first();
            $permissionIds[] = $permission->id;
        }

        foreach ($permissionIds as $pid) {
            DB::table('role_permissions')->updateOrInsert(
                [
                    'role_id' => $role->id,
                    'permission_id' => $pid
                ],
                [
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            );
        }

        $this->command->info('Seeder SuperAdmin: role & permissions berhasil dibuat!');
    }
}
