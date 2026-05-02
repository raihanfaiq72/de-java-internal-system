<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BulkReport;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BulkReportController extends Controller
{
    public function index()
    {
        $bulkReports = BulkReport::with('generator')
            ->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString();

        return view('Reports.bulk.index', compact('bulkReports'));
    }

    public function storePeriod(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
        ]);

        $month = $request->month;
        $year = $request->year;
        $periodName = Carbon::createFromDate($year, $month, 1)->format('F').' '.$year;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Check if period already exists
        $existing = BulkReport::where('month', $month)
            ->where('year', $year)
            ->first();

        if ($existing) {
            return redirect()->route('bulk-reports.index')
                ->with('error', "Periode $periodName sudah ada dalam daftar laporan massal.");
        }

        $bulkReport = BulkReport::create([
            'period_name' => $periodName,
            'slug' => Str::slug($periodName),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'month' => $month,
            'year' => $year,
            'status' => 'draft',
            'generated_by' => auth()->id(),
        ]);

        // Send notification to all users
        $this->sendBulkReportNotification('bulk_report_created', [
            'title' => 'Laporan Massal Baru',
            'message' => "Periode $periodName telah ditambahkan ke daftar laporan massal.",
            'bulk_report_id' => $bulkReport->id,
            'period_name' => $periodName,
            'created_by' => auth()->user()->name,
        ]);

        return redirect()->route('bulk-reports.index')->with('success', "Periode $periodName telah ditambahkan ke daftar laporan massal.");
    }

    public function detail(BulkReport $bulkReport)
    {
        // Get all data for the period
        $salesData = $this->getSalesData($bulkReport->start_date, $bulkReport->end_date);
        $paymentsData = $this->getPaymentsData($bulkReport->start_date, $bulkReport->end_date);
        $arAgingData = $this->getARAgingData($bulkReport->end_date);
        $profitLossData = $this->getProfitLossData($bulkReport->start_date, $bulkReport->end_date);

        return view('Reports.bulk.preview', compact('bulkReport', 'salesData', 'paymentsData', 'arAgingData', 'profitLossData'));
    }

    public function preview(BulkReport $bulkReport)
    {
        // Get all data for the period
        $salesData = $this->getSalesData($bulkReport->start_date, $bulkReport->end_date);
        $paymentsData = $this->getPaymentsData($bulkReport->start_date, $bulkReport->end_date);
        $arAgingData = $this->getARAgingData($bulkReport->end_date);
        $profitLossData = $this->getProfitLossData($bulkReport->start_date, $bulkReport->end_date);

        return view('Reports.bulk.pdf', compact('bulkReport', 'salesData', 'paymentsData', 'arAgingData', 'profitLossData'));
    }

    public function generatePDF(BulkReport $bulkReport)
    {
        $bulkReport->update([
            'status' => 'generated',
            'generated_at' => now(),
        ]);

        // Get all data for period
        $salesData = $this->getSalesData($bulkReport->start_date, $bulkReport->end_date);
        $paymentsData = $this->getPaymentsData($bulkReport->start_date, $bulkReport->end_date);
        $arAgingData = $this->getARAgingData($bulkReport->end_date);
        $profitLossData = $this->getProfitLossData($bulkReport->start_date, $bulkReport->end_date);

        // Send notification
        $this->sendBulkReportNotification('bulk_report_generated', [
            'title' => 'PDF Laporan Massal Tersedia',
            'message' => "PDF laporan massal untuk periode {$bulkReport->period_name} telah siap diunduh.",
            'bulk_report_id' => $bulkReport->id,
            'period_name' => $bulkReport->period_name,
            'generated_by' => auth()->user()->name,
        ]);

        return view('Reports.bulk.pdf', compact('bulkReport', 'salesData', 'paymentsData', 'arAgingData', 'profitLossData'));
    }

    public function markAsPrinted(BulkReport $bulkReport)
    {
        $bulkReport->update([
            'status' => 'printed',
        ]);

        // Send notification
        $this->sendBulkReportNotification('bulk_report_printed', [
            'title' => 'Laporan Massal Dicetak',
            'message' => "Laporan massal untuk periode {$bulkReport->period_name} telah ditandai sebagai telah dicetak.",
            'bulk_report_id' => $bulkReport->id,
            'period_name' => $bulkReport->period_name,
            'marked_by' => auth()->user()->name,
        ]);

        return redirect()->route('bulk-reports.index')->with('success', 'Laporan telah ditandai sebagai telah dicetak.');
    }

    public function destroy(BulkReport $bulkReport)
    {
        // Hapus file jika ada
        if ($bulkReport->file_path && Storage::exists($bulkReport->file_path)) {
            Storage::delete($bulkReport->file_path);
        }

        $periodName = $bulkReport->period_name;
        $bulkReport->delete();

        // Send notification
        $this->sendBulkReportNotification('bulk_report_deleted', [
            'title' => 'Laporan Massal Dihapus',
            'message' => "Laporan massal untuk periode {$periodName} telah dihapus dari sistem.",
            'period_name' => $periodName,
            'deleted_by' => auth()->user()->name,
        ]);

        return redirect()->route('bulk-reports.index')->with('success', 'Laporan massal telah dihapus.');
    }

    private function sendBulkReportNotification($type, $data)
    {
        $notificationType = $type === 'bulk_report_created' ? 'success' :
                         ($type === 'bulk_report_generated' ? 'info' : 'warning');

        NotificationService::notifyByPermission(
            'bulk-reports.index',
            $data['title'],
            $data['message'],
            route('bulk-reports.index'),
            $notificationType,
            $data
        );
    }

    private function getSalesData($startDate, $endDate)
    {
        return Invoice::with([
            'mitra' => function ($query) {
                $query->withTrashed();
            },
            'items',
        ])
            ->where('tipe_invoice', 'Sales')
            ->whereBetween('tgl_invoice', [$startDate, $endDate])
            ->orderBy('tgl_invoice', 'desc')
            ->get()
            ->map(function ($invoice) {
                // Ensure dates are Carbon objects
                $invoice->tgl_invoice = is_string($invoice->tgl_invoice)
                    ? Carbon::parse($invoice->tgl_invoice)
                    : ($invoice->tgl_invoice instanceof Carbon ? $invoice->tgl_invoice : Carbon::parse($invoice->tgl_invoice));

                return $invoice;
            });
    }

    private function getPaymentsData($startDate, $endDate)
    {
        return Payment::with([
            'invoice' => function ($query) {
                $query->where('tipe_invoice', 'Sales');
            },
            'invoice.mitra' => function ($query) {
                $query->withTrashed();
            },
        ])
            ->whereHas('invoice', function ($query) {
                $query->where('tipe_invoice', 'Sales');
            })
            ->whereBetween('tgl_pembayaran', [$startDate, $endDate])
            ->orderBy('tgl_pembayaran', 'desc')
            ->get()
            ->map(function ($payment) {
                // Ensure dates are Carbon objects
                $payment->tgl_pembayaran = is_string($payment->tgl_pembayaran)
                    ? Carbon::parse($payment->tgl_pembayaran)
                    : $payment->tgl_pembayaran;

                return $payment;
            });
    }

    private function getARAgingData($asOfDate)
    {
        $invoices = Invoice::with([
            'mitra' => function ($query) {
                $query->withTrashed();
            },
        ])
            ->where('tipe_invoice', 'Sales')
            ->whereIn('status_pembayaran', ['Partial', 'Unpaid'])
            ->where('tgl_invoice', '<=', $asOfDate)
            ->get();

        $agingData = [];
        foreach ($invoices as $invoice) {
            // Convert due date to Carbon if it's a string
            $dueDate = is_string($invoice->tgl_jatuh_tempo)
                ? Carbon::parse($invoice->tgl_jatuh_tempo)
                : $invoice->tgl_jatuh_tempo;

            $daysOverdue = $asOfDate->diffInDays($dueDate);

            $agingBuckets = [
                'current' => $daysOverdue <= 0 ? $invoice->total_akhir : 0,
                '1_15' => ($daysOverdue >= 1 && $daysOverdue <= 15) ? $invoice->total_akhir : 0,
                '16_30' => ($daysOverdue >= 16 && $daysOverdue <= 30) ? $invoice->total_akhir : 0,
                '31_45' => ($daysOverdue >= 31 && $daysOverdue <= 45) ? $invoice->total_akhir : 0,
                '46_60' => ($daysOverdue >= 46 && $daysOverdue <= 60) ? $invoice->total_akhir : 0,
                '61_plus' => $daysOverdue >= 61 ? $invoice->total_akhir : 0,
            ];

            $agingData[] = [
                'customer' => $invoice->mitra->nama,
                'invoice_no' => $invoice->nomor_invoice,
                'due_date' => $dueDate->format('d/m/Y'),
                'days_overdue' => $daysOverdue,
                'total_amount' => (float) $invoice->total_akhir,
                'paid_amount' => (float) $invoice->payment()->sum('jumlah_bayar'),
                'remaining_amount' => (float) ($invoice->total_akhir - $invoice->payment()->sum('jumlah_bayar')),
                'aging_buckets' => $agingBuckets,
            ];
        }

        return collect($agingData);
    }

    private function getProfitLossData($startDate, $endDate)
    {
        // Revenue from invoices
        $salesRevenue = (float) Invoice::whereBetween('tgl_invoice', [$startDate, $endDate])
            ->where('tipe_invoice', 'Sales')
            ->sum('total_akhir');

        // Cost of goods sold using products purchase price
        $costOfGoodsSold = (float) InvoiceItem::whereHas('invoice', function ($query) use ($startDate, $endDate) {
            $query->where('tipe_invoice', 'Sales')
                  ->whereBetween('tgl_invoice', [$startDate, $endDate]);
        })
            ->join('products', 'invoice_items.produk_id', '=', 'products.id')
            ->selectRaw('SUM(invoice_items.qty * products.harga_beli) as total_cogs')
            ->value('total_cogs') ?? 0;

        // Alternative approach using collection for better performance
        // $costOfGoodsSold = Invoice::with('items')
        //     ->whereBetween('tgl_invoice', [$startDate, $endDate])
        //     ->get()
        //     ->sum(function ($invoice) {
        //         return $invoice->items->sum(function ($item) {
        //             return $item->qty * $item->harga_satuan;
        //         });
        //     });

        // Other revenues
        $otherRevenues = 0; // Can be expanded later

        // Operating expenses (can be expanded with actual expense tracking)
        $operatingExpenses = 0; // Placeholder

        // Gross profit
        $grossProfit = $salesRevenue - $costOfGoodsSold;

        // Net profit
        $netProfit = $grossProfit + $otherRevenues - $operatingExpenses;

        return [
            'sales_revenue' => $salesRevenue,
            'cost_of_goods_sold' => $costOfGoodsSold,
            'other_revenues' => $otherRevenues,
            'operating_expenses' => $operatingExpenses,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'period' => $startDate->format('d M Y').' - '.$endDate->format('d M Y'),
        ];
    }
}
