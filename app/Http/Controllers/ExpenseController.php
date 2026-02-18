<?php

namespace App\Http\Controllers;

use App\Models\COA;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $officeId = session('active_office_id');

        // Akun Keuangan: Kas / Bank (Standalone FinancialAccount)
        $financialAccounts = \App\Models\FinancialAccount::where('office_id', $officeId)
            ->orderBy('code')
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
