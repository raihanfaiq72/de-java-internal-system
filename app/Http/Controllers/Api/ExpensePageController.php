<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\COA;
use App\Models\ExpenseCategory;

class ExpensePageController extends Controller
{
    public function index()
    {
        $officeId = session('active_office_id');

        $financialAccounts = \App\Models\FinancialAccount::where('office_id', $officeId)
            ->orderBy('code')
            ->get();

        $expenseAccounts = COA::where('office_id', $officeId)
            ->orderBy('kode_akun')
            ->get();

        $categories = ExpenseCategory::where('office_id', $officeId)
            ->orderBy('name')
            ->get();

        return view('Expense.index', compact('financialAccounts', 'expenseAccounts', 'categories'));
    }
}
