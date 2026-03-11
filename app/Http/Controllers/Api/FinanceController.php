<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        if (! $accountId) {
            return redirect()->back()->with('error', 'Akun tidak ditemukan');
        }

        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $filename = 'Laporan_Kas_Kecil_'.date('YmdHis').'.xls';

        return response()->streamDownload(function () use ($officeId, $accountId, $start, $end) {
            $startStr = $start->toDateString();
            $endStr = $end->toDateString();

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
                ->where('created_at', '<', $start)
                ->sum('total_cost');

            $balance = $openingIncome + $openingTransferIn - ($openingExpense + $openingTransferOut + $openingDeliveryCost);

            echo '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>';
            echo '<table border="1" style="border-collapse: collapse; width: 100%;">';
            echo '<thead>';
            echo '<tr><th></th><th colspan="7" style="text-align: center; font-weight: bold; font-size: 16px; border: 1px solid black;">LAPORAN KAS KECIL CV DE JAVANESE AUTO PARTS</th></tr>';
            echo '<tr><th></th><th colspan="7" style="text-align: center; font-weight: bold; font-size: 14px; border: 1px solid black;">PERIODE '.$start->translatedFormat('d F Y').' - '.$end->translatedFormat('d F Y').'</th></tr>';
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

            echo '<tr>
                    <td></td>
                    <td style="border: 1px solid black; text-align: center;">1</td>
                    <td style="border: 1px solid black; text-align: center;">'.$start->format('M d, y').'</td>
                    <td style="border: 1px solid black; text-align: center;">SALDO</td>
                    <td style="border: 1px solid black;">SISA SALDO</td>
                    <td style="border: 1px solid black; text-align: right;">'.($balance > 0 ? 'Rp '.number_format($balance, 0, ',', '.') : '').'</td>
                    <td style="border: 1px solid black; text-align: right;">'.($balance < 0 ? 'Rp '.number_format(abs($balance), 0, ',', '.') : '').'</td>
                    <td style="border: 1px solid black; text-align: right;">Rp '.number_format($balance, 0, ',', '.').'</td>
                  </tr>';

            $rows = [];

            $payments = Payment::with(['invoice' => function ($q) {
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
                    'description' => ($p->invoice?->mitra?->nama ?? 'Pembayaran').' - '.($p->invoice?->nomor_invoice ?? $p->nomor_pembayaran),
                    'debit' => $p->jumlah_bayar,
                    'credit' => 0,
                ];
            }

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
                    'credit' => $e->jumlah,
                ];
            }

            $transactions = FinancialTransaction::select('transaction_date', 'type', 'amount', 'description', 'to_account_id', 'from_account_id')
                ->where('office_id', $officeId)
                ->where('status', 'posted')
                ->where(function ($q) use ($accountId) {
                    $q->where('from_account_id', $accountId)->orWhere('to_account_id', $accountId);
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
                        'credit' => 0,
                    ];
                } elseif ($t->from_account_id == $accountId && in_array($t->type, ['transfer', 'expense'])) {
                    $rows[] = [
                        'date' => $t->transaction_date,
                        'account_name' => 'BEBAN',
                        'description' => $t->description ?? 'Pengeluaran',
                        'debit' => 0,
                        'credit' => $t->amount,
                    ];
                }
            }

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
                    'credit' => $dc->total_cost,
                ];
            }

            usort($rows, function ($a, $b) {
                return strcmp($a['date'], $b['date']);
            });

            $totalDebit = 0;
            $totalCredit = 0;
            $iteration = 2;

            foreach ($rows as $row) {
                $totalDebit += $row['debit'];
                $totalCredit += $row['credit'];
                $balance += $row['debit'] - $row['credit'];

                echo '<tr>';
                echo '<td></td>';
                echo '<td style="border: 1px solid black; text-align: center;">'.$iteration.'</td>';
                echo '<td style="border: 1px solid black; text-align: center;">'.Carbon::parse($row['date'])->format('M d, y').'</td>';
                echo '<td style="border: 1px solid black; text-align: center;">'.$row['account_name'].'</td>';
                echo '<td style="border: 1px solid black;">'.$row['description'].'</td>';
                echo '<td style="border: 1px solid black; text-align: right;">'.($row['debit'] ? 'Rp '.number_format($row['debit'], 0, ',', '.') : '').'</td>';
                echo '<td style="border: 1px solid black; text-align: right;">'.($row['credit'] ? 'Rp '.number_format($row['credit'], 0, ',', '.') : '').'</td>';
                echo '<td style="border: 1px solid black; text-align: right;">Rp '.number_format($balance, 0, ',', '.').'</td>';
                echo '</tr>';
                $iteration++;
            }

            echo '<tr style="font-weight: bold; background-color: #f1f1f1;">';
            echo '<td></td>';
            echo '<td colspan="4" style="border: 1px solid black; text-align: center;">TOTAL</td>';
            echo '<td style="border: 1px solid black; text-align: right;">Rp '.number_format($totalDebit, 0, ',', '.').'</td>';
            echo '<td style="border: 1px solid black; text-align: right;">Rp '.number_format($totalCredit, 0, ',', '.').'</td>';
            echo '<td style="border: 1px solid black; text-align: right;">Rp '.number_format($balance, 0, ',', '.').'</td>';
            echo '</tr>';

            echo '</tbody></table></body></html>';
        }, $filename);
    }

    public function index(Request $request)
    {
        $officeId = session('active_office_id');

        $accounts = FinancialAccount::where('office_id', $officeId)->get();

        foreach ($accounts as $account) {
            $account->balance = $this->calculateBalance($account->id, $officeId);
            $account->income_lain = $this->calculateIncomeLain($account->id, $officeId);
            $account->expense_lain = $this->calculateExpenseLain($account->id, $officeId);
        }

        $all_accounts = FinancialAccount::where('office_id', $officeId)->get();
        $parent_accounts = COA::where('office_id', $officeId)->orderBy('kode_akun')->get();

        $query = FinancialTransaction::with(['fromAccount', 'toAccount'])->where('office_id', $officeId);

        if ($request->account_id) {
            $query->where(function ($q) use ($request) {
                $q->where('from_account_id', $request->account_id)->orWhere('to_account_id', $request->account_id);
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

        $transactions = $query->latest('transaction_date')->paginate(10);

        if ($request->ajax()) {
            return view('Finance.partials.transaction_table', compact('transactions'))->render();
        }

        return view('Finance.index', compact('accounts', 'all_accounts', 'parent_accounts', 'transactions'));
    }

    private function calculateBalance($accountId, $officeId)
    {
        $income = Payment::where('office_id', $officeId)
            ->where('akun_keuangan_id', $accountId)
            ->sum('jumlah_bayar');

        $expense = Expense::where('office_id', $officeId)
            ->where('akun_keuangan_id', $accountId)
            ->sum('jumlah');

        $transferIn = FinancialTransaction::where('office_id', $officeId)
            ->where('to_account_id', $accountId)
            ->where('type', 'transfer')
            ->where('status', 'posted')
            ->sum('amount');

        $transferOut = FinancialTransaction::where('office_id', $officeId)
            ->where('from_account_id', $accountId)
            ->where('type', 'transfer')
            ->where('status', 'posted')
            ->sum('amount');

        $otherIncome = FinancialTransaction::where('office_id', $officeId)
            ->where('to_account_id', $accountId)
            ->where('type', 'income')
            ->where('status', 'posted')
            ->sum('amount');

        $otherExpense = FinancialTransaction::where('office_id', $officeId)
            ->where('from_account_id', $accountId)
            ->where('type', 'expense')
            ->where('status', 'posted')
            ->sum('amount');

        $deliveryOrderAmount = DB::table('delivery_order_invoices')
            ->where('chart_of_accounts_id', $accountId)
            ->sum('total_cost');

        return ($income + $transferIn + $otherIncome) - ($expense + $transferOut + $otherExpense + $deliveryOrderAmount);
    }

    private function calculateIncomeLain($accountId, $officeId)
    {
        $income = Payment::where('office_id', $officeId)
            ->where('akun_keuangan_id', $accountId)
            ->sum('jumlah_bayar');

        $transferIn = FinancialTransaction::where('office_id', $officeId)
            ->where('to_account_id', $accountId)
            ->where('type', 'transfer')
            ->where('status', 'posted')
            ->sum('amount');

        $otherIncome = FinancialTransaction::where('office_id', $officeId)
            ->where('to_account_id', $accountId)
            ->where('type', 'income')
            ->where('status', 'posted')
            ->sum('amount');

        return $income + $transferIn + $otherIncome;
    }

    private function calculateExpenseLain($accountId, $officeId)
    {
        $expense = Expense::where('office_id', $officeId)
            ->where('akun_keuangan_id', $accountId)
            ->sum('jumlah');

        $transferOut = FinancialTransaction::where('office_id', $officeId)
            ->where('from_account_id', $accountId)
            ->where('type', 'transfer')
            ->where('status', 'posted')
            ->sum('amount');

        $otherExpense = FinancialTransaction::where('office_id', $officeId)
            ->where('from_account_id', $accountId)
            ->where('type', 'expense')
            ->where('status', 'posted')
            ->sum('amount');

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

            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                $path = $file->store('financial_transactions', 'public');
                $input['lampiran'] = $path;
            }

            $transaction = FinancialTransaction::create($input);
            $this->logActivity('create', 'financial_transactions', $transaction->id, null, $transaction->toArray());
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Transaksi berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'tipe_akun' => 'required|string',
            'mode' => 'required|in:new,existing',
        ]);

        try {
            DB::beginTransaction();
            $input = $request->all();
            $input['office_id'] = session('active_office_id');
            $input['type'] = $request->tipe_akun;
            $input['name'] = $request->nama_akun;
            $input['code'] = $request->kode_akun;

            if ($request->mode == 'new') {
                $request->validate([
                    'kode_akun' => 'required|unique:financial_accounts,code',
                    'nama_akun' => 'required|string',
                ]);

                $account = FinancialAccount::create($input);
            } else {
                $request->validate([
                    'existing_coa_id' => 'required|exists:financial_accounts,id',
                ]);

                $account = FinancialAccount::find($request->existing_coa_id);
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

            $this->logActivity($request->mode == 'new' ? 'create' : 'update', 'financial_accounts', $account->id, null, $account->toArray());
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Akun Keuangan berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function getNextCode(Request $request)
    {
        $type = $request->type;
        $lastAccount = FinancialAccount::where('type', $type)->orderBy('code', 'desc')->first();

        if ($lastAccount && $lastAccount->code) {
            if (preg_match('/(\d+)$/', $lastAccount->code, $matches)) {
                $number = $matches[1];
                $nextNumber = str_pad($number + 1, strlen($number), '0', STR_PAD_LEFT);
                $nextCode = preg_replace('/(\d+)$/', $nextNumber, $lastAccount->code);

                return response()->json(['code' => $nextCode]);
            }
        }

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

            $exists = FinancialTransaction::where('from_account_id', $id)
                ->orWhere('to_account_id', $id)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus akun yang memiliki transaksi.'], 400);
            }

            $account->delete();
            $this->logActivity('delete', 'financial_accounts', $id);

            return response()->json(['success' => true, 'message' => 'Akun berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
