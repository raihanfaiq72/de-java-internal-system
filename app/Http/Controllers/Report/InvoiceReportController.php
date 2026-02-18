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

        // --- Tab 4: Laporan Invoice Per Produk ---
        $prodIdFilter = $request->product_id;
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

        if ($prodIdFilter) {
            $itemsQuery->where('invoice_items.produk_id', $prodIdFilter);
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
        $uniqueInvoiceIds = [];

        foreach ($invoiceItems as $item) {
            $tax = $taxSums[$item->item_id] ?? 0;
            $item->total_pajak_item = $tax;

            // Calculate total discount
            // total_harga_item = (qty * harga) - total_diskon
            // total_diskon = (qty * harga) - total_harga_item
            $gross = $item->qty * $item->harga_satuan;
            $item->total_diskon = $gross - $item->total_harga_item;

            // Total Akhir Item = total_harga_item + tax
            $item->total_akhir = $item->total_harga_item + $tax;

            $summaryTotalTransaction += $item->total_akhir;
            $uniqueInvoiceIds[$item->nomor_invoice] = true;
        }

        $summaryTotalInvoices = count($uniqueInvoiceIds);

        // Mitras for filter
        $mitras = Partner::where('office_id', $officeId)->get();
        // Products for filter
        $products = DB::table('products')->whereNull('deleted_at')->select('id', 'nama_produk')->orderBy('nama_produk')->get();

        return view('Report.Invoice.index', compact(
            'chartLabels', 'chartIncome', 'chartExpense',
            'pieSeries', 'pieLabels',
            'payments', 'totalPaymentAmount', 'totalUniqueInvoices',
            'soldProducts', 'totalSoldQty',
            'mitras', 'startDate', 'endDate', 'year',
            'invoiceItems', 'summaryTotalTransaction', 'summaryTotalInvoices', 'products'
        ));
    }
}
