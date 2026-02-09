<?php

namespace App\Http\Controllers;

use App\Models\COA;
use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogsActivity;

class FinanceController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $office_id = session('active_office_id');
        
        // Fetch Financial Accounts from new table
        $accounts = FinancialAccount::where('office_id', $office_id)->get();
            
        // Calculate Balances and Stats
        foreach ($accounts as $account) {
            $account->balance = $this->calculateBalance($account->id, $office_id);
            $account->income_lain = $this->calculateIncomeLain($account->id, $office_id);
            $account->expense_lain = $this->calculateExpenseLain($account->id, $office_id);
        }

        // For dropdowns in modals
        $all_accounts = FinancialAccount::where('office_id', $office_id)->get();
        
        // For Parent Account selection (Keep COA for reference if needed, but not for creation)
        $parent_accounts = COA::where('office_id', $office_id)
            ->orderBy('kode_akun')
            ->get();

        // Fetch Transactions for the Transaksi tab
        $transactions = FinancialTransaction::with(['fromAccount', 'toAccount'])
            ->where('office_id', $office_id)
            ->latest()
            ->paginate(10);

        return view('Finance.index', compact('accounts', 'all_accounts', 'parent_accounts', 'transactions'));
    }

    private function calculateBalance($accountId, $officeId)
    {
        // 1. Payments (Income from Invoices)
        $income = Payment::where('office_id', $officeId)
            ->where('akun_keuangan_id', $accountId)
            ->sum('jumlah_bayar');

        // 2. Expenses (Operational Expenses)
        $expense = Expense::where('office_id', $officeId)
            ->where('akun_keuangan_id', $accountId)
            ->sum('jumlah');

        // 3. Transfers In (Debit)
        $transferIn = FinancialTransaction::where('office_id', $officeId)
            ->where('to_account_id', $accountId)
            ->where('type', 'transfer')
            ->where('status', 'posted')
            ->sum('amount');

        // 4. Transfers Out (Credit)
        $transferOut = FinancialTransaction::where('office_id', $officeId)
            ->where('from_account_id', $accountId)
            ->where('type', 'transfer')
            ->where('status', 'posted')
            ->sum('amount');

        // 5. Other Income (Debit)
        $otherIncome = FinancialTransaction::where('office_id', $officeId)
            ->where('to_account_id', $accountId)
            ->where('type', 'income')
            ->where('status', 'posted')
            ->sum('amount');

        // 6. Other Expense (Credit)
        $otherExpense = FinancialTransaction::where('office_id', $officeId)
            ->where('from_account_id', $accountId)
            ->where('type', 'expense')
            ->where('status', 'posted')
            ->sum('amount');

        return ($income + $transferIn + $otherIncome) - ($expense + $transferOut + $otherExpense);
    }

    private function calculateIncomeLain($accountId, $officeId)
    {
        return FinancialTransaction::where('office_id', $officeId)
            ->where('to_account_id', $accountId)
            ->where('type', 'income')
            ->where('status', 'posted')
            ->sum('amount');
    }

    private function calculateExpenseLain($accountId, $officeId)
    {
        return FinancialTransaction::where('office_id', $officeId)
            ->where('from_account_id', $accountId)
            ->where('type', 'expense')
            ->where('status', 'posted')
            ->sum('amount');
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'type' => 'required|in:transfer,income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:posted,draft',
            'from_account_id' => 'required_if:type,transfer,expense|exists:financial_accounts,id',
            'to_account_id' => 'required_if:type,transfer,income|exists:financial_accounts,id',
        ]);

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['office_id'] = session('active_office_id');

            // Handle File Upload if exists (lampiran)
            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                $path = $file->store('financial_transactions', 'public');
                $input['lampiran'] = $path;
            }

            $transaction = FinancialTransaction::create($input);

            // Log Activity
            $this->logActivity('create', 'financial_transactions', $transaction->id, null, $transaction->toArray());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Store New Financial Account (Kas/Bank) - NOW USES FinancialAccount Table
    public function storeAccount(Request $request)
    {
        // Validation based on input
        $request->validate([
            'tipe_akun' => 'required|string',
            'mode' => 'required|in:new,existing',
        ]);
        
        try {
            DB::beginTransaction();
            $input = $request->all();
            $input['office_id'] = session('active_office_id');
            // Mapping fields
            $input['type'] = $request->tipe_akun;
            $input['name'] = $request->nama_akun;
            $input['code'] = $request->kode_akun;
            
            $account = null;

            if ($request->mode == 'new') {
                $request->validate([
                    // Remove parent_id validation as it's standalone now
                    'kode_akun' => 'required|unique:financial_accounts,code',
                    'nama_akun' => 'required|string',
                ]);
                
                // Create New Financial Account
                $account = FinancialAccount::create($input);

            } else {
                // Update Existing Account
                $request->validate([
                    'existing_coa_id' => 'required|exists:financial_accounts,id',
                ]);
                
                $account = FinancialAccount::find($request->existing_coa_id);
                // Update fields
                $account->type = $request->tipe_akun;
                $account->name = $request->nama_akun;
                $account->bank_name = $request->bank_name;
                $account->bank_account_number = $request->bank_account_number;
                $account->bank_account_name = $request->bank_account_name;
                $account->bank_branch = $request->bank_branch;
                $account->bank_city = $request->bank_city;
                $account->currency = $request->currency ?? 'IDR';
                $account->save();
            }
            
             // Log Activity
            $this->logActivity(
                $request->mode == 'new' ? 'create' : 'update',
                'financial_accounts',
                $account->id,
                null,
                $account->toArray()
            );
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Akun Keuangan berhasil disimpan'
            ]);
        } catch (\Exception $e) {
             DB::rollBack();
             return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper to generate next code
    public function getNextCode(Request $request) {
        $type = $request->type; // Cash, Bank, Corporate Card
        
        // Define prefix logic based on type (User's implicit logic)
        // Cash -> 11xx
        // Bank -> 12xx
        // Corporate Card -> 12xx (maybe?)

        // Simple logic: Find max code for that type or just increment from base.
        // Or simply find the last code in financial_accounts and increment.
        
        // Let's look for existing accounts of same type to guess the pattern
        $lastAccount = FinancialAccount::where('type', $type)->orderBy('code', 'desc')->first();
        
        if ($lastAccount && $lastAccount->code) {
             // Increment
             if (preg_match('/(\d+)$/', $lastAccount->code, $matches)) {
                $number = $matches[1];
                $nextNumber = str_pad($number + 1, strlen($number), '0', STR_PAD_LEFT);
                $nextCode = preg_replace('/(\d+)$/', $nextNumber, $lastAccount->code);
                return response()->json(['code' => $nextCode]);
            }
        }
        
        // Default if no previous account
        if ($type == 'Cash') return response()->json(['code' => '1101']);
        if ($type == 'Bank') return response()->json(['code' => '1201']);
        if ($type == 'Corporate Card') return response()->json(['code' => '1251']);
        
        return response()->json(['code' => '']);
    }

    public function destroyAccount($id)
    {
        try {
            $account = FinancialAccount::findOrFail($id);
            
            // Check if there are transactions
            $exists = FinancialTransaction::where('from_account_id', $id)
                        ->orWhere('to_account_id', $id)
                        ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun yang memiliki transaksi.'
                ], 400);
            }

            $account->delete();

            // Log Activity
            $this->logActivity('delete', 'financial_accounts', $id);

            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}