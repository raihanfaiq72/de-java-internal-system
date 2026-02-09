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
use Database\Seeders\PaymentSeeder;
use Database\Seeders\InvoiceItemTaxSeeder;
use Database\Seeders\UsersSeeder;
use Database\Seeders\BrandSeeder;
use Database\Seeders\ProductCategorySeeder;
use Database\Seeders\TaxSeeder;
use Database\Seeders\StockLocationSeeder;
use Database\Seeders\ExpenseCategorySeeder;
use Database\Seeders\FleetSeeder;
use Database\Seeders\FinancialAccountSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Auth & Base Structure
        $this->call(UsersSeeder::class); // Creates Users, Offices, Roles
        
        // 2. Finance Master Data
        $this->call(ChartOfAccountSeeder::class);
        $this->call(TaxSeeder::class);
        $this->call(FinancialAccountSeeder::class);
        $this->call(ExpenseCategorySeeder::class);

        // 3. Partners
        $this->call(MitraSeeder::class);

        // 4. Inventory Master Data
        $this->call(StockLocationSeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(ProductCategorySeeder::class);
        
        // 5. Logistics
        $this->call(FleetSeeder::class);

        // 6. Products (Depends on Brand, Category, Mitra)
        $this->call(ProductSeeder::class);

        // 7. Transactions (Optional - Demo Data)
        // Uncomment if you want transaction data
        $this->call(TransactionSeeder::class);
        $this->call(PaymentSeeder::class);
        
        // Others (Keep if valid)
        // $this->call(PermissionSeeder::class); // UsersSeeder handles permissions? Yes
        // $this->call(SuperAdminSeeder::class); // UsersSeeder handles admin? Yes
        // $this->call(AccountingLogSeeder::class);
        // $this->call(InvoiceItemTaxSeeder::class);
    }
}