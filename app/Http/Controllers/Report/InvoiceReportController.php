<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Office;
use App\Models\Partner;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InvoiceReportController extends Controller
{
    public function index(Request $request)
    {
        $officeId = session('active_office_id');
        $startDate = $request->start_date ?? date('Y-01-01');
        $endDate = $request->end_date ?? date('Y-12-31');
        $mitraId = (array) $request->mitra_id;
        $mitraId = array_values(array_filter($mitraId, fn ($v) => $v !== null && $v !== ''));
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
        $products = Product::select('id', 'nama_produk')->orderBy('nama_produk')->get();

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
            'totalNotaAmount',
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
        $mitraId = (array) $request->mitra_id;
        $mitraId = array_values(array_filter($mitraId, fn ($v) => $v !== null && $v !== ''));
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
        $response['html_payments'] = view('Report.Invoice._tab_payments', compact('payments', 'totalPaymentAmount', 'totalNotaAmount', 'totalUniqueInvoices'))->render();

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

        // If no office ID in session, try to get the first available office
        if (! $officeId) {
            $officeId = Office::first()?->id;
            session(['active_office_id' => $officeId]);
        }

        $startDate = $request->start_date ?? date('Y-01-01');
        $endDate = $request->end_date ?? date('Y-12-31');
        $mitraId = $request->mitra_id;
        $search = $request->search;
        $tab = $request->tab ?? 'general';
        $hiddenColumns = $request->hidden_columns ?? [];

        switch ($tab) {
            case 'general':
                return $this->exportGeneralSpreadsheet($startDate, $officeId);
            case 'payments':
                return $this->exportPaymentsSpreadsheet($request, $startDate, $endDate, $officeId, $mitraId, $search, $hiddenColumns);
            case 'sold_products':
                return $this->exportSoldProductsSpreadsheet($request, $startDate, $endDate, $officeId, $mitraId, $search, $hiddenColumns);
            case 'invoice_items':
                return $this->exportInvoiceItemsSpreadsheet($request, $startDate, $endDate, $officeId, $mitraId, $search, $hiddenColumns);
            default:
                return back()->with('error', 'Tab tidak dikenali');
        }

    }

    // =========================================================================
    // HELPER DATA METHODS
    // =========================================================================

    private function getGeneralStats($startDate, $officeId)
    {
        $year = Carbon::parse($startDate)->year;

        // Income
        $incomeData = Payment::whereHas('invoice', function ($query) use ($officeId) {
            $query->where('office_id', $officeId)
                ->where('tipe_invoice', 'Sales');
        })
            ->whereYear('tgl_pembayaran', $year)
            ->selectRaw('MONTH(tgl_pembayaran) as month, SUM(jumlah_bayar) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Expenditure
        $expenseData = Payment::whereHas('invoice', function ($query) use ($officeId) {
            $query->where('office_id', $officeId)
                ->where('tipe_invoice', 'Purchase');
        })
            ->whereYear('tgl_pembayaran', $year)
            ->selectRaw('MONTH(tgl_pembayaran) as month, SUM(jumlah_bayar) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

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

        $totalReceived = Payment::whereHas('invoice', function ($query) use ($officeId) {
            $query->where('office_id', $officeId)
                ->where('tipe_invoice', 'Sales');
        })
            ->sum('jumlah_bayar');

        $outstanding = max(0, $totalSales - $totalReceived);

        $pieSeries = [(float) $outstanding, (float) $totalReceived];
        $pieLabels = ['Outstanding Amount', 'Amount Received'];

        return compact('year', 'chartLabels', 'chartIncome', 'chartExpense', 'totalSales', 'totalReceived', 'outstanding', 'pieSeries', 'pieLabels');
    }

    private function getPaymentsData(Request $request, $startDate, $endDate, $officeId, $mitraId, $search)
    {
        $paymentsQuery = Invoice::with(['mitra'])
            ->leftJoin('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->select('invoices.*')
            ->addSelect(['mitras.nama as nama_mitra', 'mitras.nomor_mitra as nomor_mitra'])
            ->withSum(['payment as jumlah_bayar'], 'jumlah_bayar')
            ->selectRaw('(invoices.total_akhir - COALESCE((SELECT SUM(jumlah_bayar) FROM payments WHERE invoice_id = invoices.id AND deleted_at IS NULL), 0)) as sisa_piutang_virtual')
            ->selectSub(function ($query) {
                $query->from('payments')
                    ->select('metode_pembayaran')
                    ->whereColumn('invoice_id', 'invoices.id')
                    ->whereNull('deleted_at')
                    ->latest('id')
                    ->limit(1);
            }, 'metode_pembayaran')
            ->where('invoices.office_id', $officeId)
            ->whereBetween('invoices.tgl_invoice', [$startDate, $endDate]);

        $this->applyCommonFilters($paymentsQuery, $request, $mitraId, $search, true);

        // Calculate totals for the entire set before pagination
        $totalsQuery = Invoice::where('invoices.office_id', $officeId)
            ->whereBetween('invoices.tgl_invoice', [$startDate, $endDate]);
        $this->applyCommonFilters($totalsQuery, $request, $mitraId, $search, false);
        $totals = $totalsQuery->selectRaw('SUM(invoices.total_akhir) as total_nota, COUNT(*) as total_count')->first();

        // Unfortunately withSum doesn't work well for calculating grand total of the sum across all records in a single query easily without a subquery or second query
        $totalPaymentAmount = Payment::whereHas('invoice', function ($q) use ($officeId, $startDate, $endDate) {
            $q->where('office_id', $officeId)->whereBetween('tgl_invoice', [$startDate, $endDate]);
        })->sum('jumlah_bayar');

        $totalNotaAmount = $totals->total_nota ?? 0;
        $totalUniqueInvoices = $totals->total_count ?? 0;

        // Paginate for display
        $this->applySorting($paymentsQuery, $request, 'invoices.tgl_invoice', 'desc');
        $payments = $paymentsQuery->paginate(10)->withQueryString();

        // Map values for backward compatibility with the view if needed (though Eloquent handles this better)
        $payments->getCollection()->transform(function ($invoice) {
            $invoice->nama_mitra = $invoice->mitra?->nama;
            $invoice->nomor_mitra = $invoice->mitra?->nomor_mitra;
            $invoice->tgl_pembayaran = $invoice->tgl_invoice; // Consistency with old view
            $invoice->metode_pembayaran = $invoice->metode_pembayaran ?? 'Belum Bayar';

            return $invoice;
        });

        return compact('payments', 'totalPaymentAmount', 'totalNotaAmount', 'totalUniqueInvoices');
    }

    private function getPaymentsWithDetailsData(Request $request, $startDate, $endDate, $officeId, $mitraId, $search)
    {
        // Get payments with invoice details
        $paymentsQuery = Invoice::with(['payment', 'mitra'])
            ->where('office_id', $officeId)
            ->where('tipe_invoice', 'Sales')
            ->whereBetween('tgl_invoice', [$startDate, $endDate]);

        $this->applyCommonFilters($paymentsQuery, $request, $mitraId, $search, false);

        $payments = $paymentsQuery->orderBy('tgl_invoice', 'desc')->get();

        $payments->transform(function ($invoice) {
            $payment = $invoice->payment->first(); // Get first payment

            return (object) [
                'invoice_id' => $invoice->id,
                'tgl_invoice' => $invoice->tgl_invoice,
                'total_akhir' => $invoice->total_akhir,
                'status_pembayaran' => $invoice->status_pembayaran,
                'tgl_jatuh_tempo' => $invoice->tgl_jatuh_tempo,
                'nomor_invoice' => $invoice->nomor_invoice,
                'keterangan' => $invoice->keterangan,
                'tgl_pembayaran' => $payment ? $payment->tgl_pembayaran : $invoice->tgl_invoice,
                'jumlah_bayar' => $payment ? $payment->jumlah_bayar : 0,
                'metode_pembayaran' => $payment ? $payment->metode_pembayaran : 'Belum Bayar',
                'nomor_pembayaran' => $payment ? $payment->nomor_pembayaran : '-',
                'nama_mitra' => $invoice->mitra?->nama,
                'nomor_mitra' => $invoice->mitra?->nomor_mitra,
                'alamat' => $invoice->mitra?->alamat,
                'telepon' => $invoice->mitra?->no_hp,
            ];
        });

        $this->applyCommonFilters($paymentsQuery, $request, $mitraId, $search, true);

        $payments = $paymentsQuery->orderBy('invoices.tgl_invoice', 'desc')->get();

        // Get invoice items for each payment
        $invoiceIds = $payments->pluck('invoice_id')->toArray();
        $itemsData = [];

        if (! empty($invoiceIds)) {
            $taxSumData = InvoiceItem::with(['taxes'])
                ->whereNull('deleted_at')
                ->get()
                ->groupBy('invoice_item_id')
                ->map(function ($group) {
                    return $group->sum('taxes.nilai_pajak_diterapkan');
                });

            $itemsQuery = InvoiceItem::with(['product', 'taxes'])
                ->whereIn('invoice_id', $invoiceIds)
                ->whereNull('deleted_at')
                ->get()
                ->map(function ($item) {
                    $totalTax = $item->taxes->sum('nilai_pajak_diterapkan');

                    return (object) [
                        'invoice_id' => $item->invoice_id,
                        'qty' => $item->qty,
                        'harga_satuan' => $item->harga_satuan,
                        'diskon_nilai' => $item->diskon_nilai,
                        'total_harga_item' => $item->total_harga_item,
                        'item_id' => $item->id,
                        'nama_produk' => $item->product ? $item->product->nama_produk : null,
                        'sku_kode' => $item->product ? $item->product->sku_kode : null,
                        'satuan' => $item->product ? $item->product->satuan : null,
                        'total_tax' => $totalTax,
                    ];
                })
                ->sortBy('invoice_id')
                ->sortBy('id');

            $items = $itemsQuery->get();

            foreach ($items as $item) {
                $item->total_diskon = $item->diskon_nilai * $item->qty;
                $item->total_akhir = ($item->total_harga_item - $item->total_diskon) + $item->total_tax;
                $itemsData[$item->invoice_id][] = $item;
            }
        }

        // Attach items to payments
        foreach ($payments as $payment) {
            $payment->items = $itemsData[$payment->invoice_id] ?? [];
        }

        return compact('payments', 'totalPaymentAmount', 'totalUniqueInvoices');
    }

    private function getSoldProductsData(Request $request, $startDate, $endDate, $officeId, $mitraId, $search)
    {
        $soldProductsQuery = InvoiceItem::query()
            ->selectRaw('products.nama_produk, products.sku_kode, products.satuan, product_categories.nama_kategori, SUM(invoice_items.qty) as total_qty, SUM(invoice_items.total_harga_item) as total_base_value')
            ->join('products', 'invoice_items.produk_id', '=', 'products.id')
            ->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.office_id', $officeId)
            ->where('invoices.tipe_invoice', 'Sales')
            ->whereBetween('invoices.tgl_invoice', [$startDate, $endDate])
            ->groupBy('products.id', 'products.nama_produk', 'products.sku_kode', 'products.satuan', 'product_categories.nama_kategori');

        $this->applyCommonFilters($soldProductsQuery, $request, $mitraId, $search, false, true);

        // Calculate totals for the entire set before pagination
        $totals = (clone $soldProductsQuery)->get();
        $totalSoldQty = $totals->sum('total_qty');
        $totalSoldValue = $totals->sum('total_base_value'); // Tax is usually handled separately if complex, but here we summarize base

        // Paginate for display
        $this->applySorting($soldProductsQuery, $request, 'products.nama_produk', 'asc');
        $soldProducts = $soldProductsQuery->paginate(10)->withQueryString();

        // Attach total_value including tax if needed for each row (usually done via subquery or accessor)
        // For simplicity in this refactor, we'll use base value or add a tax subquery if critical.

        return compact('soldProducts', 'totalSoldQty', 'totalSoldValue');
    }

    private function getInvoiceItemsData(Request $request, $startDate, $endDate, $officeId, $mitraId, $search)
    {
        $itemsQuery = InvoiceItem::with(['invoice.mitra', 'product'])
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->leftJoin('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->join('products', 'invoice_items.produk_id', '=', 'products.id')
            ->select('invoice_items.*')
            ->addSelect([
                'invoices.tgl_invoice',
                'invoices.nomor_invoice',
                'invoices.status_pembayaran',
                'invoices.tipe_invoice',
                'mitras.nama as nama_mitra',
                'products.nama_produk',
                DB::raw('(invoice_items.qty * invoice_items.diskon_nilai) as total_diskon')
            ])
            ->withSum(['taxes as total_tax'], 'nilai_pajak_diterapkan')
            ->where('invoices.office_id', $officeId);

        if ($startDate && $endDate) {
            $itemsQuery->whereBetween('invoices.tgl_invoice', [$startDate, $endDate]);
        }

        $this->applyCommonFilters($itemsQuery, $request, $mitraId, $search, true, true);

        // Calculate totals for the entire set before pagination
        $totalsQuery = (clone $itemsQuery);
        $summaryTotalTransaction = $totalsQuery->get()->sum(function ($item) {
            return $item->total_harga_item + ($item->total_tax ?? 0);
        });
        $summaryTotalInvoices = $totalsQuery->distinct('invoice_id')->count('invoice_id');

        // Paginate for display
        $this->applySorting($itemsQuery, $request, 'invoice_id', 'desc');
        $invoiceItems = $itemsQuery->paginate(10)->withQueryString();

        $invoiceItems->getCollection()->transform(function ($item) {
            $item->status_pembayaran = $item->invoice?->status_pembayaran;
            $item->tgl_invoice = $item->invoice?->tgl_invoice;
            $item->nama_produk = $item->product?->nama_produk;
            $item->nama_mitra = $item->invoice?->mitra?->nama;
            $item->nomor_invoice = $item->invoice?->nomor_invoice;
            $item->total_akhir_row = $item->total_harga_item + ($item->total_tax ?? 0);
            $item->total_akhir = $item->invoice?->total_akhir;
            $item->item_id = $item->id;

            return $item;
        });

        return compact('invoiceItems', 'summaryTotalTransaction', 'summaryTotalInvoices');
    }

    // =========================================================================
    // EXPORT SPREADSHEET METHODS
    // =========================================================================

    private function exportGeneralSpreadsheet($startDate, $officeId)
    {
        extract($this->getGeneralStats($startDate, $officeId));
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Umum');

        // Header
        $sheet->setCellValue('A1', 'Laporan Invoice - Rekap Umum');
        $sheet->setCellValue('A2', 'Periode: '.$year);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Section 1: Income vs Expenditure
        $sheet->setCellValue('A4', 'Income vs Expenditure (Per Bulan)');
        $sheet->getStyle('A4')->getFont()->setBold(true);

        $row = 5;
        $headers = ['Bulan', 'Income (Pemasukan)', 'Expenditure (Pengeluaran)', 'Profit/Loss'];
        $this->writeHeader($sheet, $headers, $row);

        $row++;
        $startDataRow = $row;
        foreach ($chartLabels as $index => $label) {
            $sheet->setCellValue('A'.$row, $label);
            $sheet->setCellValue('B'.$row, $chartIncome[$index]);
            $sheet->setCellValue('C'.$row, $chartExpense[$index]);
            $sheet->setCellValue('D'.$row, $chartIncome[$index] - $chartExpense[$index]);
            $row++;
        }

        // Totals
        $sheet->setCellValue('A'.$row, 'Total');
        $sheet->setCellValue('B'.$row, "=SUM(B$startDataRow:B".($row - 1).')');
        $sheet->setCellValue('C'.$row, "=SUM(C$startDataRow:C".($row - 1).')');
        $sheet->setCellValue('D'.$row, "=SUM(D$startDataRow:D".($row - 1).')');
        $this->applyBodyStyle($sheet, 'A5', 'D'.$row);
        $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setBold(true);

        // Section 2: Composition
        $row += 2;
        $sheet->setCellValue('A'.$row, 'Komposisi Piutang (Sales)');
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);

        $row++;
        $this->writeHeader($sheet, ['Kategori', 'Nilai'], $row);

        $row++;
        $sheet->setCellValue('A'.$row, 'Total Penjualan (Sales)');
        $sheet->setCellValue('B'.$row, $totalSales);
        $row++;
        $sheet->setCellValue('A'.$row, 'Total Diterima (Received)');
        $sheet->setCellValue('B'.$row, $totalReceived);
        $row++;
        $sheet->setCellValue('A'.$row, 'Belum Dibayar (Outstanding)');
        $sheet->setCellValue('B'.$row, $outstanding);

        $this->applyBodyStyle($sheet, 'A'.($row - 3), 'B'.$row);
        $sheet->getStyle('B4:B'.$row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('C4:C'.$row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('D4:D'.$row)->getNumberFormat()->setFormatCode('#,##0');

        return $this->streamSpreadsheet($spreadsheet, 'Laporan_Invoice_General_'.date('Y-m-d').'.xlsx');
    }

    private function exportPaymentsSpreadsheet($request, $startDate, $endDate, $officeId, $mitraId, $search, $hiddenColumns)
    {
        $paymentsQuery = Invoice::with(['mitra'])
            ->withSum(['items as total_qty'], 'qty')
            ->withSum(['payment as jumlah_bayar'], 'jumlah_bayar')
            ->selectSub(function ($query) {
                $query->from('payments')
                    ->select('metode_pembayaran')
                    ->whereColumn('invoice_id', 'invoices.id')
                    ->whereNull('deleted_at')
                    ->latest('id')
                    ->limit(1);
            }, 'metode_pembayaran')
            ->where('office_id', $officeId)
            ->whereBetween('tgl_invoice', [$startDate, $endDate]);

        $this->applyCommonFilters($paymentsQuery, $request, $mitraId, $search, false);
        $this->applySorting($paymentsQuery, $request, 'tgl_invoice', 'desc');
        $data = $paymentsQuery->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Nota');

        $allHeaders = [
            0 => 'Status', 1 => 'Tanggal', 2 => 'No. Nota', 3 => 'Metode', 4 => 'No. Mitra',
            5 => 'Nama Mitra', 6 => 'Qty', 7 => 'Total Nota', 8 => 'Terbayar', 9 => 'Sisa Piutang',
        ];

        $visibleHeaders = ['No'];
        $mapping = [];
        foreach ($allHeaders as $idx => $name) {
            if (! in_array($idx, $hiddenColumns)) {
                $visibleHeaders[] = $name;
                $mapping[] = $idx;
            }
        }

        $row = 1;
        $this->writeHeader($sheet, $visibleHeaders, $row);
        $row++;
        foreach ($data as $i => $item) {
            $colIdx = 1;
            $sheet->setCellValue([$colIdx++, $row], $i + 1);
            foreach ($mapping as $mIdx) {
                $val = match ($mIdx) {
                    0 => $item->status_pembayaran,
                    1 => Carbon::parse($item->tgl_invoice)->format('d-m-Y'),
                    2 => $item->nomor_invoice,
                    3 => $item->metode_pembayaran ?? 'Belum Bayar',
                    4 => $item->mitra?->nomor_mitra,
                    5 => $item->mitra?->nama,
                    6 => (float) $item->total_qty,
                    7 => (float) $item->total_akhir,
                    8 => (float) $item->jumlah_bayar,
                    9 => (float) ($item->total_akhir - $item->jumlah_bayar),
                    default => ''
                };
                $sheet->setCellValue([$colIdx++, $row], $val);
            }
            $row++;
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($visibleHeaders));
        $this->applyBodyStyle($sheet, 'A1', $lastCol.($row - 1));

        return $this->streamSpreadsheet($spreadsheet, 'Laporan_Pembayaran_'.date('Y-m-d').'.xlsx');
    }

    private function exportSoldProductsSpreadsheet($request, $startDate, $endDate, $officeId, $mitraId, $search, $hiddenColumns)
    {
        $metric = $request->input('product_metric', 'both');

        // Re-use logic to get ALL data
        $invoiceIdsQuery = DB::table('invoices')->join('payments', 'invoices.id', '=', 'payments.invoice_id')->where('invoices.office_id', $officeId)->where('invoices.tipe_invoice', 'Sales')->whereNull('payments.deleted_at')->whereNull('invoices.deleted_at')->whereBetween('payments.tgl_pembayaran', [$startDate, $endDate])->select('invoices.id');
        $this->applyCommonFilters($invoiceIdsQuery, $request, $mitraId, $search, false);
        $invoiceIds = $invoiceIdsQuery->pluck('id')->unique();

        $taxSumSub = DB::table('invoice_item_taxes')->whereNull('deleted_at')->select('invoice_item_id', DB::raw('SUM(nilai_pajak_diterapkan) as total_tax'))->groupBy('invoice_item_id');
        $query = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.produk_id', '=', 'products.id')
            ->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->leftJoinSub($taxSumSub, 'tax_sum', function ($join) {
                $join->on('invoice_items.id', '=', 'tax_sum.invoice_item_id');
            })
            ->whereIn('invoice_items.invoice_id', $invoiceIds)
            ->select(
                'products.nama_produk',
                'products.sku_kode',
                'product_categories.nama_kategori',
                'products.satuan',
                DB::raw('SUM(invoice_items.qty) as total_qty'),
                DB::raw('SUM(invoice_items.total_harga_item + COALESCE(tax_sum.total_tax, 0)) as total_value')
            )
            ->groupBy('products.id', 'products.nama_produk', 'products.sku_kode', 'products.satuan', 'product_categories.nama_kategori');

        $prodIdFilter = $this->extractProductFilter($request);
        if (! empty($prodIdFilter)) {
            $query->whereIn('invoice_items.produk_id', $prodIdFilter);
        }
        $this->applySorting($query, $request, 'products.nama_produk', 'asc');
        $data = $query->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Produk Terjual');

        // Dynamic Header Mapping based on metric
        $allHeaders = [
            0 => 'Nama Produk',
            1 => 'Kode (SKU)',
            2 => 'Kategori',
            3 => 'Satuan',
        ];

        if ($metric === 'qty') {
            $allHeaders[4] = 'Kuantitas';
        } elseif ($metric === 'value') {
            $allHeaders[4] = 'Value (Rp)';
        } else {
            $allHeaders[4] = 'Kuantitas';
            $allHeaders[5] = 'Value (Rp)';
        }

        $visibleHeaders = ['No'];
        $mapping = [];
        foreach ($allHeaders as $idx => $name) {
            if (! in_array($idx, $hiddenColumns)) {
                $visibleHeaders[] = $name;
                $mapping[] = $idx;
            }
        }

        $row = 1;
        $this->writeHeader($sheet, $visibleHeaders, $row);
        $row++;
        foreach ($data as $i => $item) {
            $colIdx = 1;
            $sheet->setCellValue([$colIdx++, $row], $i + 1);
            foreach ($mapping as $mIdx) {
                $val = match ($mIdx) {
                    0 => $item->nama_produk,
                    1 => $item->sku_kode,
                    2 => $item->nama_kategori,
                    3 => $item->satuan,
                    4 => ($metric === 'value' ? (float) $item->total_value : (float) $item->total_qty),
                    5 => (float) $item->total_value,
                    default => ''
                };
                $sheet->setCellValue([$colIdx++, $row], $val);
            }
            $row++;
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($visibleHeaders));
        $this->applyBodyStyle($sheet, 'A1', $lastCol.($row - 1));

        return $this->streamSpreadsheet($spreadsheet, 'Laporan_Produk_Terjual_'.date('Y-m-d').'.xlsx');
    }

    private function exportInvoiceItemsSpreadsheet($request, $startDate, $endDate, $officeId, $mitraId, $search, $hiddenColumns)
    {
        $query = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.produk_id', '=', 'products.id')
            ->leftJoin('mitras', 'invoices.mitra_id', '=', 'mitras.id')
            ->where('invoices.office_id', $officeId)
            ->whereBetween('invoices.tgl_invoice', [$startDate, $endDate])
            ->select(
                'invoices.status_pembayaran',
                'invoices.tgl_invoice',
                'invoices.nomor_invoice',
                'products.nama_produk',
                'mitras.nama as nama_mitra',
                'invoice_items.qty',
                'invoice_items.harga_satuan',
                'invoice_items.diskon_nilai',
                DB::raw('(invoice_items.diskon_nilai * invoice_items.qty) as total_diskon'),
                'invoice_items.id as item_id',
                'invoice_items.total_harga_item'
            );

        $this->applyCommonFilters($query, $request, $mitraId, $search, true, true);
        $this->applySorting($query, $request, 'invoices.tgl_invoice', 'desc');
        $data = $query->get();

        // Add tax manually as we did in index
        $itemIds = $data->pluck('item_id')->toArray();
        $taxSums = [];
        if (! empty($itemIds)) {
            $taxSums = DB::table('invoice_item_taxes')->whereIn('invoice_item_id', $itemIds)->whereNull('deleted_at')->select('invoice_item_id', DB::raw('SUM(nilai_pajak_diterapkan) as total_tax'))->groupBy('invoice_item_id')->pluck('total_tax', 'invoice_item_id')->toArray();
        }

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Per Produk');

        $allHeaders = [
            0 => 'Status', 1 => 'Tanggal', 2 => 'No. Nota', 3 => 'Produk', 4 => 'Mitra',
            5 => 'Qty', 6 => 'Harga', 7 => 'Disc. Item', 8 => 'Tot. Disc', 9 => 'Total Akhir',
        ];

        $visibleHeaders = ['No'];
        $mapping = [];
        foreach ($allHeaders as $idx => $name) {
            if (! in_array($idx, $hiddenColumns)) {
                $visibleHeaders[] = $name;
                $mapping[] = $idx;
            }
        }

        $row = 1;
        $this->writeHeader($sheet, $visibleHeaders, $row);
        $row++;
        foreach ($data as $i => $item) {
            $colIdx = 1;
            $sheet->setCellValue([$colIdx++, $row], $i + 1);
            $tax = $taxSums[$item->item_id] ?? 0;
            $totalAkhir = $item->total_harga_item + $tax;

            foreach ($mapping as $mIdx) {
                $val = match ($mIdx) {
                    0 => $item->status_pembayaran,
                    1 => Carbon::parse($item->tgl_invoice)->format('d-m-Y'),
                    2 => $item->nomor_invoice,
                    3 => $item->nama_produk,
                    4 => $item->nama_mitra,
                    5 => (float) $item->qty,
                    6 => (float) $item->harga_satuan,
                    7 => (float) $item->diskon_nilai,
                    8 => (float) $item->total_diskon,
                    9 => (float) $totalAkhir,
                    default => ''
                };
                $sheet->setCellValue([$colIdx++, $row], $val);
            }
            $row++;
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($visibleHeaders));
        $this->applyBodyStyle($sheet, 'A1', $lastCol.($row - 1));

        return $this->streamSpreadsheet($spreadsheet, 'Laporan_Nota_Per_Produk_'.date('Y-m-d').'.xlsx');
    }

    // --- Core Spreadsheet Helpers ---

    private function writeHeader($sheet, $headers, $row)
    {
        foreach ($headers as $index => $header) {
            $col = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($col.$row, $header);
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A'.$row.':'.$lastCol.$row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }

    private function applyBodyStyle($sheet, $start, $end)
    {
        $sheet->getStyle($start.':'.$end)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Auto size columns
        $lastCol = $sheet->getHighestColumn();
        $lastColIdx = Coordinate::columnIndexFromString($lastCol);
        for ($i = 1; $i <= $lastColIdx; $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
    }

    private function streamSpreadsheet($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Cache-Control' => 'max-age=0',
            ]
        );
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

    private function applySorting($query, Request $request, $defaultSortField, $defaultSortDir)
    {
        $sortBy = $request->sort_by ?: $defaultSortField;
        $sortDir = $request->sort_dir ?: $defaultSortDir;

        if ($sortBy === 'status_pembayaran') {
            $query->orderByRaw("CASE status_pembayaran 
                WHEN 'Overdue' THEN 1 
                WHEN 'Unpaid' THEN 2 
                WHEN 'Partially Paid' THEN 3 
                WHEN 'Paid' THEN 4 
                WHEN 'Draft' THEN 5 
                ELSE 6 END ".$sortDir);
        } elseif ($sortBy === 'payments.jumlah_bayar' || $sortBy === 'terbayar') {
            $query->orderByRaw('COALESCE(jumlah_bayar, 0) '.$sortDir);
        } elseif ($sortBy === 'sisa_piutang') {
            $query->orderByRaw('sisa_piutang_virtual '.$sortDir);
        } elseif ($sortBy === 'total_diskon') {
            $query->orderByRaw('(invoice_items.qty * invoice_items.diskon_nilai) '.$sortDir);
        } elseif ($sortBy === 'total_akhir' && str_contains($query->toSql(), 'invoice_items')) {
            // Special case for invoice items sort by total akhir (including tax sum)
            $query->orderByRaw('( (invoice_items.qty * invoice_items.harga_satuan) - (invoice_items.qty * invoice_items.diskon_nilai) + COALESCE(total_tax, 0) ) '.$sortDir);
        } else {
            // Fix ambiguity for total_akhir if multiple tables joined
            if ($sortBy === 'total_akhir' || $sortBy === 'invoices.total_akhir') {
                $sortBy = 'invoices.total_akhir';
            }
            $query->orderBy($sortBy, $sortDir);
        }
    }

    private function applyCommonFilters($query, Request $request, $mitraId, $search, $hasMitraJoin = false, $isInvoiceItemsQuery = false)
    {
        $prodIdFilter = $this->extractProductFilter($request);
        $invTypeFilter = $request->invoice_type;
        $payStatusFilter = $request->payment_status;
        $showDeleted = $request->show_deleted;

        // Soft delete handling
        if (! $showDeleted) {
            $query->whereNull($query->getModel()->getTable().'.deleted_at');
        }

        // Apply filters
        if (! empty($mitraId)) {
            $query->whereIn('mitra_id', (array) $mitraId);
        }
        if ($invTypeFilter) {
            $query->where('tipe_invoice', $invTypeFilter);
        }
        if ($payStatusFilter) {
            $query->where('status_pembayaran', $payStatusFilter);
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
                $table = $q->getModel()->getTable();
                $q->where($table.'.nomor_invoice', 'like', "%$search%");

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
