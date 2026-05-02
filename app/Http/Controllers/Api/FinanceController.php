<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\COA;
use App\Models\DeliveryOrderInvoice;
use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FinanceController extends Controller
{
    use LogsActivity;

    public function previewReport(Request $request)
    {
        $officeId = session('active_office_id');
        $accountId = $request->account_id;
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $data = $this->getReportData($officeId, $accountId, $start, $end);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $officeId = session('active_office_id');
        $accounts = FinancialAccount::where('office_id', $officeId)->orderBy('code')->get();
        $accountId = $request->account_id;

        $account = $accountId ? FinancialAccount::find($accountId) : null;
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $reportData = $this->getReportData($officeId, $accountId, $start, $end);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Keuangan');

        // Header
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'LAPORAN KEUANGAN: '.strtoupper($account->name ?? 'SEMUA AKUN'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'Periode: '.$start->format('d/m/Y').' - '.$end->format('d/m/Y'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Table Header
        $headers = ['No', 'Tanggal', 'Keterangan', 'Debit', 'Kredit', 'Saldo'];
        $columnMap = ['A', 'B', 'C', 'D', 'E', 'F'];
        foreach ($headers as $index => $header) {
            $col = $columnMap[$index];
            $sheet->setCellValue($col.'4', $header);
            $sheet->getStyle($col.'4')->getFont()->setBold(true);
            $sheet->getStyle($col.'4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col.'4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F1F1');
            $sheet->getStyle($col.'4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);

        // Saldo Awal
        $rowIdx = 5;
        $sheet->setCellValue('A'.$rowIdx, 1);
        $sheet->setCellValue('B'.$rowIdx, $start->format('d/m/Y'));
        $sheet->setCellValue('C'.$rowIdx, 'SALDO AWAL');
        $sheet->getStyle('C'.$rowIdx)->getFont()->setBold(true);
        $sheet->setCellValue('D'.$rowIdx, '-');
        $sheet->setCellValue('E'.$rowIdx, '-');
        $sheet->setCellValue('F'.$rowIdx, $reportData['opening_balance']);
        $sheet->getStyle('F'.$rowIdx)->getFont()->setBold(true);
        $sheet->getStyle('F'.$rowIdx)->getNumberFormat()->setFormatCode('"Rp "#,##0');

        foreach ($columnMap as $col) {
            $sheet->getStyle($col.$rowIdx)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            if (in_array($col, ['A', 'B'])) {
                $sheet->getStyle($col.$rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            if ($col == 'F') {
                $sheet->getStyle($col.$rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        }

        // Data Rows
        $balance = $reportData['opening_balance'];
        $totalDebit = 0;
        $totalCredit = 0;
        $rowIdx++;

        foreach ($reportData['rows'] as $index => $row) {
            $balance += $row['debit'];
            $balance -= $row['credit'];
            $totalDebit += $row['debit'];
            $totalCredit += $row['credit'];

            $sheet->setCellValue('A'.$rowIdx, $index + 2);
            $sheet->setCellValue('B'.$rowIdx, Carbon::parse($row['date'])->format('d/m/Y'));
            $desc = strtoupper($row['account_name'])."\n".$row['description'];
            $sheet->setCellValue('C'.$rowIdx, $desc);
            $sheet->getStyle('C'.$rowIdx)->getAlignment()->setWrapText(true);

            if ($row['debit'] > 0) {
                $sheet->setCellValue('D'.$rowIdx, $row['debit']);
                $sheet->getStyle('D'.$rowIdx)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            } else {
                $sheet->setCellValue('D'.$rowIdx, '-');
            }

            if ($row['credit'] > 0) {
                $sheet->setCellValue('E'.$rowIdx, $row['credit']);
                $sheet->getStyle('E'.$rowIdx)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            } else {
                $sheet->setCellValue('E'.$rowIdx, '-');
            }

            $sheet->setCellValue('F'.$rowIdx, $balance);
            $sheet->getStyle('F'.$rowIdx)->getNumberFormat()->setFormatCode('"Rp "#,##0');

            foreach ($columnMap as $col) {
                $sheet->getStyle($col.$rowIdx)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle($col.$rowIdx)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                if (in_array($col, ['A', 'B'])) {
                    $sheet->getStyle($col.$rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
                if (in_array($col, ['D', 'E', 'F'])) {
                    $sheet->getStyle($col.$rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
            }
            $rowIdx++;
        }

        // Total Row
        $sheet->mergeCells('A'.$rowIdx.':C'.$rowIdx);
        $sheet->setCellValue('A'.$rowIdx, 'TOTAL');
        $sheet->getStyle('A'.$rowIdx)->getFont()->setBold(true);
        $sheet->getStyle('A'.$rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('D'.$rowIdx, $totalDebit);
        $sheet->getStyle('D'.$rowIdx)->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->setCellValue('E'.$rowIdx, $totalCredit);
        $sheet->getStyle('E'.$rowIdx)->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->setCellValue('F'.$rowIdx, $balance);
        $sheet->getStyle('F'.$rowIdx)->getNumberFormat()->setFormatCode('"Rp "#,##0');

        foreach ($columnMap as $col) {
            $sheet->getStyle($col.$rowIdx)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($col.$rowIdx)->getFont()->setBold(true);
            $sheet->getStyle($col.$rowIdx)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F1F1');
            if (in_array($col, ['D', 'E', 'F'])) {
                $sheet->getStyle($col.$rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        }

        $accountName = $account ? $account->name : 'Semua Akun';
        $filename = 'Laporan_Keuangan_'.str_replace(' ', '_', $accountName).'_'.$start->format('Ymd').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function getReportData($officeId, $accountId, $start, $end)
    {
        $startStr = $start->toDateString();
        $endStr = $end->toDateString();

        $openingIncome = Payment::where('office_id', $officeId)
            ->when($accountId, fn ($q) => $q->where('akun_keuangan_id', $accountId))
            ->where('tgl_pembayaran', '<', $startStr)
            ->sum('jumlah_bayar');

        $openingExpense = Expense::where('office_id', $officeId)
            ->when($accountId, fn ($q) => $q->where('akun_keuangan_id', $accountId))
            ->where('tgl_biaya', '<', $startStr)
            ->sum('jumlah');

        $openingTransferIn = FinancialTransaction::where('office_id', $officeId)
            ->when($accountId, fn ($q) => $q->where('to_account_id', $accountId))
            ->where('status', 'posted')
            ->whereIn('type', ['transfer', 'income'])
            ->where('transaction_date', '<', $startStr)
            ->sum('amount');

        $openingTransferOut = FinancialTransaction::where('office_id', $officeId)
            ->when($accountId, fn ($q) => $q->where('from_account_id', $accountId))
            ->where('status', 'posted')
            ->whereIn('type', ['transfer', 'expense'])
            ->where('transaction_date', '<', $startStr)
            ->sum('amount');

        $openingDeliveryCost = DeliveryOrderInvoice::when($accountId, fn ($q) => $q->where('chart_of_accounts_id', $accountId))
            ->where('created_at', '<', $start)
            ->sum('total_cost');

        $openingBalance = $openingIncome + $openingTransferIn - ($openingExpense + $openingTransferOut + $openingDeliveryCost);

        $rows = [];

        $payments = Payment::with([
            'invoice' => function ($q) {
                $q->select('id', 'mitra_id', 'nomor_invoice', 'tipe_invoice');
            }, 'invoice.mitra:id,nama',
        ])
            ->select('tgl_pembayaran', 'jumlah_bayar', 'invoice_id', 'nomor_pembayaran')
            ->where('office_id', $officeId)
            ->when($accountId, fn ($q) => $q->where('akun_keuangan_id', $accountId))
            ->whereBetween('tgl_pembayaran', [$startStr, $endStr])
            ->get();

        foreach ($payments as $p) {
            $isPurchase = ($p->invoice?->tipe_invoice === 'Purchase');
            $rows[] = [
                'date' => $p->tgl_pembayaran,
                'account_name' => $isPurchase ? 'BEBAN' : 'PENDAPATAN',
                'description' => ($p->invoice?->mitra?->nama ?? 'Pembayaran').' - '.($p->invoice?->nomor_invoice ?? $p->nomor_pembayaran),
                'debit' => $isPurchase ? 0 : $p->jumlah_bayar,
                'credit' => $isPurchase ? $p->jumlah_bayar : 0,
            ];
        }

        $expenses = Expense::select('tgl_biaya', 'nama_biaya', 'jumlah')
            ->where('office_id', $officeId)
            ->when($accountId, fn ($q) => $q->where('akun_keuangan_id', $accountId))
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
            ->when($accountId, function ($q) use ($accountId) {
                $q->where(function ($sq) use ($accountId) {
                    $sq->where('from_account_id', $accountId)->orWhere('to_account_id', $accountId);
                });
            })
            ->whereBetween('transaction_date', [$startStr, $endStr])
            ->get();

        foreach ($transactions as $t) {
            if ($accountId) {
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
            } else {
                if (in_array($t->type, ['income'])) {
                    $rows[] = [
                        'date' => $t->transaction_date,
                        'account_name' => 'DEPOSIT',
                        'description' => $t->description ?? 'Penerimaan',
                        'debit' => $t->amount,
                        'credit' => 0,
                    ];
                } elseif (in_array($t->type, ['expense'])) {
                    $rows[] = [
                        'date' => $t->transaction_date,
                        'account_name' => 'BEBAN',
                        'description' => $t->description ?? 'Pengeluaran',
                        'debit' => 0,
                        'credit' => $t->amount,
                    ];
                } elseif (in_array($t->type, ['transfer'])) {
                    $rows[] = [
                        'date' => $t->transaction_date,
                        'account_name' => 'TRANSFER OUT',
                        'description' => ($t->description ?? 'Transfer').' (Dari '.($t->fromAccount->name ?? '-').' ke '.($t->toAccount->name ?? '-').')',
                        'debit' => 0,
                        'credit' => $t->amount,
                    ];
                    $rows[] = [
                        'date' => $t->transaction_date,
                        'account_name' => 'TRANSFER IN',
                        'description' => ($t->description ?? 'Transfer').' (Masuk ke '.($t->toAccount->name ?? '-').' dari '.($t->fromAccount->name ?? '-').')',
                        'debit' => $t->amount,
                        'credit' => 0,
                    ];
                }
            }
        }

        $deliveryCosts = DeliveryOrderInvoice::select('created_at', 'total_cost')
            ->when($accountId, fn ($q) => $q->where('chart_of_accounts_id', $accountId))
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

        return [
            'opening_balance' => $openingBalance,
            'rows' => $rows,
        ];
    }

    public function index(Request $request)
    {
        $officeId = session('active_office_id');

        $accounts = FinancialAccount::where('office_id', $officeId)
            ->withSum(['payments as income_sales' => function ($q) {
                $q->whereHas('invoice', fn ($sq) => $sq->where('tipe_invoice', 'Sales'));
            }], 'jumlah_bayar')
            ->withSum(['payments as expense_purchase' => function ($q) {
                $q->whereHas('invoice', fn ($sq) => $sq->where('tipe_invoice', 'Purchase'));
            }], 'jumlah_bayar')
            ->withSum('expenses', 'jumlah')
            ->withSum(['transfersIn as transfer_in' => fn ($q) => $q->where('type', 'transfer')->where('status', 'posted')], 'amount')
            ->withSum(['transfersOut as transfer_out' => fn ($q) => $q->where('type', 'transfer')->where('status', 'posted')], 'amount')
            ->withSum(['transfersIn as other_income' => fn ($q) => $q->where('type', 'income')->where('status', 'posted')], 'amount')
            ->withSum(['transfersOut as other_expense' => fn ($q) => $q->where('type', 'expense')->where('status', 'posted')], 'amount')
            ->withSum('deliveryOrderCosts', 'total_cost')
            ->get();

        foreach ($accounts as $account) {
            $inc = ($account->income_sales ?? 0) + ($account->transfer_in ?? 0) + ($account->other_income ?? 0);
            $exp = ($account->expense_purchase ?? 0) + ($account->expenses_sum_jumlah ?? 0) + ($account->transfer_out ?? 0) + ($account->other_expense ?? 0) + ($account->delivery_order_costs_sum_total_cost ?? 0);

            $account->balance = $inc - $exp;
            $account->income_lain = $inc;
            $account->expense_lain = $exp;
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

        $transactions = $query->latest('transaction_date')->paginate(10)->withQueryString();

        if ($request->ajax() || $request->has('ajax')) {
            return view('Finance.partials.transaction_table', compact('transactions'))->render();
        }

        return view('Finance.index', compact('accounts', 'all_accounts', 'parent_accounts', 'transactions'));
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

    public function updateTransaction(Request $request, $id)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'type' => 'required|in:transfer,income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:posted,draft,canceled',
            'from_account_id' => 'required_if:type,transfer,expense|nullable|exists:financial_accounts,id',
            'to_account_id' => 'required_if:type,transfer,income|nullable|exists:financial_accounts,id',
        ]);

        try {
            DB::beginTransaction();
            $transaction = FinancialTransaction::where('office_id', session('active_office_id'))->findOrFail($id);
            $old = $transaction->toArray();

            $input = $request->only(['transaction_date', 'type', 'amount', 'description', 'status', 'from_account_id', 'to_account_id']);

            // Clear irrelevant account fields based on type
            if ($request->type === 'income') {
                $input['from_account_id'] = null;
            } elseif ($request->type === 'expense') {
                $input['to_account_id'] = null;
            }

            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                $path = $file->store('financial_transactions', 'public');
                $input['lampiran'] = $path;
            }

            $transaction->update($input);
            $this->logActivity('update', 'financial_transactions', $transaction->id, $old, $transaction->toArray());
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Transaksi berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroyTransaction($id)
    {
        try {
            $transaction = FinancialTransaction::where('office_id', session('active_office_id'))->findOrFail($id);
            $this->logActivity('delete', 'financial_transactions', $id, $transaction->toArray(), null);
            $transaction->delete();

            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dihapus']);
        } catch (\Exception $e) {
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
