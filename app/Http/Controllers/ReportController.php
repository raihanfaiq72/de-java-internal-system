<?php

namespace App\Http\Controllers;

use App\Models\COA;
use App\Models\Invoice;
use App\Models\Partner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->with(['mitra', 'payment']);

        if ($request->mitra_id) {
            $query->where('mitra_id', $request->mitra_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tgl_invoice', [$request->start_date, $request->end_date]);
        }

        $invoices = $query->get();

        $agingData = [];
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

        foreach ($grouped as $mid => $invs) {
            $mitraName = $invs->first()->mitra->nama ?? 'Unknown';
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
                $paid = $inv->payment->sum('jumlah_bayar');
                $remaining = $inv->total_akhir - $paid;

                // STRICTLY > 0 as per requirement "sisa_piutang > 0"
                if ($remaining <= 0) {
                    continue;
                }

                // Use tgl_jatuh_tempo, fallback to tgl_invoice
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

            if ($mitraTotal > 0) {
                $avgDays = $mitraOverdueCount > 0 ? $mitraTotalDaysOverdue / $mitraOverdueCount : 0;

                $agingData[] = [
                    'mitra_name' => $mitraName,
                    'avg_days' => $avgDays,
                    'count' => $mitraInvoicesCount,
                    'total' => $mitraTotal,
                    'buckets' => $buckets,
                ];

                $summary['total_customers']++;
                $summary['total_invoices'] += $mitraInvoicesCount;
                $summary['total_amount'] += $mitraTotal;
                $sumAvgDays += $avgDays;

                foreach ($buckets as $key => $val) {
                    $summary['buckets'][$key] += $val;
                }
            }
        }

        if ($summary['total_customers'] > 0) {
            $summary['avg_days_past_due'] = $sumAvgDays / $summary['total_customers'];
        }

        // Sort data by Total Piutang Descending
        usort($agingData, fn ($a, $b) => $b['total'] <=> $a['total']);

        return view($this->views.'ar-aging', compact('agingData', 'summary', 'mitras'));
    }

    public function salesReport()
    {
        $year = date('Y');
        $officeId = session('active_office_id');

        // Stats
        $stats = DB::table('invoices')
            ->where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->whereNull('deleted_at')
            ->select(
                DB::raw('SUM(total_akhir) as total_revenue'),
                DB::raw('SUM(CASE WHEN status_pembayaran != "Paid" THEN (total_akhir - COALESCE((SELECT SUM(jumlah_bayar) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL), 0)) ELSE 0 END) as total_ar'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN tgl_jatuh_tempo < CURDATE() AND status_pembayaran != "Paid" THEN (total_akhir - COALESCE((SELECT SUM(jumlah_bayar) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL), 0)) ELSE 0 END) as total_overdue')
            )->first();

        // Monthly Data
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = DB::table('invoices')
                ->where('tipe_invoice', 'Sales')
                ->where('office_id', $officeId)
                ->whereYear('tgl_invoice', $year)
                ->whereMonth('tgl_invoice', $m)
                ->whereNull('deleted_at')
                ->sum('total_akhir');
        }

        // Top Clients
        $topClients = DB::table('invoices')
            ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->where('invoices.tipe_invoice', 'Sales')
            ->where('invoices.office_id', $officeId)
            ->whereNull('invoices.deleted_at')
            ->select('mitras.nama', DB::raw('SUM(total_akhir) as value'))
            ->groupBy('mitras.id', 'mitras.nama')
            ->orderByDesc('value')
            ->limit(5)
            ->get();

        return view($this->views.'sales', compact('stats', 'monthlyData', 'topClients'));
    }

    public function purchaseReport()
    {
        $year = date('Y');
        $officeId = session('active_office_id');

        // Stats
        $stats = DB::table('invoices')
            ->where('tipe_invoice', 'Purchase')
            ->where('office_id', $officeId)
            ->whereNull('deleted_at')
            ->select(
                DB::raw('SUM(total_akhir) as total_spending'),
                DB::raw('SUM(CASE WHEN status_pembayaran != "Paid" THEN (total_akhir - COALESCE((SELECT SUM(jumlah_bayar) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL), 0)) ELSE 0 END) as total_ap'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN tgl_jatuh_tempo < CURDATE() AND status_pembayaran != "Paid" THEN (total_akhir - COALESCE((SELECT SUM(jumlah_bayar) FROM payments WHERE payments.invoice_id = invoices.id AND payments.deleted_at IS NULL), 0)) ELSE 0 END) as total_overdue')
            )->first();

        // Monthly Data
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = DB::table('invoices')
                ->where('tipe_invoice', 'Purchase')
                ->where('office_id', $officeId)
                ->whereYear('tgl_invoice', $year)
                ->whereMonth('tgl_invoice', $m)
                ->whereNull('deleted_at')
                ->sum('total_akhir');
        }

        // Top Suppliers
        $topSuppliers = DB::table('invoices')
            ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->where('invoices.tipe_invoice', 'Purchase')
            ->where('invoices.office_id', $officeId)
            ->whereNull('invoices.deleted_at')
            ->select('mitras.nama', DB::raw('SUM(total_akhir) as value'))
            ->groupBy('mitras.id', 'mitras.nama')
            ->orderByDesc('value')
            ->limit(5)
            ->get();

        return view($this->views.'purchase', compact('stats', 'monthlyData', 'topSuppliers'));
    }

    private function getStockData(Request $request)
    {
        $officeId = session('active_office_id');

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        $query = DB::table('products')
            ->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->leftJoin('stock_mutations', function ($join) use ($officeId, $request) {
                $join->on('products.id', '=', 'stock_mutations.product_id')
                    ->where('stock_mutations.office_id', '=', $officeId)
                    ->whereNull('stock_mutations.deleted_at');

                if ($request->location_id) {
                    $join->where('stock_mutations.stock_location_id', $request->location_id);
                }
            })
            ->where('products.office_id', $officeId)
            ->whereNull('products.deleted_at')
            ->select(
                'products.id',
                'products.nama_produk',
                'products.sku_kode',
                'products.satuan',
                'product_categories.nama_kategori',
                DB::raw("SUM(CASE 
                    WHEN stock_mutations.created_at < '$startDate' AND stock_mutations.type = 'IN' THEN stock_mutations.qty 
                    WHEN stock_mutations.created_at < '$startDate' AND stock_mutations.type = 'OUT' THEN -stock_mutations.qty 
                    ELSE 0 END) as opening_qty"),
                DB::raw("SUM(CASE 
                    WHEN stock_mutations.created_at BETWEEN '$startDate' AND '$endDate' AND stock_mutations.type = 'IN' THEN stock_mutations.qty 
                    ELSE 0 END) as qty_in"),
                DB::raw("SUM(CASE 
                    WHEN stock_mutations.created_at BETWEEN '$startDate' AND '$endDate' AND stock_mutations.type = 'OUT' THEN stock_mutations.qty 
                    ELSE 0 END) as qty_out"),
                DB::raw("SUM(CASE 
                    WHEN stock_mutations.created_at < '$startDate' AND stock_mutations.type = 'IN' THEN stock_mutations.qty * stock_mutations.cost_price
                    WHEN stock_mutations.created_at < '$startDate' AND stock_mutations.type = 'OUT' THEN -stock_mutations.qty * stock_mutations.cost_price
                    ELSE 0 END) as opening_value"),
                DB::raw("SUM(CASE 
                    WHEN stock_mutations.created_at BETWEEN '$startDate' AND '$endDate' AND stock_mutations.type = 'IN' THEN stock_mutations.qty * stock_mutations.cost_price
                    ELSE 0 END) as value_in"),
                DB::raw("SUM(CASE 
                    WHEN stock_mutations.created_at BETWEEN '$startDate' AND '$endDate' AND stock_mutations.type = 'OUT' THEN stock_mutations.qty * stock_mutations.cost_price
                    ELSE 0 END) as value_out")
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

        $categories = DB::table('product_categories')->where('office_id', $officeId)->get();
        $locations = DB::table('stock_locations')->where('office_id', $officeId)->get();
        $allProducts = DB::table('products')->where('office_id', $officeId)->select('id', 'nama_produk')->get();

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

        $fileName = 'laporan-stok-'.date('Y-m-d-His').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Produk', 'Kategori', 'Unit', 'Qty Awal', 'Qty Masuk', 'Qty Keluar', 'Qty Akhir', 'Nilai Awal', 'Nilai Masuk', 'Nilai Keluar', 'Nilai Akhir'];

        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                $row['Produk'] = $product->nama_produk.($product->sku_kode ? ' ('.$product->sku_kode.')' : '');
                $row['Kategori'] = $product->nama_kategori;
                $row['Unit'] = $product->satuan;
                $row['Qty Awal'] = $product->opening_qty;
                $row['Qty Masuk'] = $product->qty_in;
                $row['Qty Keluar'] = $product->qty_out;
                $row['Qty Akhir'] = $product->closing_qty;
                $row['Nilai Awal'] = $product->opening_value;
                $row['Nilai Masuk'] = $product->value_in;
                $row['Nilai Keluar'] = $product->value_out;
                $row['Nilai Akhir'] = $product->closing_value;

                fputcsv($file, [
                    $row['Produk'],
                    $row['Kategori'],
                    $row['Unit'],
                    $row['Qty Awal'],
                    $row['Qty Masuk'],
                    $row['Qty Keluar'],
                    $row['Qty Akhir'],
                    $row['Nilai Awal'],
                    $row['Nilai Masuk'],
                    $row['Nilai Keluar'],
                    $row['Nilai Akhir'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function generalLedger(Request $request)
    {
        $officeId = session('active_office_id');
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');
        $coaId = $request->coa_id;
        $status = $request->status;

        $query = DB::table('journal_details')
            ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
            ->join('chart_of_accounts', 'journal_details.akun_id', '=', 'chart_of_accounts.id')
            ->leftJoin('invoices', function ($join) use ($officeId) {
                $join->on('journals.nomor_referensi', '=', 'invoices.nomor_invoice')
                    ->where('invoices.office_id', '=', $officeId)
                    ->whereNull('invoices.deleted_at');
            })
            ->where('journals.office_id', $officeId)
            ->whereNull('journals.deleted_at')
            ->whereNull('journal_details.deleted_at')
            ->whereBetween('journals.tgl_jurnal', [$startDate, $endDate])
            ->select(
                'journals.tgl_jurnal',
                'journals.keterangan',
                'journals.nomor_referensi',
                'journal_details.nomor_journal',
                'journal_details.debit',
                'journal_details.kredit',
                'chart_of_accounts.id as coa_id',
                'chart_of_accounts.kode_akun',
                'chart_of_accounts.nama_akun',
                'invoices.status_dok'
            );

        if ($coaId) {
            $query->where('journal_details.akun_id', $coaId);
        }

        if ($status) {
            $query->where('invoices.status_dok', $status);
        }

        $journalLines = $query->orderBy('journals.tgl_jurnal')->orderBy('journals.id')->get();

        $groupedData = $journalLines->groupBy('coa_id');

        $reportData = [];
        foreach ($groupedData as $id => $lines) {
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

                $line->balance = $lineBalance; // Row balance

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

        // Get all COAs for filter
        $coas = COA::where('office_id', $officeId)->orderBy('kode_akun')->get();

        return view($this->views.'general-ledger', compact('reportData', 'startDate', 'endDate', 'coas'));
    }

    private function getBalanceSheetData($date)
    {
        $officeId = session('active_office_id');

        // Helper to calculate balance as of date
        $getBalance = function ($accountId) use ($officeId, $date) {
            $balance = DB::table('journal_details')
                ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
                ->where('journal_details.akun_id', $accountId)
                ->where('journals.office_id', $officeId)
                ->where('journals.tgl_jurnal', '<=', $date)
                ->whereNull('journal_details.deleted_at')
                ->whereNull('journals.deleted_at')
                ->select(DB::raw('SUM(debit) - SUM(kredit) as bal'))
                ->first()->bal ?? 0;

            return $balance;
        };

        // Fetch structure with eager loading
        // Note: COAGroup relationship is named 'type', not 'types'
        $groups = \App\Models\COAGroup::where('office_id', $officeId)
            ->with(['type' => function ($q) {
                $q->orderBy('nama_tipe');
            }, 'type.coas' => function ($q) {
                $q->orderBy('kode_akun');
            }])
            ->orderBy('kode_kelompok')
            ->get();

        // Process Balances
        $groups->each(function ($group) use ($getBalance) {
            $group->type->each(function ($type) use ($getBalance, $group) {
                $type->coas->each(function ($coa) use ($getBalance, $group) {
                    $rawBalance = $getBalance($coa->id);
                    // Adjust sign: Assets (1) = Normal Debit (+), Liability (2)/Equity (3) = Normal Credit (-)
                    // If group starts with 2 or 3, we flip the sign so credit balances appear positive
                    $isCreditNormal = in_array(substr($group->kode_kelompok, 0, 1), ['2', '3']);
                    $coa->balance = $isCreditNormal ? ($rawBalance * -1) : $rawBalance;
                });
                // Remove COAs with 0 balance if desired, but usually we keep them or filter in view
                // For now, let's keep them but maybe calculate type totals
                $type->total_balance = $type->coas->sum('balance');
            });
            $group->total_balance = $group->type->sum('total_balance');
        });

        $aktivaGroups = $groups->filter(fn ($g) => substr($g->kode_kelompok, 0, 1) == '1');
        $kewajibanGroups = $groups->filter(fn ($g) => substr($g->kode_kelompok, 0, 1) == '2');
        $modalGroups = $groups->filter(fn ($g) => substr($g->kode_kelompok, 0, 1) == '3');

        // Calculate Current Year Profit (Laba Tahun Berjalan)
        $startOfYear = date('Y-01-01', strtotime($date));

        $revenue = DB::table('invoices')
            ->where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->whereBetween('tgl_invoice', [$startOfYear, $date])
            ->whereNull('deleted_at')
            ->sum('total_akhir');

        $cogs = DB::table('invoices')
            ->where('tipe_invoice', 'Purchase')
            ->where('office_id', $officeId)
            ->whereBetween('tgl_invoice', [$startOfYear, $date])
            ->whereNull('deleted_at')
            ->sum('total_akhir');

        $expenses = DB::table('expenses')
            ->where('office_id', $officeId)
            ->whereBetween('tgl_biaya', [$startOfYear, $date])
            ->whereNull('deleted_at')
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
        $date = $request->input('date', date('Y-m-d'));
        $data = $this->getBalanceSheetData($date);

        $officeId = session('active_office_id');

        $groups = \DB::table('coa_group as g')
            ->join('coa_type as t', 't.kelompok_id', '=', 'g.id')
            ->join('chart_of_accounts as c', 'c.tipe_id', '=', 't.id')
            ->where('g.office_id', $officeId)
            ->select(
                'g.id as kelompok_id',
                'g.kode_kelompok',
                'g.nama_kelompok',

                't.id as tipe_id',
                't.nama_tipe',

                'c.id as coa_id',
                'c.kode_akun',
                'c.nama_akun'
            )
            ->orderBy('g.kode_kelompok')
            ->orderBy('t.nama_tipe')
            ->orderBy('c.kode_akun')
            ->get();

        // Prepare dropdown data
        $dropdownGroups = \App\Models\COAGroup::where('office_id', $officeId)->get();
        // Types depend on Group, but for simplicity let's pass all types and filter in JS or just pass all.
        // Actually, better to fetch types via AJAX when group changes, OR pass all types with group_id attribute.
        $dropdownTypes = \App\Models\COAType::whereHas('group', function ($q) use ($officeId) {
            $q->where('office_id', $officeId);
        })->get();

        $groupedAccounts = $groups->groupBy('nama_kelompok') // LEVEL 1
            ->map(function ($items) {
                return $items->groupBy('nama_tipe'); // LEVEL 2
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

        $fileName = 'neraca-keuangan-'.date('Y-m-d-His').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($data, $date) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Neraca Keuangan']);
            fputcsv($file, ['Per Tanggal', $date]);
            fputcsv($file, []);

            // Aktiva
            fputcsv($file, ['AKTIVA']);
            foreach ($data['aktivaGroups'] as $group) {
                // Check if group has any types/accounts or balance
                if ($group->type->isNotEmpty()) {
                    fputcsv($file, [$group->nama_kelompok]);
                    foreach ($group->type as $type) {
                        if ($type->coas->isNotEmpty()) {
                            // Indent Type
                            fputcsv($file, ['', $type->nama_tipe]);
                            foreach ($type->coas as $acc) {
                                fputcsv($file, [$acc->kode_akun, $acc->nama_akun, $acc->balance]);
                            }
                        }
                    }
                    fputcsv($file, ['Total '.$group->nama_kelompok, '', $group->total_balance]);
                    fputcsv($file, []);
                }
            }

            // Kewajiban
            fputcsv($file, ['KEWAJIBAN']);
            foreach ($data['kewajibanGroups'] as $group) {
                if ($group->type->isNotEmpty()) {
                    fputcsv($file, [$group->nama_kelompok]);
                    foreach ($group->type as $type) {
                        if ($type->coas->isNotEmpty()) {
                            fputcsv($file, ['', $type->nama_tipe]);
                            foreach ($type->coas as $acc) {
                                fputcsv($file, [$acc->kode_akun, $acc->nama_akun, $acc->balance]);
                            }
                        }
                    }
                    fputcsv($file, ['Total '.$group->nama_kelompok, '', $group->total_balance]);
                    fputcsv($file, []);
                }
            }

            // Modal
            fputcsv($file, ['MODAL']);
            foreach ($data['modalGroups'] as $group) {
                if ($group->type->isNotEmpty()) {
                    fputcsv($file, [$group->nama_kelompok]);
                    foreach ($group->type as $type) {
                        if ($type->coas->isNotEmpty()) {
                            fputcsv($file, ['', $type->nama_tipe]);
                            foreach ($type->coas as $acc) {
                                fputcsv($file, [$acc->kode_akun, $acc->nama_akun, $acc->balance]);
                            }
                        }
                    }
                    fputcsv($file, ['Total '.$group->nama_kelompok, '', $group->total_balance]);
                    fputcsv($file, []);
                }
            }

            // Laba Tahun Berjalan
            fputcsv($file, ['Laba Tahun Berjalan']);
            fputcsv($file, ['3202', 'Laba Tahun Berjalan', $data['currentYearEarnings']]);
            fputcsv($file, []);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function profitAndLoss(Request $request)
    {
        $officeId = session('active_office_id');
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        // Target Groups
        // 4000, 7001 -> Revenue (Credit - Debit)
        // 5000, 6000, 6800, 8001, 9000 -> Expense (Debit - Credit)
        $targetGroups = [4000, 5000, 6000, 6800, 7001, 8001, 9000];

        // 1. Get Transaction Sums per Account for the period
        $movements = DB::table('journal_details')
            ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
            ->where('journals.office_id', $officeId)
            ->whereNull('journals.deleted_at')
            ->whereNull('journal_details.deleted_at')
            ->whereBetween('journals.tgl_jurnal', [$startDate, $endDate])
            ->select(
                'journal_details.akun_id',
                DB::raw('SUM(journal_details.debit) as total_debit'),
                DB::raw('SUM(journal_details.kredit) as total_credit')
            )
            ->groupBy('journal_details.akun_id')
            ->get()
            ->keyBy('akun_id');

        // 2. Get COA Structure filtered by groups
        $groups = DB::table('coa_group')
            ->join('coa_type', 'coa_group.id', '=', 'coa_type.kelompok_id')
            ->join('chart_of_accounts', 'coa_type.id', '=', 'chart_of_accounts.tipe_id')
            ->where('coa_group.office_id', $officeId)
            ->whereIn('coa_group.kode_kelompok', $targetGroups)
            ->whereNull('coa_group.deleted_at')
            ->whereNull('coa_type.deleted_at')
            ->whereNull('chart_of_accounts.deleted_at')
            ->select(
                'coa_group.kode_kelompok',
                'coa_group.nama_kelompok',
                'chart_of_accounts.id as coa_id',
                'chart_of_accounts.kode_akun',
                'chart_of_accounts.nama_akun'
            )
            ->orderBy('coa_group.kode_kelompok')
            ->orderBy('chart_of_accounts.kode_akun')
            ->get();

        // 3. Structure Data
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

            // Calculate Balance
            $debit = 0;
            $credit = 0;

            if (isset($movements[$row->coa_id])) {
                $debit = $movements[$row->coa_id]->total_debit;
                $credit = $movements[$row->coa_id]->total_credit;
            }

            // Determine sign based on group
            // Revenue (4000, 7001): Credit - Debit
            // Expense (Others): Debit - Credit
            if (in_array($groupId, [4000, 7001])) {
                $balance = $credit - $debit;
            } else {
                $balance = $debit - $credit;
            }

            // Only add if non-zero?
            // User: "Saldo per akun dalam periode terpilih".
            // If strictly 0, maybe hide?
            // Let's show all for now, clearer.

            $report[$groupId]['accounts'][] = [
                'kode_akun' => $row->kode_akun,
                'nama_akun' => $row->nama_akun,
                'balance' => $balance,
            ];

            $report[$groupId]['total_balance'] += $balance;
        }

        // 4. Calculate Summary
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
}
