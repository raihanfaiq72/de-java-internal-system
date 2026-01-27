<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Mitra;

class ReportController extends Controller
{
    private $views = 'Report.';

    public function arAging(Request $request)
    {
        $officeId = session('active_office_id');
        $mitras = Mitra::where('office_id', $officeId)->where('tipe_mitra', '!=', 'Vendor')->get();
        
        $query = Invoice::where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->where('status_pembayaran', '!=', 'Paid')
            ->whereNull('deleted_at')
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
            ]
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
                
                if ($remaining <= 100) continue; // Ignore very small amounts (rounding errors)
                
                $dueDate = Carbon::parse($inv->tgl_jatuh_tempo);
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
                    'buckets' => $buckets
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

        return view($this->views . 'ar-aging', compact('agingData', 'summary', 'mitras'));
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

        return view($this->views . 'sales', compact('stats', 'monthlyData', 'topClients'));
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

        return view($this->views . 'purchase', compact('stats', 'monthlyData', 'topSuppliers'));
    }

    private function getStockData(Request $request)
    {
        $officeId = session('active_office_id');

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        $query = DB::table('products')
            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
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
                'units.nama_unit',
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
            ->groupBy('products.id', 'products.nama_produk', 'products.sku_kode', 'units.nama_unit', 'product_categories.nama_kategori');

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

        return view($this->views . 'stock', compact('products', 'stats', 'categories', 'locations', 'allProducts'));
    }

    public function stockReportExport(Request $request)
    {
        $products = $this->getStockData($request);

        $fileName = 'laporan-stok-' . date('Y-m-d-His') . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Produk', 'Kategori', 'Unit', 'Qty Awal', 'Qty Masuk', 'Qty Keluar', 'Qty Akhir', 'Nilai Awal', 'Nilai Masuk', 'Nilai Keluar', 'Nilai Akhir');

        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                $row['Produk'] = $product->nama_produk . ($product->sku_kode ? ' (' . $product->sku_kode . ')' : '');
                $row['Kategori'] = $product->nama_kategori;
                $row['Unit'] = $product->nama_unit;
                $row['Qty Awal'] = $product->opening_qty;
                $row['Qty Masuk'] = $product->qty_in;
                $row['Qty Keluar'] = $product->qty_out;
                $row['Qty Akhir'] = $product->closing_qty;
                $row['Nilai Awal'] = $product->opening_value;
                $row['Nilai Masuk'] = $product->value_in;
                $row['Nilai Keluar'] = $product->value_out;
                $row['Nilai Akhir'] = $product->closing_value;

                fputcsv($file, array(
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
                    $row['Nilai Akhir']
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function generalLedger()
    {
        return view($this->views . 'general-ledger');
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
        
        $accounts = DB::table('chart_of_accounts')
            ->select('id', 'kode_akun', 'nama_akun')
            ->get()
            ->map(function($acc) use ($getBalance) {
                $acc->balance = $getBalance($acc->id);
                $acc->type = substr($acc->kode_akun, 0, 1);
                return $acc;
            });

        $aktivaAccounts = $accounts->filter(fn($a) => $a->type == '1');
        
        $aktivaGroups = [
            'Kas' => $aktivaAccounts->filter(fn($a) => substr($a->kode_akun, 0, 2) == '11')->values(),
            'Bank' => $aktivaAccounts->filter(fn($a) => substr($a->kode_akun, 0, 2) == '12')->values(),
            'Piutang Usaha' => $aktivaAccounts->filter(fn($a) => substr($a->kode_akun, 0, 2) == '13')->values(),
            'Persediaan' => $aktivaAccounts->filter(fn($a) => in_array(substr($a->kode_akun, 0, 2), ['14', '15']))->values(),
            'Pajak Dibayar Di Muka' => $aktivaAccounts->filter(fn($a) => substr($a->kode_akun, 0, 2) == '17')->values(),
            'Aktiva Tetap' => $aktivaAccounts->filter(fn($a) => substr($a->kode_akun, 0, 2) == '18' && substr($a->kode_akun, 0, 3) != '185')->values(),
            'Akumulasi Penyusutan' => $aktivaAccounts->filter(fn($a) => substr($a->kode_akun, 0, 3) == '185')->values(),
        ];

        $kewajibanAccounts = $accounts->filter(fn($a) => $a->type == '2')->map(function($a) {
            $a->balance = $a->balance * -1;
            return $a;
        });
        
        $kewajibanGroups = [
            'Hutang Usaha' => $kewajibanAccounts->filter(fn($a) => substr($a->kode_akun, 0, 2) == '21')->values(),
            'Hutang Non-Usaha' => $kewajibanAccounts->filter(fn($a) => substr($a->kode_akun, 0, 2) == '22')->values(),
            'Hutang Pajak' => $kewajibanAccounts->filter(fn($a) => substr($a->kode_akun, 0, 2) == '23')->values(),
            'Kewajiban Jangka Panjang' => $kewajibanAccounts->filter(fn($a) => substr($a->kode_akun, 0, 2) == '24')->values(),
        ];
        
        $modalAccountsList = $accounts->filter(fn($a) => $a->type == '3' && $a->kode_akun != '3202')->map(function($a) {
            $a->balance = $a->balance * -1;
            return $a;
        });

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
        
        $labaTahunBerjalan = (object)[
            'kode_akun' => '3202',
            'nama_akun' => 'Laba Tahun Berjalan',
            'balance' => $currentYearEarnings,
            'type' => '3'
        ];

        $modalGroups = [
            'Modal' => $modalAccountsList->filter(fn($a) => in_array(substr($a->kode_akun, 0, 2), ['31', '33', '39']))->values(),
            'Laba' => $modalAccountsList->filter(fn($a) => substr($a->kode_akun, 0, 2) == '32')->push($labaTahunBerjalan)->values(),
        ];

        return compact('aktivaGroups', 'kewajibanGroups', 'modalGroups', 'currentYearEarnings');
    }

    public function balanceSheet(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $data = $this->getBalanceSheetData($date);
        
        return view($this->views . 'balance-sheet', array_merge($data, ['date' => $date]));
    }

    public function coaManagement(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $data = $this->getBalanceSheetData($date);
        
        // Build dynamic top-level groups by first digit to support up to 9
        $officeId = session('active_office_id');
        $accounts = \DB::table('chart_of_accounts')
            ->select('id', 'kode_akun', 'nama_akun')
            ->where('office_id', $officeId)
            ->get()
            ->map(function($acc) {
                $acc->top = substr($acc->kode_akun, 0, 1);
                return $acc;
            });

        $topGroups = [];
        foreach (range(1,9) as $d) {
            $group = $accounts->filter(fn($a) => $a->top == (string)$d)->values();
            if ($group->isNotEmpty()) {
                $topGroups[(string)$d] = $group;
            }
        }

        return view($this->views . 'coa-management', array_merge($data, [
            'date' => $date,
            'topGroups' => $topGroups
        ]));
    }

    public function balanceSheetExportCSV(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $data = $this->getBalanceSheetData($date);
        
        $fileName = 'neraca-keuangan-' . date('Y-m-d-His') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($data, $date) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Neraca Keuangan']);
            fputcsv($file, ['Per Tanggal', $date]);
            fputcsv($file, []);
            
            // Aktiva
            fputcsv($file, ['AKTIVA']);
            foreach ($data['aktivaGroups'] as $groupName => $accounts) {
                if ($accounts->isNotEmpty()) {
                    fputcsv($file, [$groupName]);
                    foreach ($accounts as $acc) {
                        fputcsv($file, [$acc->kode_akun, $acc->nama_akun, $acc->balance]);
                    }
                    fputcsv($file, ['Total ' . $groupName, '', $accounts->sum('balance')]);
                    fputcsv($file, []);
                }
            }
            
            // Kewajiban
            fputcsv($file, ['KEWAJIBAN']);
            foreach ($data['kewajibanGroups'] as $groupName => $accounts) {
                if ($accounts->isNotEmpty()) {
                    fputcsv($file, [$groupName]);
                    foreach ($accounts as $acc) {
                        fputcsv($file, [$acc->kode_akun, $acc->nama_akun, $acc->balance]);
                    }
                    fputcsv($file, ['Total ' . $groupName, '', $accounts->sum('balance')]);
                    fputcsv($file, []);
                }
            }
            
            // Modal
            fputcsv($file, ['MODAL']);
            foreach ($data['modalGroups'] as $groupName => $accounts) {
                if ($accounts->isNotEmpty()) {
                    fputcsv($file, [$groupName]);
                    foreach ($accounts as $acc) {
                        fputcsv($file, [$acc->kode_akun, $acc->nama_akun, $acc->balance]);
                    }
                    fputcsv($file, ['Total ' . $groupName, '', $accounts->sum('balance')]);
                    fputcsv($file, []);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function profitAndLoss()
    {
        $year = date('Y');
        $officeId = session('active_office_id');
        
        $revenue = DB::table('invoices')
            ->where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->whereYear('tgl_invoice', $year)
            ->whereNull('deleted_at')
            ->sum('total_akhir');

        $cogs = DB::table('invoices')
            ->where('tipe_invoice', 'Purchase')
            ->where('office_id', $officeId)
            ->whereYear('tgl_invoice', $year)
            ->whereNull('deleted_at')
            ->sum('total_akhir');

        $expenses = DB::table('expenses')
            ->where('office_id', $officeId)
            ->whereYear('tgl_biaya', $year)
            ->whereNull('deleted_at')
            ->select('nama_biaya', DB::raw('SUM(jumlah) as total'))
            ->groupBy('nama_biaya')
            ->get();

        $totalExpenses = $expenses->sum('total');
        $grossProfit = $revenue - $cogs;
        $netIncome = $grossProfit - $totalExpenses;

        return view($this->views . 'profit-loss', compact('revenue', 'cogs', 'expenses', 'totalExpenses', 'grossProfit', 'netIncome', 'year'));
    }
}
