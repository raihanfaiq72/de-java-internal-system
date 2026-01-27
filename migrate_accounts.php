<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\COA;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\DB;

echo "Migrating COA (is_kas_bank=true) to FinancialAccounts...\n";

// 1. Get existing Kas/Bank COAs
$coas = COA::where('is_kas_bank', true)->get();

if ($coas->isEmpty()) {
    echo "No existing Kas/Bank accounts found in COA. Checking specific codes (1101, 1201)...\n";
    $coas = COA::whereIn('kode_akun', ['1101', '1201', '1251'])->get();
}

foreach ($coas as $coa) {
    echo "Migrating COA: {$coa->kode_akun} - {$coa->nama_akun}...\n";
    
    // Determine Type
    $type = 'Cash';
    if (stripos($coa->nama_akun, 'Bank') !== false || $coa->kode_akun == '1201') $type = 'Bank';
    if (stripos($coa->nama_akun, 'Card') !== false || $coa->kode_akun == '1251') $type = 'Corporate Card';

    // Create FinancialAccount
    $newAccount = FinancialAccount::create([
        'office_id' => $coa->office_id ?? 1,
        'name' => $coa->nama_akun,
        'code' => $coa->kode_akun,
        'type' => $type,
        'description' => 'Migrated from COA',
        'bank_name' => $coa->bank_name,
        'bank_account_number' => $coa->bank_account_number,
        'bank_account_name' => $coa->bank_account_name,
        'bank_branch' => $coa->bank_branch,
        'bank_city' => $coa->bank_city,
        'currency' => $coa->currency ?? 'IDR',
    ]);
    
    echo "  -> Created FinancialAccount ID: {$newAccount->id}\n";
    
    // Update Transactions
    $updatedFrom = FinancialTransaction::where('from_account_id', $coa->id)->update(['from_account_id' => $newAccount->id]);
    $updatedTo = FinancialTransaction::where('to_account_id', $coa->id)->update(['to_account_id' => $newAccount->id]);
    
    echo "  -> Updated Transactions: From ($updatedFrom), To ($updatedTo)\n";
    
    // Optionally remove is_kas_bank flag from COA or keep it?
    // User wants standalone. Maybe delete COA? No, might break other things.
    // Just leave it.
}

echo "Migration Complete.\n";
