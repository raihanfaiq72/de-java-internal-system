<?php

namespace App\Http\Controllers;

use App\Models\COA;
use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    use LogsActivity;

    public function exportExcel(Request $request)
    {
        $officeId = session('active_office_id');
        $accounts = FinancialAccount::where('office_id', $officeId)->orderBy('code')->get();
        $accountId = $request->account_id ?: ($accounts->first()->id ?? null);
        
        if (!$accountId) {
            return redirect()->back()->with('error', 'Akun tidak ditemukan');
        }

        $account = FinancialAccount::find($accountId);
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $filename = 'Laporan_Kas_Kecil_' . date('YmdHis') . '.xls';

        return response()->streamDownload(function () use ($officeId, $accountId, $start, $end) {
            $startStr = $start->toDateString();
            $endStr = $end->toDateString();

            // 1. Calculate Opening Balance (Optimized Queries)
            // Use where() instead of whereDate() for better index usage
            $openingIncome = Payment::where('office_id', $officeId)
                ->where('akun_keuangan_id', $accountId)
                ->where('tgl_pembayaran', '<', $startStr)
                ->sum('jumlah_bayar');

            $openingExpense = Expense::where('office_id', $officeId)
                ->where('akun_keuangan_id', $accountId)
                ->where('tgl_biaya', '<', $startStr)
                ->sum('jumlah');

            $openingTransferIn = FinancialTransaction::where('office_id', $officeId)
                ->where('to_account_id', $accountId)
                ->where('status', 'posted')
                ->whereIn('type', ['transfer', 'income'])
                ->where('transaction_date', '<', $startStr)
                ->sum('amount');

            $openingTransferOut = FinancialTransaction::where('office_id', $officeId)
                ->where('from_account_id', $accountId)
                ->where('status', 'posted')
                ->whereIn('type', ['transfer', 'expense'])
                ->where('transaction_date', '<', $startStr)
                ->sum('amount');

            $openingDeliveryCost = DB::table('delivery_order_invoices')
                ->where('chart_of_accounts_id', $accountId)
                ->where('created_at', '<', $start) // DateTime comparison
                ->sum('total_cost');

            $balance = $openingIncome + $openingTransferIn - ($openingExpense + $openingTransferOut + $openingDeliveryCost);

            // Output HTML Header
            echo '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>';
            echo '<table border="1" style="border-collapse: collapse; width: 100%;">';
            echo '<thead>';
            echo '<tr><th></th><th colspan="7" style="text-align: center; font-weight: bold; font-size: 16px; border: 1px solid black;">LAPORAN KAS KECIL CV DE JAVANESE AUTO PARTS</th></tr>';
            echo '<tr><th></th><th colspan="7" style="text-align: center; font-weight: bold; font-size: 14px; border: 1px solid black;">PERIODE ' . $start->translatedFormat('d F Y') . ' - ' . $end->translatedFormat('d F Y') . '</th></tr>';
            echo '<tr>
                    <th></th>
                    <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 50px;">No</th>
                    <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 100px;">Tanggal</th>
                    <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 150px;">Nama Akun</th>
                    <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 300px;">Keterangan</th>
                    <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 120px;">Debit</th>
                    <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 120px;">Kredit</th>
                    <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 120px;">Total Saldo</th>
                  </tr>';
            echo '</thead><tbody>';

            // Output Opening Balance Row
            echo '<tr>
                    <td></td>
                    <td style="border: 1px solid black; text-align: center;">1</td>
                    <td style="border: 1px solid black; text-align: center;">' . $start->format('M d, y') . '</td>
                    <td style="border: 1px solid black; text-align: center;">SALDO</td>
                    <td style="border: 1px solid black;">SISA SALDO</td>
                    <td style="border: 1px solid black; text-align: right;">' . ($balance > 0 ? 'Rp ' . number_format($balance, 0, ',', '.') : '') . '</td>
                    <td style="border: 1px solid black; text-align: right;">' . ($balance < 0 ? 'Rp ' . number_format(abs($balance), 0, ',', '.') : '') . '</td>
                    <td style="border: 1px solid black; text-align: right;">Rp ' . number_format($balance, 0, ',', '.') . '</td>
                  </tr>';

            // 2. Fetch and Merge Data
            $rows = [];

            // Payments
            $payments = Payment::with(['invoice' => function($q) {
                    $q->select('id', 'mitra_id', 'nomor_invoice');
                }, 'invoice.mitra:id,nama'])
                ->select('tgl_pembayaran', 'jumlah_bayar', 'invoice_id', 'nomor_pembayaran')
                ->where('office_id', $officeId)
                ->where('akun_keuangan_id', $accountId)
                ->whereBetween('tgl_pembayaran', [$startStr, $endStr])
                ->get();

            foreach ($payments as $p) {
                $rows[] = [
                    'date' => $p->tgl_pembayaran,
                    'account_name' => 'PENDAPATAN',
                    'description' => ($p->invoice?->mitra?->nama ?? 'Pembayaran') . ' - ' . ($p->invoice?->nomor_invoice ?? $p->nomor_pembayaran),
                    'debit' => $p->jumlah_bayar,
                    'credit' => 0
                ];
            }

            // Expenses
            $expenses = Expense::select('tgl_biaya', 'nama_biaya', 'jumlah')
                ->where('office_id', $officeId)
                ->where('akun_keuangan_id', $accountId)
                ->whereBetween('tgl_biaya', [$startStr, $endStr])
                ->get();

            foreach ($expenses as $e) {
                $rows[] = [
                    'date' => $e->tgl_biaya,
                    'account_name' => 'BEBAN',
                    'description' => $e->nama_biaya,
                    'debit' => 0,
                    'credit' => $e->jumlah
                ];
            }

            // Financial Transactions
            $transactions = FinancialTransaction::select('transaction_date', 'type', 'amount', 'description', 'to_account_id', 'from_account_id')
                ->where('office_id', $officeId)
                ->where('status', 'posted')
                ->where(function($q) use ($accountId) {
                    $q->where('from_account_id', $accountId)
                      ->orWhere('to_account_id', $accountId);
                })
                ->whereBetween('transaction_date', [$startStr, $endStr])
                ->get();

            foreach ($transactions as $t) {
                if ($t->to_account_id == $accountId && in_array($t->type, ['transfer', 'income'])) {
                    $rows[] = [
                        'date' => $t->transaction_date,
                        'account_name' => 'DEPOSIT',
                        'description' => $t->description ?? 'Penerimaan',
                        'debit' => $t->amount,
                        'credit' => 0
                    ];
                } elseif ($t->from_account_id == $accountId && in_array($t->type, ['transfer', 'expense'])) {
                    $rows[] = [
                        'date' => $t->transaction_date,
                        'account_name' => 'BEBAN',
                        'description' => $t->description ?? 'Pengeluaran',
                        'debit' => 0,
                        'credit' => $t->amount
                    ];
                }
            }

            // Delivery Costs
            $deliveryCosts = DB::table('delivery_order_invoices')
                ->select('created_at', 'total_cost')
                ->where('chart_of_accounts_id', $accountId)
                ->whereBetween('created_at', [$start, $end])
                ->get();

            foreach ($deliveryCosts as $dc) {
                $rows[] = [
                    'date' => substr($dc->created_at, 0, 10),
                    'account_name' => 'BEBAN PENGIRIMAN',
                    'description' => 'Biaya Pengiriman DO',
                    'debit' => 0,
                    'credit' => $dc->total_cost
                ];
            }

            // Sort rows by date
            usort($rows, function ($a, $b) {
                return strcmp($a['date'], $b['date']);
            });

            // Output Rows
            $totalDebit = 0;
            $totalCredit = 0;
            $iteration = 2;

            foreach ($rows as $row) {
                $balance += $row['debit'] - $row['credit'];
                $totalDebit += $row['debit'];
                $totalCredit += $row['credit'];

                echo '<tr>
                        <td></td>
                        <td style="border: 1px solid black; text-align: center;">' . $iteration++ . '</td>
                        <td style="border: 1px solid black; text-align: center;">' . Carbon::parse($row['date'])->format('M d, y') . '</td>
                        <td style="border: 1px solid black; text-align: center;">' . $row['account_name'] . '</td>
                        <td style="border: 1px solid black;">' . $row['description'] . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . ($row['debit'] > 0 ? 'Rp ' . number_format($row['debit'], 0, ',', '.') : '') . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . ($row['credit'] > 0 ? 'Rp ' . number_format($row['credit'], 0, ',', '.') : '') . '</td>
                        <td style="border: 1px solid black; text-align: right;">Rp ' . number_format($balance, 0, ',', '.') . '</td>
                      </tr>';
                
                if ($iteration % 100 == 0) flush();
            }

            // Output Footer
            echo '</tbody><tfoot>
                    <tr>
                        <td></td>
                        <td colspan="4" style="border: 1px solid black; font-weight: bold; text-align: center; background-color: #FFFF00;">TOTAL</td>
                        <td style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #FFFF00;">Rp ' . number_format($totalDebit, 0, ',', '.') . '</td>
                        <td style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #FFFF00;">Rp ' . number_format($totalCredit, 0, ',', '.') . '</td>
                        <td style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #FFFF00;">Rp ' . number_format($balance, 0, ',', '.') . '</td>
                    </tr>
                  </tfoot></table></body></html>';

        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function index(Request $request)
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

        // Fetch Transactions for the Transaksi tab with Filters
        $query = FinancialTransaction::with(['fromAccount', 'toAccount'])
            ->where('office_id', $office_id);

        if ($request->account_id) {
            $query->where(function($q) use ($request) {
                $q->where('from_account_id', $request->account_id)
                  ->orWhere('to_account_id', $request->account_id);
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->start_date) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        $transactions = $query->latest('transaction_date')
            ->paginate(10);

        if ($request->ajax()) {
            return view('Finance.partials.transaction_table', compact('transactions'))->render();
        }

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

        // 7. Delivery Orders
        $deliveryOrderAmount = DB::table('delivery_order_invoices')
            ->where('chart_of_accounts_id', $accountId)
            ->sum('total_cost');

        return ($income + $transferIn + $otherIncome) - ($expense + $transferOut + $otherExpense + $deliveryOrderAmount);
    }

    private function calculateIncomeLain($accountId, $officeId)
    {
        // 1. Payments (Income from Invoices)
        $income = Payment::where('office_id', $officeId)
            ->where('akun_keuangan_id', $accountId)
            ->sum('jumlah_bayar');

        // 2. Transfers In (Debit)
        $transferIn = FinancialTransaction::where('office_id', $officeId)
            ->where('to_account_id', $accountId)
            ->where('type', 'transfer')
            ->where('status', 'posted')
            ->sum('amount');

        // 3. Other Income (Debit)
        $otherIncome = FinancialTransaction::where('office_id', $officeId)
            ->where('to_account_id', $accountId)
            ->where('type', 'income')
            ->where('status', 'posted')
            ->sum('amount');

        return $income + $transferIn + $otherIncome;
    }

    private function calculateExpenseLain($accountId, $officeId)
    {
        // 1. Expenses (Operational Expenses)
        $expense = Expense::where('office_id', $officeId)
            ->where('akun_keuangan_id', $accountId)
            ->sum('jumlah');

        // 2. Transfers Out (Credit)
        $transferOut = FinancialTransaction::where('office_id', $officeId)
            ->where('from_account_id', $accountId)
            ->where('type', 'transfer')
            ->where('status', 'posted')
            ->sum('amount');

        // 3. Other Expense (Credit)
        $otherExpense = FinancialTransaction::where('office_id', $officeId)
            ->where('from_account_id', $accountId)
            ->where('type', 'expense')
            ->where('status', 'posted')
            ->sum('amount');

        // 4. Delivery Orders
        $deliveryOrderAmount = DB::table('delivery_order_invoices')
            ->where('chart_of_accounts_id', $accountId)
            ->sum('total_cost');

        return $expense + $transferOut + $otherExpense + $deliveryOrderAmount;
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
                'message' => 'Transaksi berhasil disimpan',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
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
                'message' => 'Akun Keuangan berhasil disimpan',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    // Helper to generate next code
    public function getNextCode(Request $request)
    {
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
        if ($type == 'Cash') {
            return response()->json(['code' => '1101']);
        }
        if ($type == 'Bank') {
            return response()->json(['code' => '1201']);
        }
        if ($type == 'Corporate Card') {
            return response()->json(['code' => '1251']);
        }

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
                    'message' => 'Tidak dapat menghapus akun yang memiliki transaksi.',
                ], 400);
            }

            $account->delete();

            // Log Activity
            $this->logActivity('delete', 'financial_accounts', $id);

            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }
}
