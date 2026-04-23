<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\COA;
use App\Models\COAGroup;
use App\Models\COAType;
use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Invoice;
use App\Models\JournalDetail;
use App\Models\Office;
use App\Models\Partner;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private $views = 'Report.';

    public function arAging(Request $request)
    {
        $officeId = session('active_office_id');
        $mitras = Partner::where('office_id', $officeId)
            ->where('tipe_mitra', '!=', 'Vendor')
            ->where('is_cash_customer', false)
            ->get();

        $query = Invoice::where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->where('status_pembayaran', '!=', 'Paid')
            ->whereNull('deleted_at')
            ->whereHas('mitra', function ($q) {
                $q->where('is_cash_customer', false);
            })
            ->with(['mitra', 'sales']);

        if ($request->mitra_id) {
            $query->where('mitra_id', $request->mitra_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tgl_invoice', [$request->start_date, $request->end_date]);
        }

        $invoices = $query->withSum(['payment as paid_amount'], 'jumlah_bayar')->get();

        $agingData = [];
        $customerDetails = [];
        $summary = [
            'total_customers' => 0,
            'avg_days_past_due' => 0,
            'total_invoices' => 0,
            'total_amount' => 0,
            'buckets' => [
                'current' => 0,
                '1-15' => 0,
                '16-30' => 0,
                '31-45' => 0,
                '46-60' => 0,
                '61+' => 0,
            ],
        ];

        $grouped = $invoices->groupBy('mitra_id');
        $sumAvgDays = 0;

        foreach ($grouped as $invs) {
            $mitraName = $invs->first()->mitra->nama ?? 'Unknown';
            $mitraCode = $invs->first()->mitra->nomor_mitra ?? '-';
            $mitraTotal = 0;
            $mitraInvoicesCount = 0;
            $mitraTotalDaysOverdue = 0;
            $mitraOverdueCount = 0;

            $buckets = [
                'current' => 0,
                '1-15' => 0,
                '16-30' => 0,
                '31-45' => 0,
                '46-60' => 0,
                '61+' => 0,
            ];

            foreach ($invs as $inv) {
                $remaining = $inv->total_akhir - ($inv->paid_amount ?? 0);
                if ($remaining <= 0) {
                    continue;
                }

                $dueDate = $inv->tgl_jatuh_tempo ? Carbon::parse($inv->tgl_jatuh_tempo) : Carbon::parse($inv->tgl_invoice);
                $daysOverdue = floor($dueDate->diffInDays(Carbon::now(), false));

                if ($daysOverdue <= 0) {
                    $buckets['current'] += $remaining;
                } elseif ($daysOverdue <= 15) {
                    $buckets['1-15'] += $remaining;
                } elseif ($daysOverdue <= 30) {
                    $buckets['16-30'] += $remaining;
                } elseif ($daysOverdue <= 45) {
                    $buckets['31-45'] += $remaining;
                } elseif ($daysOverdue <= 60) {
                    $buckets['46-60'] += $remaining;
                } else {
                    $buckets['61+'] += $remaining;
                }

                $mitraTotal += $remaining;
                $mitraInvoicesCount++;

                if ($daysOverdue > 0) {
                    $mitraTotalDaysOverdue += $daysOverdue;
                    $mitraOverdueCount++;
                }
            }

            if ($mitraTotal <= 0) {
                continue;
            }

            $avgDays = $mitraOverdueCount > 0 ? $mitraTotalDaysOverdue / $mitraOverdueCount : 0;
            $agingData[] = [
                'mitra_name' => $mitraName,
                'avg_days' => $avgDays,
                'count' => $mitraInvoicesCount,
                'total' => $mitraTotal,
                'buckets' => $buckets,
            ];

            $totalTagihan = $invs->sum('total_akhir');
            $totalPaid = $invs->sum('paid_amount');
            $outstanding = $totalTagihan - $totalPaid;

            $ages = [];
            foreach ($invs as $inv) {
                $remaining = $inv->total_akhir - ($inv->paid_amount ?? 0);
                if ($remaining <= 0) {
                    continue;
                }
                $ages[] = Carbon::parse($inv->tgl_invoice)->diffInDays(Carbon::now());
            }
            $avgAge = count($ages) > 0 ? floor(array_sum($ages) / count($ages)) : 0;

            $latestInv = $invs->sortByDesc('tgl_invoice')->first();
            $salesUser = $latestInv ? $latestInv->sales : null;

            $customerDetails[] = [
                'kode' => $mitraCode,
                'nama' => $mitraName,
                'tagihan' => $totalTagihan,
                'dibayar' => $totalPaid,
                'outstanding' => $outstanding,
                'sales_code' => $salesUser?->username ?? '-',
                'sales_name' => $salesUser?->name ?? '-',
                'umur_nota' => $avgAge,
            ];

            $summary['total_customers']++;
            $summary['total_invoices'] += $mitraInvoicesCount;
            $summary['total_amount'] += $mitraTotal;
            $sumAvgDays += $avgDays;

            foreach ($buckets as $key => $val) {
                $summary['buckets'][$key] += $val;
            }
        }

        if ($summary['total_customers'] > 0) {
            $summary['avg_days_past_due'] = $sumAvgDays / $summary['total_customers'];
        }

        usort($agingData, fn ($a, $b) => $b['total'] <=> $a['total']);

        if ($request->has('export') && $request->export == 'excel') {
            $filename = 'Laporan_Umur_Piutang_'.date('YmdHis').'.xls';

            return response()->streamDownload(function () use ($summary, $customerDetails) {
                echo view('Report.ar-aging-excel', compact('summary', 'customerDetails'));
            }, $filename, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        }

        return view($this->views.'ar-aging', compact('agingData', 'summary', 'mitras', 'customerDetails'));
    }

    public function cashBook(Request $request)
    {
        $officeId = session('active_office_id');
        $accounts = FinancialAccount::where('office_id', $officeId)->orderBy('code')->get();
        $accountId = $request->account_id ?: ($accounts->first()->id ?? null);

        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $openingIncome = Payment::where('office_id', $officeId)
            ->where('akun_keuangan_id', $accountId)
            ->whereDate('tgl_pembayaran', '<', $start->toDateString())
            ->sum('jumlah_bayar');

        $openingExpense = Expense::where('office_id', $officeId)
            ->where('akun_keuangan_id', $accountId)
            ->whereDate('tgl_biaya', '<', $start->toDateString())
            ->sum('jumlah');

        $openingTransferIn = FinancialTransaction::where('office_id', $officeId)
            ->where('to_account_id', $accountId)
            ->where('status', 'posted')
            ->whereIn('type', ['transfer', 'income'])
            ->whereDate('transaction_date', '<', $start->toDateString())
            ->sum('amount');

        $openingTransferOut = FinancialTransaction::where('office_id', $officeId)
            ->where('from_account_id', $accountId)
            ->where('status', 'posted')
            ->whereIn('type', ['transfer', 'expense'])
            ->whereDate('transaction_date', '<', $start->toDateString())
            ->sum('amount');

        $openingBalance = $openingIncome + $openingTransferIn - ($openingExpense + $openingTransferOut);

        $rows = [];
        $rows[] = [
            'date' => $start->toDateString(),
            'type' => 'DEPOSIT',
            'description' => 'SALDO AWAL',
            'debit' => $openingBalance > 0 ? $openingBalance : 0,
            'credit' => $openingBalance < 0 ? abs($openingBalance) : 0,
        ];

        $payments = Payment::with(['invoice.mitra'])
            ->where('office_id', $officeId)
            ->where('akun_keuangan_id', $accountId)
            ->whereBetween('tgl_pembayaran', [$start->toDateString(), $end->toDateString()])
            ->get();

        foreach ($payments as $p) {
            $mitra = $p->invoice?->mitra?->nama;
            $invNo = $p->invoice?->nomor_invoice;
            $desc = ($mitra ?: 'Pembayaran').' - Pembayaran Nota '.($invNo ?: $p->nomor_pembayaran);
            $rows[] = [
                'date' => Carbon::parse($p->tgl_pembayaran)->toDateString(),
                'type' => 'DEPOSIT',
                'description' => $desc,
                'debit' => $p->jumlah_bayar,
                'credit' => 0,
            ];
        }

        $expenses = Expense::where('office_id', $officeId)
            ->where('akun_keuangan_id', $accountId)
            ->whereBetween('tgl_biaya', [$start->toDateString(), $end->toDateString()])
            ->get();

        foreach ($expenses as $e) {
            $rows[] = [
                'date' => Carbon::parse($e->tgl_biaya)->toDateString(),
                'type' => 'BEBAN',
                'description' => $e->nama_biaya,
                'debit' => 0,
                'credit' => $e->jumlah,
            ];
        }

        $finTrxIn = FinancialTransaction::where('office_id', $officeId)
            ->where('to_account_id', $accountId)
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        foreach ($finTrxIn as $t) {
            $rows[] = [
                'date' => Carbon::parse($t->transaction_date)->toDateString(),
                'type' => 'DEPOSIT',
                'description' => $t->description ?? 'Penerimaan',
                'debit' => $t->amount,
                'credit' => 0,
            ];
        }

        $finTrxOut = FinancialTransaction::where('office_id', $officeId)
            ->where('from_account_id', $accountId)
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        foreach ($finTrxOut as $t) {
            $rows[] = [
                'date' => Carbon::parse($t->transaction_date)->toDateString(),
                'type' => 'BEBAN',
                'description' => $t->description ?? 'Pengeluaran',
                'debit' => 0,
                'credit' => $t->amount,
            ];
        }

        usort($rows, function ($a, $b) {
            if ($a['description'] === 'SALDO AWAL') {
                return -1;
            }
            if ($b['description'] === 'SALDO AWAL') {
                return 1;
            }

            return strcmp($a['date'], $b['date']);
        });

        $running = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($rows as &$r) {
            $totalDebit += $r['debit'];
            $totalCredit += $r['credit'];
            if ($r['description'] === 'SALDO AWAL') {
                $running = $openingBalance;
            } else {
                $running += $r['debit'];
                $running -= $r['credit'];
            }
            $r['balance'] = $running;
        }
        unset($r);

        $office = Office::find($officeId);

        return view($this->views.'cash-book', [
            'accounts' => $accounts,
            'accountId' => $accountId,
            'rows' => $rows,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'finalBalance' => $running,
            'start' => $start,
            'end' => $end,
            'officeName' => $office?->name,
        ]);
    }

    public function salesReport()
    {
        $year = date('Y');
        $officeId = session('active_office_id');

        $statsInvoices = Invoice::where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->withSum(['payment as paid_amount'], 'jumlah_bayar')
            ->get();

        $stats = (object) [
            'total_revenue' => $statsInvoices->sum('total_akhir'),
            'total_ar' => $statsInvoices->filter(fn ($inv) => $inv->status_pembayaran !== 'Paid')
                ->sum(fn ($inv) => max(0, $inv->total_akhir - ($inv->paid_amount ?? 0))),
            'total_count' => $statsInvoices->count(),
            'total_overdue' => $statsInvoices->filter(fn ($inv) => $inv->tgl_jatuh_tempo < now() && $inv->status_pembayaran !== 'Paid')
                ->sum(fn ($inv) => max(0, $inv->total_akhir - ($inv->paid_amount ?? 0))),
        ];

        $monthlyData = [];
        $monthlyRaw = Invoice::where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->whereYear('tgl_invoice', $year)
            ->selectRaw('MONTH(tgl_invoice) as month, SUM(total_akhir) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = $monthlyRaw->get($m, 0);
        }

        $topClients = Invoice::where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->selectRaw('mitras.nama, SUM(invoices.total_akhir) as total')
            ->groupBy('invoices.mitra_id', 'mitras.nama')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($row) => (object) [
                'nama' => $row->nama,
                'value' => $row->total,
            ]);

        return view($this->views.'sales', compact('stats', 'monthlyData', 'topClients'));
    }

    private function getStockData(Request $request)
    {
        $officeId = session('active_office_id');

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        $query = Product::leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->leftJoin('stock_mutations', function ($join) use ($officeId, $request) {
                $join->on('products.id', '=', 'stock_mutations.product_id')
                    ->where('stock_mutations.office_id', '=', $officeId)
                    ->whereNull('stock_mutations.deleted_at');

                if ($request->location_id) {
                    $join->where('stock_mutations.stock_location_id', $request->location_id);
                }
            })
            ->where('products.office_id', $officeId)
            ->select(
                'products.id',
                'products.nama_produk',
                'products.sku_kode',
                'products.satuan',
                'product_categories.nama_kategori',
                \DB::raw("SUM(CASE WHEN stock_mutations.created_at < '$startDate' AND stock_mutations.type = 'IN' THEN stock_mutations.qty WHEN stock_mutations.created_at < '$startDate' AND stock_mutations.type = 'OUT' THEN -stock_mutations.qty ELSE 0 END) as opening_qty"),
                \DB::raw("SUM(CASE WHEN stock_mutations.created_at BETWEEN '$startDate' AND '$endDate' AND stock_mutations.type = 'IN' THEN stock_mutations.qty ELSE 0 END) as qty_in"),
                \DB::raw("SUM(CASE WHEN stock_mutations.created_at BETWEEN '$startDate' AND '$endDate' AND stock_mutations.type = 'OUT' THEN stock_mutations.qty ELSE 0 END) as qty_out"),
                \DB::raw("SUM(CASE WHEN stock_mutations.created_at < '$startDate' AND stock_mutations.type = 'IN' THEN stock_mutations.qty * stock_mutations.cost_price WHEN stock_mutations.created_at < '$startDate' AND stock_mutations.type = 'OUT' THEN -stock_mutations.qty * stock_mutations.cost_price ELSE 0 END) as opening_value"),
                \DB::raw("SUM(CASE WHEN stock_mutations.created_at BETWEEN '$startDate' AND '$endDate' AND stock_mutations.type = 'IN' THEN stock_mutations.qty * stock_mutations.cost_price ELSE 0 END) as value_in"),
                \DB::raw("SUM(CASE WHEN stock_mutations.created_at BETWEEN '$startDate' AND '$endDate' AND stock_mutations.type = 'OUT' THEN stock_mutations.qty * stock_mutations.cost_price ELSE 0 END) as value_out"),
            )
            ->groupBy('products.id', 'products.nama_produk', 'products.sku_kode', 'products.satuan', 'product_categories.nama_kategori');

        if ($request->category_id) {
            $query->where('products.product_category_id', $request->category_id);
        }

        if ($request->product_id) {
            $query->where('products.id', $request->product_id);
        }

        $products = $query->get();

        $products->transform(function ($p) {
            $p->closing_qty = $p->opening_qty + $p->qty_in - $p->qty_out;
            $p->closing_value = $p->opening_value + $p->value_in - $p->value_out;

            return $p;
        });

        return $products;
    }

    public function stockReport(Request $request)
    {
        $officeId = session('active_office_id');

        $categories = ProductCategory::where('office_id', $officeId)->get();
        $locations = StockLocation::where('office_id', $officeId)->get();
        $allProducts = Product::where('office_id', $officeId)->select('id', 'nama_produk')->get();

        $products = $this->getStockData($request);

        $stats = [
            'opening_qty' => $products->sum('opening_qty'),
            'qty_in' => $products->sum('qty_in'),
            'qty_out' => $products->sum('qty_out'),
            'closing_qty' => $products->sum('closing_qty'),
            'opening_value' => $products->sum('opening_value'),
            'value_in' => $products->sum('value_in'),
            'value_out' => $products->sum('value_out'),
            'closing_value' => $products->sum('closing_value'),
        ];

        return view($this->views.'stock', compact('products', 'stats', 'categories', 'locations', 'allProducts'));
    }

    public function stockReportExport(Request $request)
    {
        $products = $this->getStockData($request);

        return view($this->views.'export_stock', compact('products'));
    }

    public function generalLedger(Request $request)
    {
        $officeId = session('active_office_id');
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');
        $coaId = $request->coa_id;
        $status = $request->status;

        $query = JournalDetail::with(['journal' => function ($query) use ($officeId, $startDate, $endDate) {
            $query->where('office_id', $officeId)
                ->whereBetween('tgl_jurnal', [$startDate, $endDate]);
        }, 'journal.invoice' => function ($query) use ($officeId) {
            $query->where('office_id', $officeId);
        }, 'coa'])
            ->whereHas('journal', function ($query) use ($officeId, $startDate, $endDate) {
                $query->where('office_id', $officeId)
                    ->whereBetween('tgl_jurnal', [$startDate, $endDate]);
            });

        if ($coaId) {
            $query->where('akun_id', $coaId);
        }

        if ($status) {
            $query->whereHas('journal.invoice', function ($query) use ($status) {
                $query->where('status_dok', $status);
            });
        }

        $journalLines = $query->get()
            ->map(function ($detail) {
                return (object) [
                    'tgl_jurnal' => $detail->journal->tgl_jurnal,
                    'keterangan' => $detail->journal->keterangan,
                    'nomor_referensi' => $detail->journal->nomor_referensi,
                    'nomor_journal' => $detail->nomor_journal,
                    'debit' => $detail->debit,
                    'kredit' => $detail->kredit,
                    'coa_id' => $detail->coa_id,
                    'kode_akun' => $detail->coa ? $detail->coa->kode_akun : null,
                    'nama_akun' => $detail->coa ? $detail->coa->nama_akun : null,
                    'status_dok' => $detail->journal->invoice ? $detail->journal->invoice->status_dok : null,
                ];
            })
            ->sortBy('tgl_jurnal')
            ->sortBy('nomor_journal')
            ->values();
        $groupedData = $journalLines->groupBy('coa_id');

        $reportData = [];
        foreach ($groupedData as $lines) {
            $first = $lines->first();
            $accountInfo = [
                'kode_akun' => $first->kode_akun,
                'nama_akun' => $first->nama_akun,
            ];

            $totalDebit = 0;
            $totalCredit = 0;
            $balance = 0;

            $processedLines = $lines->map(function ($line) use (&$totalDebit, &$totalCredit, &$balance) {
                $lineBalance = $line->debit - $line->kredit;
                $balance += $lineBalance;
                $totalDebit += $line->debit;
                $totalCredit += $line->kredit;
                $line->balance = $lineBalance;

                return $line;
            });

            $reportData[] = [
                'account' => $accountInfo,
                'lines' => $processedLines,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'total_saldo' => $balance,
            ];
        }

        $coas = COA::where('office_id', $officeId)->orderBy('kode_akun')->get();

        return view($this->views.'general-ledger', compact('reportData', 'startDate', 'endDate', 'coas'));
    }

    private function getBalanceSheetData($date)
    {
        $officeId = session('active_office_id');

        $movements = JournalDetail::query()
            ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
            ->where('journals.office_id', $officeId)
            ->where('journals.tgl_jurnal', '<=', $date)
            ->selectRaw('journal_details.akun_id, SUM(journal_details.debit) as total_debit, SUM(journal_details.kredit) as total_credit')
            ->groupBy('journal_details.akun_id')
            ->get()
            ->keyBy('akun_id');

        $groups = COAGroup::where('office_id', $officeId)
            ->with(['type' => function ($q) {
                $q->orderBy('nama_tipe');
            }, 'type.coas' => function ($q) {
                $q->orderBy('kode_akun');
            }])
            ->orderBy('kode_kelompok')
            ->get();

        $groups->each(function ($group) use ($movements) {
            $group->type->each(function ($type) use ($movements, $group) {
                $type->coas->each(function ($coa) use ($movements, $group) {
                    $m = $movements->get($coa->id);
                    $rawBalance = $m ? ($m->total_debit - $m->total_credit) : 0;
                    $isCreditNormal = in_array(substr($group->kode_kelompok, 0, 1), ['2', '3']);
                    $coa->balance = $isCreditNormal ? ($rawBalance * -1) : $rawBalance;
                });
                $type->total_balance = $type->coas->sum('balance');
            });
            $group->total_balance = $group->type->sum('total_balance');
        });

        $aktivaGroups = $groups->filter(fn ($g) => substr($g->kode_kelompok, 0, 1) == '1');
        $kewajibanGroups = $groups->filter(fn ($g) => substr($g->kode_kelompok, 0, 1) == '2');
        $modalGroups = $groups->filter(fn ($g) => substr($g->kode_kelompok, 0, 1) == '3');

        $startOfYear = date('Y-01-01', strtotime($date));

        $revenue = Invoice::where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->whereBetween('tgl_invoice', [$startOfYear, $date])
            ->sum('total_akhir');

        $cogs = Invoice::where('tipe_invoice', 'Purchase')
            ->where('office_id', $officeId)
            ->whereBetween('tgl_invoice', [$startOfYear, $date])
            ->sum('total_akhir');

        $expenses = Expense::where('office_id', $officeId)
            ->whereBetween('tgl_biaya', [$startOfYear, $date])
            ->sum('jumlah');

        $currentYearEarnings = $revenue - ($cogs + $expenses);

        return compact('aktivaGroups', 'kewajibanGroups', 'modalGroups', 'currentYearEarnings');
    }

    public function balanceSheet(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $data = $this->getBalanceSheetData($date);

        return view($this->views.'balance-sheet', array_merge($data, ['date' => $date]));
    }

    public function coaManagement(Request $request)
    {

        $officeId = session('active_office_id');
        $date = $request->input('date', date('Y-m-d'));
        $data = $this->getBalanceSheetData($date);

        $dropdownGroups = COAGroup::where('office_id', $officeId)->get();
        $dropdownTypes = COAType::whereHas('group', function ($q) use ($officeId) {
            $q->where('office_id', $officeId);
        })->get();

        $groups = COA::join('coa_type', 'chart_of_accounts.tipe_id', '=', 'coa_type.id')
            ->join('coa_group', 'coa_type.kelompok_id', '=', 'coa_group.id')
            ->where('chart_of_accounts.office_id', $officeId)
            ->select('chart_of_accounts.*', 'coa_type.nama_tipe', 'coa_group.nama_kelompok')
            ->get();

        $groupedAccounts = $groups->groupBy('nama_kelompok')->map(function ($items) {
            return $items->groupBy('nama_tipe');
        });

        return view($this->views.'coa-management', array_merge($data, [
            'date' => $date,
            'groupedAccounts' => $groupedAccounts,
            'dropdownGroups' => $dropdownGroups,
            'dropdownTypes' => $dropdownTypes,
        ]));
    }

    public function balanceSheetExportCSV(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $data = $this->getBalanceSheetData($date);

        return view($this->views.'export_balance_sheet', array_merge($data, ['date' => $date]));
    }

    public function profitAndLoss(Request $request)
    {
        $officeId = session('active_office_id');
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $targetGroups = [4000, 5000, 6000, 6800, 7001, 8001, 9000];

        $movements = JournalDetail::query()
            ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
            ->where('journals.office_id', $officeId)
            ->whereBetween('journals.tgl_jurnal', [$startDate, $endDate])
            ->selectRaw('journal_details.akun_id, SUM(journal_details.debit) as total_debit, SUM(journal_details.kredit) as total_credit')
            ->groupBy('journal_details.akun_id')
            ->get()
            ->keyBy('akun_id');

        $groups = COAGroup::with(['type.coas'])
            ->where('office_id', $officeId)
            ->whereIn('kode_kelompok', $targetGroups)
            ->get()
            ->map(function ($group) {
                return $group->type->flatMap(function ($type) use ($group) {
                    return $type->coas->map(function ($coa) use ($group) {
                        return (object) [
                            'kode_kelompok' => $group->kode_kelompok,
                            'nama_kelompok' => $group->nama_kelompok,
                            'coa_id' => $coa->id,
                            'kode_akun' => $coa->kode_akun,
                            'nama_akun' => $coa->nama_akun,
                        ];
                    });
                });
            })
            ->flatten(1);

        $report = [];
        $summary = [
            'total_revenue' => 0,
            'total_expense' => 0,
            'net_profit' => 0,
        ];

        foreach ($groups as $row) {
            $groupId = $row->kode_kelompok;

            if (! isset($report[$groupId])) {
                $report[$groupId] = [
                    'code' => $row->kode_kelompok,
                    'name' => $row->nama_kelompok,
                    'accounts' => [],
                    'total_balance' => 0,
                ];
            }

            $debit = 0;
            $credit = 0;
            if (isset($movements[$row->coa_id])) {
                $debit = $movements[$row->coa_id]->total_debit;
                $credit = $movements[$row->coa_id]->total_credit;
            }

            if (in_array($groupId, [4000, 7001])) {
                $balance = $credit - $debit;
            } else {
                $balance = $debit - $credit;
            }

            $report[$groupId]['accounts'][] = [
                'kode_akun' => $row->kode_akun,
                'nama_akun' => $row->nama_akun,
                'balance' => $balance,
            ];

            $report[$groupId]['total_balance'] += $balance;
        }

        foreach ($report as $groupId => $groupData) {
            if (in_array($groupId, [4000, 7001])) {
                $summary['total_revenue'] += $groupData['total_balance'];
            } else {
                $summary['total_expense'] += $groupData['total_balance'];
            }
        }

        $summary['net_profit'] = $summary['total_revenue'] - $summary['total_expense'];

        return view($this->views.'profit-loss', compact('report', 'summary', 'startDate', 'endDate'));
    }

    public function supplierInvoices(Request $request)
    {
        $officeId = session('active_office_id');

        // Build query for purchase invoices that are not fully paid
        $query = Invoice::with(['mitra', 'payment'])
            ->where('office_id', $officeId)
            ->where('tipe_invoice', 'Purchase')
            ->where('status_pembayaran', '!=', 'lunas')
            ->whereNull('deleted_at')
            ->whereHas('mitra', function ($q) {
                $q->whereIn('tipe_mitra', ['Supplier', 'Both']);
            });

        // Apply filters
        if ($request->mitra_id) {
            $query->where('mitra_id', $request->mitra_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tgl_invoice', [$request->start_date, $request->end_date]);
        }

        $invoices = $query->orderBy('tgl_invoice', 'desc')->get();

        // Group by supplier and calculate totals
        $suppliersData = collect();
        $grandTotalInvoice = 0;
        $grandTotalPayment = 0;

        foreach ($invoices as $invoice) {
            $totalPaid = $invoice->payment->sum('jumlah_bayar');
            $remaining = $invoice->total_akhir - $totalPaid;

            // Only include invoices with remaining balance
            if ($remaining > 0) {
                $supplierId = $invoice->mitra_id;

                if (! $suppliersData->has($supplierId)) {
                    $suppliersData->put($supplierId, [
                        'id' => $invoice->mitra->id,
                        'nama' => $invoice->mitra->nama,
                        'kota' => $invoice->mitra->kota ?? '-',
                        'total_invoice' => 0,
                        'total_paid' => 0,
                        'total_remaining' => 0,
                        'invoice_count' => 0,
                        'latest_invoice_date' => $invoice->tgl_invoice,
                    ]);
                }

                $supplierData = $suppliersData->get($supplierId);
                $supplierData['total_invoice'] += $invoice->total_akhir;
                $supplierData['total_paid'] += $totalPaid;
                $supplierData['total_remaining'] += $remaining;
                $supplierData['invoice_count'] += 1;

                // Update latest invoice date if this one is newer
                if ($invoice->tgl_invoice > $supplierData['latest_invoice_date']) {
                    $supplierData['latest_invoice_date'] = $invoice->tgl_invoice;
                }

                $grandTotalInvoice += $invoice->total_akhir;
                $grandTotalPayment += $totalPaid;
            }
        }

        // Sort by supplier name
        $suppliersData = $suppliersData->sortBy('nama');

        // Calculate grand totals
        $grandTotalRemaining = $grandTotalInvoice - $grandTotalPayment;

        return view($this->views.'supplier-invoices', compact('suppliersData', 'grandTotalInvoice', 'grandTotalPayment', 'grandTotalRemaining'));
    }

    public function supplierInvoicesDetail($id, Request $request)
    {
        $officeId = session('active_office_id');

        // Get supplier info
        $supplier = Partner::where('office_id', $officeId)
            ->where('id', $id)
            ->whereIn('tipe_mitra', ['Supplier', 'Both'])
            ->firstOrFail();

        // Build query for purchase invoices for this specific supplier
        $query = Invoice::with(['mitra', 'payment'])
            ->where('office_id', $officeId)
            ->where('tipe_invoice', 'Purchase')
            ->where('status_pembayaran', '!=', 'lunas')
            ->where('mitra_id', $id)
            ->whereNull('deleted_at');

        // Apply date filters
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tgl_invoice', [$request->start_date, $request->end_date]);
        }

        $invoices = $query->orderBy('tgl_invoice', 'desc')->get();

        // Process invoice data
        $invoiceData = collect();
        $totalInvoice = 0;
        $totalPayment = 0;

        foreach ($invoices as $invoice) {
            $totalPaid = $invoice->payment->sum('jumlah_bayar');
            $remaining = $invoice->total_akhir - $totalPaid;

            // Only include invoices with remaining balance
            if ($remaining > 0) {
                $invoiceData->push([
                    'id' => $invoice->id,
                    'nomor_invoice' => $invoice->nomor_invoice,
                    'tgl_invoice' => $invoice->tgl_invoice,
                    'tgl_jatuh_tempo' => $invoice->tgl_jatuh_tempo,
                    'total_akhir' => $invoice->total_akhir,
                    'total_paid' => $totalPaid,
                    'remaining' => $remaining,
                    'status_pembayaran' => $invoice->status_pembayaran,
                    'keterangan' => $invoice->keterangan,
                    'days_overdue' => $invoice->tgl_jatuh_tempo ?
                        Carbon::parse($invoice->tgl_jatuh_tempo)->diffInDays(now(), false) : 0,
                ]);

                $totalInvoice += $invoice->total_akhir;
                $totalPayment += $totalPaid;
            }
        }

        // Calculate totals
        $totalRemaining = $totalInvoice - $totalPayment;

        // Handle export
        if ($request->has('export') && $request->export == 'excel') {
            $filename = 'Detail_Tagihan_Supplier_'.str_replace(' ', '_', $supplier->nama).'_'.date('YmdHis').'.xlsx';

            return response()->streamDownload(function () use ($supplier, $invoiceData, $totalInvoice, $totalPayment, $totalRemaining) {
                echo view('Report.supplier-invoices-detail-excel', compact(
                    'supplier',
                    'invoiceData',
                    'totalInvoice',
                    'totalPayment',
                    'totalRemaining'
                ));
            }, $filename, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        }

        return view($this->views.'supplier-invoices-detail', compact(
            'supplier',
            'invoiceData',
            'totalInvoice',
            'totalPayment',
            'totalRemaining'
        ));
    }
}
