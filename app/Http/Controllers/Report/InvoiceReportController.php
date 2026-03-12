<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Partner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceReportController extends Controller
{
    public function index(Request $request)
    {
        $officeId = session('active_office_id');
        $startDate = $request->start_date ?? date('Y-01-01');
        $endDate = $request->end_date ?? date('Y-12-31');
        $mitraId = $request->mitra_id;
        $search = $request->search;

        if ($request->ajax()) {
            return $this->getDataForAjax($request, $officeId);
        }

        // --- Tab 1: Rekap Umum (Charts) ---
        // 1. Line Chart: Income vs Expenditure (Monthly for the selected year)
        // Uses the year from start_date
        $year = Carbon::parse($startDate)->year;

        // Income (Sales Payments)
        $incomeData = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.office_id', $officeId)
            ->where('invoices.tipe_invoice', 'Sales')
            ->whereYear('payments.tgl_pembayaran', $year)
            ->whereNull('payments.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->select(DB::raw('MONTH(payments.tgl_pembayaran) as month'), DB::raw('SUM(payments.jumlah_bayar) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')->toArray();

        // Expenditure (Purchase Payments)
        $expenseData = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.office_id', $officeId)
            ->where('invoices.tipe_invoice', 'Purchase')
            ->whereYear('payments.tgl_pembayaran', $year)
            ->whereNull('payments.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->select(DB::raw('MONTH(payments.tgl_pembayaran) as month'), DB::raw('SUM(payments.jumlah_bayar) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')->toArray();

        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartIncome = [];
        $chartExpense = [];

        for ($m = 1; $m <= 12; $m++) {
            $chartIncome[] = $incomeData[$m] ?? 0;
            $chartExpense[] = $expenseData[$m] ?? 0;
        }

        // 2. Pie Chart: Receivables Composition (Sales)
        // Outstanding = Total Sales - Total Received
        // Received = Total Received
        // Filter: Sales Invoices

        $totalSales = DB::table('invoices')
            ->where('office_id', $officeId)
            ->where('tipe_invoice', 'Sales')
            ->whereNull('deleted_at')
            ->sum('total_akhir');

        $totalReceived = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.office_id', $officeId)
            ->where('invoices.tipe_invoice', 'Sales')
            ->whereNull('payments.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->sum('payments.jumlah_bayar');

        $outstanding = $totalSales - $totalReceived;
        if ($outstanding < 0) {
            $outstanding = 0;
        }

        $pieSeries = [(float) $outstanding, (float) $totalReceived]; // [Outstanding, Received]
        $pieLabels = ['Outstanding Amount', 'Amount Received'];

        // --- Tab 2: Rekap Pembayaran ---
        $paymentsQuery = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->leftJoin('mitras', 'invoices.mitra_id', '=', 'mitras.id') // Changed to leftJoin
            ->where('invoices.office_id', $officeId)
            ->whereNull('payments.deleted_at')
            ->whereBetween('payments.tgl_pembayaran', [$startDate, $endDate])
            ->select(
                'payments.tgl_pembayaran',
                'invoices.nomor_invoice',
                'payments.metode_pembayaran',
                'mitras.nomor_mitra',
                'mitras.nama as nama_mitra',
                'payments.jumlah_bayar',
                'payments.nomor_pembayaran'
            );

        if ($mitraId) {
            $paymentsQuery->where('invoices.mitra_id', $mitraId);
        }
        if ($search) {
            $paymentsQuery->where(function ($q) use ($search) {
                $q->where('invoices.nomor_invoice', 'like', "%$search%")
                    ->orWhere('mitras.nama', 'like', "%$search%")
                    ->orWhere('mitras.nomor_mitra', 'like', "%$search%")
                    ->orWhere('payments.nomor_pembayaran', 'like', "%$search%");
            });
        }

        $allPayments = $paymentsQuery->orderBy('payments.tgl_pembayaran', 'desc')->get();
        $totalPaymentAmount = $allPayments->sum('jumlah_bayar');
        $totalUniqueInvoices = $allPayments->unique('nomor_invoice')->count();
        $payments = $allPayments;

        // --- Tab 3: Laporan Produk Terjual ---
        // Filter Invoices based on Payments in range
        $invoiceIdsQuery = DB::table('invoices')
            ->join('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->where('invoices.office_id', $officeId)
            ->where('invoices.tipe_invoice', 'Sales')
            ->whereNull('payments.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->whereBetween('payments.tgl_pembayaran', [$startDate, $endDate]);

        if ($mitraId) {
            $invoiceIdsQuery->where('invoices.mitra_id', $mitraId);
        }
        if ($search) {
            $invoiceIdsQuery->leftJoin('mitras', 'invoices.mitra_id', '=', 'mitras.id') // Changed to leftJoin
                ->where(function ($q) use ($search) {
                    $q->where('invoices.nomor_invoice', 'like', "%$search%")
                        ->orWhere('mitras.nama', 'like', "%$search%")
                        ->orWhere('mitras.nomor_mitra', 'like', "%$search%")
                        ->orWhere('payments.nomor_pembayaran', 'like', "%$search%");
                });
        }

        $invoiceIds = $invoiceIdsQuery->pluck('invoices.id')->unique();

        $soldProducts = DB::table('invoice_items')
            ->join('products', 'invoice_items.produk_id', '=', 'products.id')
            ->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->whereIn('invoice_items.invoice_id', $invoiceIds)
            ->whereNull('invoice_items.deleted_at')
            ->select(
                'products.nama_produk',
                'products.sku_kode',
                'products.satuan',
                'product_categories.nama_kategori',
                DB::raw('SUM(invoice_items.qty) as total_qty')
            )
            ->groupBy('products.id', 'products.nama_produk', 'products.sku_kode', 'products.satuan', 'product_categories.nama_kategori')
            ->get();

        $totalSoldQty = $soldProducts->sum('total_qty');

        // --- Tab 4: Laporan Invoice Per Produk ---
        $prodIdFilter = $request->input('product_id', []);
        if (! is_array($prodIdFilter)) {
            $prodIdFilter = [$prodIdFilter];
        }
        $prodIdFilter = array_values(array_filter($prodIdFilter, function ($v) {
            return $v !== null && $v !== '';
        }));
        $invTypeFilter = $request->invoice_type;
        $payStatusFilter = $request->payment_status;

        $itemsQuery = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.produk_id', '=', 'products.id')
            ->leftJoin('mitras', 'invoices.mitra_id', '=', 'mitras.id') // Changed to leftJoin
            ->where('invoices.office_id', $officeId)
            ->whereNull('invoice_items.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->select(
                'invoices.status_pembayaran',
                'invoices.tgl_invoice',
                'products.nama_produk',
                'mitras.nama as nama_mitra',
                'invoices.nomor_invoice',
                'invoice_items.qty',
                'invoice_items.harga_satuan',
                'invoice_items.diskon_nilai',
                'invoice_items.total_harga_item',
                'invoice_items.id as item_id',
                'invoice_items.invoice_id'
            );

        if (! empty($prodIdFilter)) {
            $itemsQuery->whereIn('invoice_items.produk_id', $prodIdFilter);
        }
        if ($invTypeFilter) {
            $itemsQuery->where('invoices.tipe_invoice', $invTypeFilter);
        }
        if ($payStatusFilter) {
            $itemsQuery->where('invoices.status_pembayaran', $payStatusFilter);
        }
        if ($startDate && $endDate) {
            $itemsQuery->whereBetween('invoices.tgl_invoice', [$startDate, $endDate]);
        }
        if ($search) {
            $itemsQuery->where(function ($q) use ($search) {
                $q->where('invoices.nomor_invoice', 'like', "%$search%")
                    ->orWhere('products.nama_produk', 'like', "%$search%")
                    ->orWhere('mitras.nama', 'like', "%$search%");
            });
        }

        $invoiceItems = $itemsQuery->orderBy('invoices.tgl_invoice', 'desc')->get();

        // Calculate Taxes and Final Total per Item
        $itemIds = $invoiceItems->pluck('item_id')->toArray();

        $taxSums = [];
        if (! empty($itemIds)) {
            $taxSums = DB::table('invoice_item_taxes')
                ->whereIn('invoice_item_id', $itemIds)
                ->whereNull('deleted_at')
                ->select('invoice_item_id', DB::raw('SUM(nilai_pajak_diterapkan) as total_tax'))
                ->groupBy('invoice_item_id')
                ->pluck('total_tax', 'invoice_item_id')
                ->toArray();
        }

        $summaryTotalTransaction = 0;
        $summaryTotalInvoices = $invoiceItems->unique('invoice_id')->count();

        foreach ($invoiceItems as $item) {
            $tax = $taxSums[$item->item_id] ?? 0;
            $item->total_diskon = ($item->diskon_nilai * $item->qty);
            $item->total_akhir = ($item->total_harga_item - $item->total_diskon) + $tax;

            // Only sum up the final amount for report
            $summaryTotalTransaction += $item->total_akhir;
        }

        // Mitras for filter
        $mitras = Partner::where('office_id', $officeId)->get();
        // Products for filter
        $products = DB::table('products')->whereNull('deleted_at')->select('id', 'nama_produk')->orderBy('nama_produk')->get();

        $pieSeries = [(float) $outstanding, (float) $totalReceived]; // [Outstanding, Received]
        $pieLabels = ['Outstanding Amount', 'Amount Received'];

        return view('Report.Invoice.index', compact(
            'startDate',
            'endDate',
            'mitras',
            'products',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'year',
            'totalSales',
            'totalReceived',
            'outstanding',
            'pieSeries',
            'pieLabels',
            'payments',
            'totalPaymentAmount',
            'totalUniqueInvoices',
            'soldProducts',
            'totalSoldQty',
            'invoiceItems',
            'summaryTotalTransaction',
            'summaryTotalInvoices'
        ));
    }

    private function getDataForAjax(Request $request, $officeId)
    {
        $startDate = $request->start_date ?? date('Y-01-01');
        $endDate = $request->end_date ?? date('Y-12-31');
        $mitraId = $request->mitra_id;
        $search = $request->search;

        $response = [];

        // --- General Stats (Always needed or specific to Tab 1) ---
        $year = Carbon::parse($startDate)->year;
        // Income
        $incomeData = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.office_id', $officeId)
            ->where('invoices.tipe_invoice', 'Sales')
            ->whereYear('payments.tgl_pembayaran', $year)
            ->whereNull('payments.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->select(DB::raw('MONTH(payments.tgl_pembayaran) as month'), DB::raw('SUM(payments.jumlah_bayar) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')->toArray();

        // Expenditure
        $expenseData = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.office_id', $officeId)
            ->where('invoices.tipe_invoice', 'Purchase')
            ->whereYear('payments.tgl_pembayaran', $year)
            ->whereNull('payments.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->select(DB::raw('MONTH(payments.tgl_pembayaran) as month'), DB::raw('SUM(payments.jumlah_bayar) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')->toArray();

        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartIncome = [];
        $chartExpense = [];

        for ($m = 1; $m <= 12; $m++) {
            $chartIncome[] = $incomeData[$m] ?? 0;
            $chartExpense[] = $expenseData[$m] ?? 0;
        }

        $totalSales = DB::table('invoices')
            ->where('office_id', $officeId)
            ->where('tipe_invoice', 'Sales')
            ->whereNull('deleted_at')
            ->sum('total_akhir');

        $totalReceived = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.office_id', $officeId)
            ->where('invoices.tipe_invoice', 'Sales')
            ->whereNull('payments.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->sum('payments.jumlah_bayar');

        $outstanding = $totalSales - $totalReceived;
        if ($outstanding < 0) $outstanding = 0;

        $response['charts'] = [
            'income' => $chartIncome,
            'expense' => $chartExpense,
            'labels' => $chartLabels,
            'pieSeries' => [(float) $outstanding, (float) $totalReceived],
            'year' => $year
        ];

        // --- Tab 2: Payments ---
        $paymentsQuery = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->where('invoices.office_id', $officeId)
            ->whereNull('payments.deleted_at')
            ->whereBetween('payments.tgl_pembayaran', [$startDate, $endDate])
            ->select(
                'payments.tgl_pembayaran',
                'invoices.nomor_invoice',
                'payments.metode_pembayaran',
                'mitras.nomor_mitra',
                'mitras.nama as nama_mitra',
                'payments.jumlah_bayar'
            );

        if ($mitraId) {
            $paymentsQuery->where('invoices.mitra_id', $mitraId);
        }
        if ($search) {
            $paymentsQuery->where(function ($q) use ($search) {
                $q->where('invoices.nomor_invoice', 'like', "%$search%")
                    ->orWhere('mitras.nama', 'like', "%$search%")
                    ->orWhere('mitras.nomor_mitra', 'like', "%$search%")
                    ->orWhere('payments.nomor_pembayaran', 'like', "%$search%");
            });
        }
        $payments = $paymentsQuery->orderBy('payments.tgl_pembayaran', 'desc')->get();
        $totalPaymentAmount = $payments->sum('jumlah_bayar');
        $totalUniqueInvoices = $payments->unique('nomor_invoice')->count();

        // Render Partial View for Payments
        $response['html_payments'] = view('Report.Invoice._tab_payments', compact('payments', 'totalPaymentAmount', 'totalUniqueInvoices'))->render();

        // --- Tab 3: Sold Products ---
        $invoiceIdsQuery = DB::table('invoices')
            ->join('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->where('invoices.office_id', $officeId)
            ->where('invoices.tipe_invoice', 'Sales')
            ->whereNull('payments.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->whereBetween('payments.tgl_pembayaran', [$startDate, $endDate]);

        if ($mitraId) {
            $invoiceIdsQuery->where('invoices.mitra_id', $mitraId);
        }
        if ($search) {
            $invoiceIdsQuery->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
                ->where(function ($q) use ($search) {
                    $q->where('invoices.nomor_invoice', 'like', "%$search%")
                        ->orWhere('mitras.nama', 'like', "%$search%")
                        ->orWhere('mitras.nomor_mitra', 'like', "%$search%")
                        ->orWhere('payments.nomor_pembayaran', 'like', "%$search%");
                });
        }

        $invoiceIds = $invoiceIdsQuery->pluck('invoices.id')->unique();
        $soldProducts = DB::table('invoice_items')
            ->join('products', 'invoice_items.produk_id', '=', 'products.id')
            ->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->whereIn('invoice_items.invoice_id', $invoiceIds)
            ->whereNull('invoice_items.deleted_at')
            ->select(
                'products.nama_produk',
                'products.sku_kode',
                'products.satuan',
                'product_categories.nama_kategori',
                DB::raw('SUM(invoice_items.qty) as total_qty')
            )
            ->groupBy('products.id', 'products.nama_produk', 'products.sku_kode', 'products.satuan', 'product_categories.nama_kategori')
            ->get();
        $totalSoldQty = $soldProducts->sum('total_qty');

        $response['html_products'] = view('Report.Invoice._tab_products', compact('soldProducts', 'totalSoldQty'))->render();

        // --- Tab 4: Invoice Items ---
        $prodIdFilter = $request->input('product_id', []);
        if (! is_array($prodIdFilter)) {
            $prodIdFilter = [$prodIdFilter];
        }
        $prodIdFilter = array_values(array_filter($prodIdFilter, function ($v) {
            return $v !== null && $v !== '';
        }));
        $invTypeFilter = $request->invoice_type;
        $payStatusFilter = $request->payment_status;

        $itemsQuery = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.produk_id', '=', 'products.id')
            ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->where('invoices.office_id', $officeId)
            ->whereNull('invoice_items.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->select(
                'invoices.status_pembayaran',
                'invoices.tgl_invoice',
                'products.nama_produk',
                'mitras.nama as nama_mitra',
                'invoices.nomor_invoice',
                'invoice_items.qty',
                'invoice_items.harga_satuan',
                'invoice_items.diskon_nilai',
                'invoice_items.total_harga_item',
                'invoice_items.id as item_id',
                'invoice_items.invoice_id'
            );

        if (! empty($prodIdFilter)) {
            $itemsQuery->whereIn('invoice_items.produk_id', $prodIdFilter);
        }
        if ($invTypeFilter) {
            $itemsQuery->where('invoices.tipe_invoice', $invTypeFilter);
        }
        if ($payStatusFilter) {
            $itemsQuery->where('invoices.status_pembayaran', $payStatusFilter);
        }
        if ($startDate && $endDate) {
            $itemsQuery->whereBetween('invoices.tgl_invoice', [$startDate, $endDate]);
        }
        if ($search) {
            $itemsQuery->where(function ($q) use ($search) {
                $q->where('invoices.nomor_invoice', 'like', "%$search%")
                    ->orWhere('products.nama_produk', 'like', "%$search%")
                    ->orWhere('mitras.nama', 'like', "%$search%");
            });
        }

        $invoiceItems = $itemsQuery->orderBy('invoices.tgl_invoice', 'desc')->get();
        $itemIds = $invoiceItems->pluck('item_id')->toArray();
        $taxSums = [];
        if (! empty($itemIds)) {
            $taxSums = DB::table('invoice_item_taxes')
                ->whereIn('invoice_item_id', $itemIds)
                ->whereNull('deleted_at')
                ->select('invoice_item_id', DB::raw('SUM(nilai_pajak_diterapkan) as total_tax'))
                ->groupBy('invoice_item_id')
                ->pluck('total_tax', 'invoice_item_id')
                ->toArray();
        }

        $summaryTotalTransaction = 0;
        $summaryTotalInvoices = $invoiceItems->unique('invoice_id')->count();

        foreach ($invoiceItems as $item) {
            $tax = $taxSums[$item->item_id] ?? 0;
            $item->total_diskon = ($item->diskon_nilai * $item->qty);
            $item->total_akhir = ($item->total_harga_item - $item->total_diskon) + $tax;
            $summaryTotalTransaction += $item->total_akhir;
        }

        $response['html_invoice_items'] = view('Report.Invoice._tab_invoice_items', compact('invoiceItems', 'summaryTotalTransaction', 'summaryTotalInvoices'))->render();

        return response()->json($response);
    }

    public function export(Request $request)
    {
        $officeId = session('active_office_id');
        $startDate = $request->start_date ?? date('Y-01-01');
        $endDate = $request->end_date ?? date('Y-12-31');
        $mitraId = $request->mitra_id;
        $search = $request->search;
        $tab = $request->tab ?? 'general';
        $hiddenColumns = $request->hidden_columns ?? [];

        switch ($tab) {
            case 'general':
                $year = Carbon::parse($startDate)->year;
                // Income
                $incomeData = DB::table('payments')
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->where('invoices.office_id', $officeId)
                    ->where('invoices.tipe_invoice', 'Sales')
                    ->whereYear('payments.tgl_pembayaran', $year)
                    ->whereNull('payments.deleted_at')
                    ->whereNull('invoices.deleted_at')
                    ->select(DB::raw('MONTH(payments.tgl_pembayaran) as month'), DB::raw('SUM(payments.jumlah_bayar) as total'))
                    ->groupBy('month')
                    ->pluck('total', 'month')->toArray();

                // Expenditure
                $expenseData = DB::table('payments')
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->where('invoices.office_id', $officeId)
                    ->where('invoices.tipe_invoice', 'Purchase')
                    ->whereYear('payments.tgl_pembayaran', $year)
                    ->whereNull('payments.deleted_at')
                    ->whereNull('invoices.deleted_at')
                    ->select(DB::raw('MONTH(payments.tgl_pembayaran) as month'), DB::raw('SUM(payments.jumlah_bayar) as total'))
                    ->groupBy('month')
                    ->pluck('total', 'month')->toArray();

                $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $chartIncome = [];
                $chartExpense = [];

                for ($m = 1; $m <= 12; $m++) {
                    $chartIncome[] = $incomeData[$m] ?? 0;
                    $chartExpense[] = $expenseData[$m] ?? 0;
                }

                $totalSales = DB::table('invoices')
                    ->where('office_id', $officeId)
                    ->where('tipe_invoice', 'Sales')
                    ->whereNull('deleted_at')
                    ->sum('total_akhir');

                $totalReceived = DB::table('payments')
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->where('invoices.office_id', $officeId)
                    ->where('invoices.tipe_invoice', 'Sales')
                    ->whereNull('payments.deleted_at')
                    ->whereNull('invoices.deleted_at')
                    ->sum('payments.jumlah_bayar');

                $outstanding = $totalSales - $totalReceived;
                if ($outstanding < 0) $outstanding = 0;

                return view('Report.Invoice.export_general', compact('year', 'chartLabels', 'chartIncome', 'chartExpense', 'totalSales', 'totalReceived', 'outstanding'));

            case 'payments':
                $paymentsQuery = DB::table('payments')
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
                    ->where('invoices.office_id', $officeId)
                    ->whereNull('payments.deleted_at')
                    ->whereNull('invoices.deleted_at')
                    ->whereBetween('payments.tgl_pembayaran', [$startDate, $endDate])
                    ->select(
                        'payments.tgl_pembayaran',
                        'payments.jumlah_bayar',
                        'payments.metode_pembayaran',
                        'invoices.nomor_invoice',
                        'mitras.nama as nama_mitra',
                        'mitras.nomor_mitra'
                    );

                if ($mitraId) {
                    $paymentsQuery->where('invoices.mitra_id', $mitraId);
                }

                if ($search) {
                    $paymentsQuery->where(function ($q) use ($search) {
                        $q->where('invoices.nomor_invoice', 'like', "%$search%")
                            ->orWhere('mitras.nama', 'like', "%$search%")
                            ->orWhere('mitras.nomor_mitra', 'like', "%$search%")
                            ->orWhere('payments.nomor_pembayaran', 'like', "%$search%");
                    });
                }

                $payments = $paymentsQuery->orderBy('payments.tgl_pembayaran', 'desc')->get();
                $totalPaymentAmount = $payments->sum('jumlah_bayar');

                return view('Report.Invoice.export_payments', compact('startDate', 'endDate', 'payments', 'totalPaymentAmount', 'hiddenColumns'));

            case 'sold_products':
                $invoiceIdsQuery = DB::table('invoices')
                    ->join('payments', 'invoices.id', '=', 'payments.invoice_id')
                    ->where('invoices.office_id', $officeId)
                    ->where('invoices.tipe_invoice', 'Sales')
                    ->whereNull('payments.deleted_at')
                    ->whereNull('invoices.deleted_at')
                    ->whereBetween('payments.tgl_pembayaran', [$startDate, $endDate]);

                if ($mitraId) {
                    $invoiceIdsQuery->where('invoices.mitra_id', $mitraId);
                }
                if ($search) {
                    $invoiceIdsQuery->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
                        ->where(function ($q) use ($search) {
                            $q->where('invoices.nomor_invoice', 'like', "%$search%")
                                ->orWhere('mitras.nama', 'like', "%$search%")
                                ->orWhere('mitras.nomor_mitra', 'like', "%$search%")
                                ->orWhere('payments.nomor_pembayaran', 'like', "%$search%");
                        });
                }

                $invoiceIds = $invoiceIdsQuery->pluck('invoices.id')->unique();

                $soldProducts = DB::table('invoice_items')
                    ->join('products', 'invoice_items.produk_id', '=', 'products.id')
                    ->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')
                    ->whereIn('invoice_items.invoice_id', $invoiceIds)
                    ->whereNull('invoice_items.deleted_at')
                    ->select(
                        'products.nama_produk',
                        'products.sku_kode',
                        'products.satuan',
                        'product_categories.nama_kategori',
                        DB::raw('SUM(invoice_items.qty) as total_qty')
                    )
                    ->groupBy('products.id', 'products.nama_produk', 'products.sku_kode', 'products.satuan', 'product_categories.nama_kategori')
                    ->get();

                $totalSoldQty = $soldProducts->sum('total_qty');

                return view('Report.Invoice.export_sold_products', compact('startDate', 'endDate', 'soldProducts', 'totalSoldQty', 'hiddenColumns'));

            case 'invoice_items':
                $prodIdFilter = $request->input('product_id', []);
                if (! is_array($prodIdFilter)) {
                    $prodIdFilter = [$prodIdFilter];
                }
                $prodIdFilter = array_values(array_filter($prodIdFilter, function ($v) {
                    return $v !== null && $v !== '';
                }));
                $invTypeFilter = $request->invoice_type;
                $payStatusFilter = $request->payment_status;

                $itemsQuery = DB::table('invoice_items')
                    ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->join('products', 'invoice_items.produk_id', '=', 'products.id')
                    ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
                    ->where('invoices.office_id', $officeId)
                    ->whereNull('invoice_items.deleted_at')
                    ->whereNull('invoices.deleted_at')
                    ->select(
                        'invoices.status_pembayaran',
                        'invoices.tgl_invoice',
                        'products.nama_produk',
                        'mitras.nama as nama_mitra',
                        'invoices.nomor_invoice',
                        'invoice_items.qty',
                        'invoice_items.harga_satuan',
                        'invoice_items.diskon_nilai',
                        'invoice_items.total_harga_item',
                        'invoice_items.id as item_id',
                        'invoice_items.invoice_id'
                    );

                if (! empty($prodIdFilter)) {
                    $itemsQuery->whereIn('invoice_items.produk_id', $prodIdFilter);
                }
                if ($invTypeFilter) {
                    $itemsQuery->where('invoices.tipe_invoice', $invTypeFilter);
                }
                if ($payStatusFilter) {
                    $itemsQuery->where('invoices.status_pembayaran', $payStatusFilter);
                }
                if ($startDate && $endDate) {
                    $itemsQuery->whereBetween('invoices.tgl_invoice', [$startDate, $endDate]);
                }
                if ($search) {
                    $itemsQuery->where(function ($q) use ($search) {
                        $q->where('invoices.nomor_invoice', 'like', "%$search%")
                            ->orWhere('products.nama_produk', 'like', "%$search%")
                            ->orWhere('mitras.nama', 'like', "%$search%");
                    });
                }

                $invoiceItems = $itemsQuery->orderBy('invoices.tgl_invoice', 'desc')->get();

                $itemIds = $invoiceItems->pluck('item_id')->toArray();
                $taxSums = [];
                if (! empty($itemIds)) {
                    $taxSums = DB::table('invoice_item_taxes')
                        ->whereIn('invoice_item_id', $itemIds)
                        ->whereNull('deleted_at')
                        ->select('invoice_item_id', DB::raw('SUM(nilai_pajak_diterapkan) as total_tax'))
                        ->groupBy('invoice_item_id')
                        ->pluck('total_tax', 'invoice_item_id')
                        ->toArray();
                }

                $summaryTotalTransaction = 0;
                foreach ($invoiceItems as $item) {
                    $tax = $taxSums[$item->item_id] ?? 0;
                    $item->total_diskon = ($item->diskon_nilai * $item->qty);
                    $item->total_akhir = ($item->total_harga_item - $item->total_diskon) + $tax;
                    $summaryTotalTransaction += $item->total_akhir;
                }

                return view('Report.Invoice.export_invoice_items', compact('startDate', 'endDate', 'invoiceItems', 'summaryTotalTransaction', 'hiddenColumns'));
        }
    }
}
