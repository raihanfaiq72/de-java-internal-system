<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ChartOfAccountSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\SuperAdminSeeder;
use Database\Seeders\AccountingLogSeeder;
use Database\Seeders\MitraSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\TransactionSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(ChartOfAccountSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(SuperAdminSeeder::class);
        $this->call(AccountingLogSeeder::class);
        $this->call(MitraSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(TransactionSeeder::class);
    }
}
