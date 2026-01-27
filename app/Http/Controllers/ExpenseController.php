<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\COA;
use App\Models\ExpenseCategory;

class ExpenseController extends Controller
{
    public function index()
    {
        $officeId = session('active_office_id');
        
        // Akun Keuangan: Kas / Bank (is_kas_bank = true)
        $financialAccounts = COA::where('office_id', $officeId)
            ->where('is_kas_bank', true)
            ->orderBy('kode_akun')
            ->get();

        // Akun Beban: Semua COA (User request: sementara tampilkan semua COA nya)
        // Usually expenses are 'Beban' (5xxx or 6xxx), but user asked for ALL.
        $expenseAccounts = COA::where('office_id', $officeId)
            ->orderBy('kode_akun')
            ->get();

        // Master Categories for dropdown
        $categories = ExpenseCategory::where('office_id', $officeId)
            ->orderBy('name')
            ->get();

        return view('Expense.index', compact('financialAccounts', 'expenseAccounts', 'categories'));
    }
}
