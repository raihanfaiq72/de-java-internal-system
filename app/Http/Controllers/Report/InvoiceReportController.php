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
        extract($this->getGeneralStats($startDate, $officeId));

        // --- Tab 2: Rekap Pembayaran ---
        extract($this->getPaymentsData($request, $startDate, $endDate, $officeId, $mitraId, $search));

        // --- Tab 3: Laporan Produk Terjual ---
        extract($this->getSoldProductsData($request, $startDate, $endDate, $officeId, $mitraId, $search));

        // --- Tab 4: Laporan Invoice Per Produk ---
        extract($this->getInvoiceItemsData($request, $startDate, $endDate, $officeId, $mitraId, $search));

        // Mitras for filter
        $mitras = Partner::where('office_id', $officeId)->get();
        // Products for filter
        $products = DB::table('products')->whereNull('deleted_at')->select('id', 'nama_produk')->orderBy('nama_produk')->get();

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
            'totalSoldValue',
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

        // --- General Stats ---
        extract($this->getGeneralStats($startDate, $officeId));

        $response['charts'] = [
            'income' => $chartIncome,
            'expense' => $chartExpense,
            'labels' => $chartLabels,
            'pieSeries' => [(float) $outstanding, (float) $totalReceived],
            'year' => $year,
        ];

        // --- Tab 2: Payments ---
        extract($this->getPaymentsData($request, $startDate, $endDate, $officeId, $mitraId, $search));
        $response['html_payments'] = view('Report.Invoice._tab_payments', compact('payments', 'totalPaymentAmount', 'totalUniqueInvoices'))->render();

        // --- Tab 3: Sold Products ---
        extract($this->getSoldProductsData($request, $startDate, $endDate, $officeId, $mitraId, $search));
        $response['html_products'] = view('Report.Invoice._tab_products', compact('soldProducts', 'totalSoldQty', 'totalSoldValue'))->render();

        // --- Tab 4: Invoice Items ---
        extract($this->getInvoiceItemsData($request, $startDate, $endDate, $officeId, $mitraId, $search));
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
                extract($this->getGeneralStats($startDate, $officeId));

                return view('Report.Invoice.export_general', compact('year', 'chartLabels', 'chartIncome', 'chartExpense', 'totalSales', 'totalReceived', 'outstanding'));

            case 'payments':
                extract($this->getPaymentsData($request, $startDate, $endDate, $officeId, $mitraId, $search));

                return view('Report.Invoice.export_payments', compact('startDate', 'endDate', 'payments', 'totalPaymentAmount', 'hiddenColumns'));

            case 'sold_products':
                extract($this->getSoldProductsData($request, $startDate, $endDate, $officeId, $mitraId, $search));

                return view('Report.Invoice.export_sold_products', compact('startDate', 'endDate', 'soldProducts', 'totalSoldQty', 'hiddenColumns'));

            case 'invoice_items':
                extract($this->getInvoiceItemsData($request, $startDate, $endDate, $officeId, $mitraId, $search));

                return view('Report.Invoice.export_invoice_items', compact('startDate', 'endDate', 'invoiceItems', 'summaryTotalTransaction', 'hiddenColumns'));
        }
    }

    // =========================================================================
    // HELPER DATA METHODS
    // =========================================================================

    private function getGeneralStats($startDate, $officeId)
    {
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

        $totalSales = Invoice::where('office_id', $officeId)
            ->where('tipe_invoice', 'Sales')
            ->sum('total_akhir');

        $totalReceived = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('invoices.office_id', $officeId)
            ->where('invoices.tipe_invoice', 'Sales')
            ->whereNull('payments.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->sum('payments.jumlah_bayar');

        $outstanding = max(0, $totalSales - $totalReceived);

        $pieSeries = [(float) $outstanding, (float) $totalReceived];
        $pieLabels = ['Outstanding Amount', 'Amount Received'];

        return compact('year', 'chartLabels', 'chartIncome', 'chartExpense', 'totalSales', 'totalReceived', 'outstanding', 'pieSeries', 'pieLabels');
    }

    private function getPaymentsData(Request $request, $startDate, $endDate, $officeId, $mitraId, $search)
    {
        $itemsSubQuery = DB::table('invoice_items')
            ->whereNull('deleted_at')
            ->select('invoice_id', DB::raw('SUM(qty) as total_qty'), DB::raw('MAX(harga_satuan) as harga_satuan'))
            ->groupBy('invoice_id');

        $paymentsQuery = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->leftJoin('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->leftJoinSub($itemsSubQuery, 'items', function ($join) {
                $join->on('invoices.id', '=', 'items.invoice_id');
            })
            ->where('invoices.office_id', $officeId)
            ->whereNull('payments.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->whereBetween('payments.tgl_pembayaran', [$startDate, $endDate])
            ->select(
                'payments.tgl_pembayaran',
                'payments.jumlah_bayar',
                'payments.metode_pembayaran',
                'payments.nomor_pembayaran',
                'invoices.nomor_invoice',
                'invoices.status_pembayaran',
                'mitras.nama as nama_mitra',
                'mitras.nomor_mitra',
                'items.total_qty',
                'items.harga_satuan'
            );

        $this->applyCommonFilters($paymentsQuery, $request, $mitraId, $search, true);

        $payments = $paymentsQuery->orderBy('payments.tgl_pembayaran', 'desc')->get();
        $totalPaymentAmount = $payments->sum('jumlah_bayar');
        $totalUniqueInvoices = $payments->unique('nomor_invoice')->count();

        return compact('payments', 'totalPaymentAmount', 'totalUniqueInvoices');
    }

    private function getSoldProductsData(Request $request, $startDate, $endDate, $officeId, $mitraId, $search)
    {
        $invoiceIdsQuery = DB::table('invoices')
            ->join('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->where('invoices.office_id', $officeId)
            ->where('invoices.tipe_invoice', 'Sales')
            ->whereNull('payments.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->whereBetween('payments.tgl_pembayaran', [$startDate, $endDate])
            ->select('invoices.id');

        $this->applyCommonFilters($invoiceIdsQuery, $request, $mitraId, $search, false); // No mitras join needed for items, done in subquery if search

        $invoiceIds = $invoiceIdsQuery->pluck('id')->unique();

        $taxSumSub = DB::table('invoice_item_taxes')
            ->whereNull('deleted_at')
            ->select('invoice_item_id', DB::raw('SUM(nilai_pajak_diterapkan) as total_tax'))
            ->groupBy('invoice_item_id');

        $soldProductsQuery = DB::table('invoice_items')
            ->join('products', 'invoice_items.produk_id', '=', 'products.id')
            ->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->leftJoinSub($taxSumSub, 'tax_sum', function ($join) {
                $join->on('invoice_items.id', '=', 'tax_sum.invoice_item_id');
            })
            ->whereIn('invoice_items.invoice_id', $invoiceIds)
            ->whereNull('invoice_items.deleted_at')
            ->select(
                'products.nama_produk',
                'products.sku_kode',
                'products.satuan',
                'product_categories.nama_kategori',
                DB::raw('SUM(invoice_items.qty) as total_qty'),
                DB::raw('SUM((invoice_items.total_harga_item - (invoice_items.diskon_nilai * invoice_items.qty)) + COALESCE(tax_sum.total_tax, 0)) as total_value')
            )
            ->groupBy('products.id', 'products.nama_produk', 'products.sku_kode', 'products.satuan', 'product_categories.nama_kategori');

        // Apply Product ID filter directly on the items for this tab since it's querying invoice_items
        $prodIdFilter = $this->extractProductFilter($request);
        if (! empty($prodIdFilter)) {
            $soldProductsQuery->whereIn('invoice_items.produk_id', $prodIdFilter);
        }

        $soldProducts = $soldProductsQuery->get();
        $totalSoldQty = $soldProducts->sum('total_qty');
        $totalSoldValue = $soldProducts->sum('total_value');

        return compact('soldProducts', 'totalSoldQty', 'totalSoldValue');
    }

    private function getInvoiceItemsData(Request $request, $startDate, $endDate, $officeId, $mitraId, $search)
    {
        $itemsQuery = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.produk_id', '=', 'products.id')
            ->leftJoin('mitras', 'invoices.mitra_id', '=', 'mitras.id')
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

        if ($startDate && $endDate) {
            $itemsQuery->whereBetween('invoices.tgl_invoice', [$startDate, $endDate]);
        }

        $this->applyCommonFilters($itemsQuery, $request, $mitraId, $search, true, true);

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

        return compact('invoiceItems', 'summaryTotalTransaction', 'summaryTotalInvoices');
    }

    private function extractProductFilter(Request $request)
    {
        $prodIdFilter = $request->input('product_id', []);
        if (! is_array($prodIdFilter)) {
            $prodIdFilter = [$prodIdFilter];
        }

        return array_values(array_filter($prodIdFilter, function ($v) {
            return $v !== null && $v !== '';
        }));
    }

    private function applyCommonFilters($query, Request $request, $mitraId, $search, $hasMitraJoin = false, $isInvoiceItemsQuery = false)
    {
        $prodIdFilter = $this->extractProductFilter($request);
        $invTypeFilter = $request->invoice_type;
        $payStatusFilter = $request->payment_status;

        // Apply filters directly to invoices table
        if ($mitraId) {
            $query->where('invoices.mitra_id', $mitraId);
        }
        if ($invTypeFilter) {
            $query->where('invoices.tipe_invoice', $invTypeFilter);
        }
        if ($payStatusFilter) {
            $query->where('invoices.status_pembayaran', $payStatusFilter);
        }

        // Apply product filter
        if (! empty($prodIdFilter)) {
            if ($isInvoiceItemsQuery) {
                $query->whereIn('invoice_items.produk_id', $prodIdFilter);
            } else {
                $query->whereExists(function ($q) use ($prodIdFilter) {
                    $q->select(DB::raw(1))
                        ->from('invoice_items')
                        ->whereColumn('invoice_items.invoice_id', 'invoices.id')
                        ->whereIn('invoice_items.produk_id', $prodIdFilter)
                        ->whereNull('invoice_items.deleted_at');
                });
            }
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search, $hasMitraJoin, $isInvoiceItemsQuery) {
                $q->where('invoices.nomor_invoice', 'like', "%$search%");

                if ($hasMitraJoin) {
                    $q->orWhere('mitras.nama', 'like', "%$search%")
                        ->orWhere('mitras.nomor_mitra', 'like', "%$search%");
                } else {
                    $q->orWhereExists(function ($sub) use ($search) {
                        $sub->select(DB::raw(1))
                            ->from('mitras')
                            ->whereColumn('mitras.id', 'invoices.mitra_id')
                            ->where(function ($mitraQ) use ($search) {
                                $mitraQ->where('nama', 'like', "%$search%")
                                    ->orWhere('nomor_mitra', 'like', "%$search%");
                            });
                    });
                }

                // If joining payments (meaning 'payments.nomor_pembayaran' exists)
                if (str_contains($q->toSql(), '`payments`')) {
                    $q->orWhere('payments.nomor_pembayaran', 'like', "%$search%");
                }

                if ($isInvoiceItemsQuery) {
                    $q->orWhere('products.nama_produk', 'like', "%$search%");
                }
            });
        }
    }
}
