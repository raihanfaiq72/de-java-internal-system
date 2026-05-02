<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BulkReport;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\COAGroup;
use App\Models\JournalDetail;
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
            'payment',
        ])
            ->where('tipe_invoice', 'Sales')
            ->whereBetween('tgl_invoice', [$startDate, $endDate])
            ->orderBy('tgl_invoice', 'asc')
            ->get()
            ->map(function ($invoice) {
                // Ensure dates are Carbon objects
                $invoice->tgl_invoice = is_string($invoice->tgl_invoice)
                    ? Carbon::parse($invoice->tgl_invoice)
                    : ($invoice->tgl_invoice instanceof Carbon ? $invoice->tgl_invoice : Carbon::parse($invoice->tgl_invoice));

                // Calculate full payment vs down payment
                $invoiceTotal = $invoice->total_akhir;
                $paidAmount = 0;
                $downPaymentAmount = 0;

                foreach ($invoice->payment as $payment) {
                    if ($payment->jumlah_bayar >= $invoiceTotal) {
                        $paidAmount += $payment->jumlah_bayar;
                    } else {
                        $downPaymentAmount += $payment->jumlah_bayar;
                    }
                }

                $invoice->full_payment_amount = $paidAmount;
                $invoice->down_payment_amount = $downPaymentAmount;
                $invoice->amount_due = $invoiceTotal - $paidAmount - $downPaymentAmount;

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
            ->orderBy('tgl_pembayaran', 'asc')
            ->get()
            ->map(function ($payment) {
                // Ensure dates are Carbon objects
                $payment->tgl_pembayaran = is_string($payment->tgl_pembayaran)
                    ? Carbon::parse($payment->tgl_pembayaran)
                    : $payment->tgl_pembayaran;

                // Calculate full payment vs down payment
                $invoiceTotal = $payment->invoice ? $payment->invoice->total_akhir : 0;
                $paymentAmount = $payment->jumlah_bayar;

                if ($paymentAmount >= $invoiceTotal) {
                    $payment->full_payment_amount = $paymentAmount;
                    $payment->down_payment_amount = 0;
                } else {
                    $payment->full_payment_amount = 0;
                    $payment->down_payment_amount = $paymentAmount;
                }

                $payment->amount_due = $invoiceTotal - $payment->full_payment_amount - $payment->down_payment_amount;

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
        $officeId = session('active_office_id');

        // Define groups that appear in P&L
        // 4000: Revenue, 5000: COGS, 6000: Expenses, etc.
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
            ->orderBy('kode_kelompok')
            ->get();

        $report = [];
        $totalRevenue = 0;
        $totalExpense = 0;

        foreach ($groups as $group) {
            $groupData = [
                'code' => $group->kode_kelompok,
                'name' => $group->nama_kelompok,
                'types' => [],
                'total_balance' => 0,
            ];

            foreach ($group->type as $type) {
                $typeData = [
                    'name' => $type->nama_tipe,
                    'accounts' => [],
                    'total_balance' => 0,
                ];

                foreach ($type->coas as $coa) {
                    $m = $movements->get($coa->id);
                    $debit = $m ? $m->total_debit : 0;
                    $credit = $m ? $m->total_credit : 0;

                    // Normal balance: Credit for Revenue (4xxx, 7xxx), Debit for others
                    if (in_array($group->kode_kelompok, [4000, 7001])) {
                        $balance = $credit - $debit;
                    } else {
                        $balance = $debit - $credit;
                    }

                    $typeData['accounts'][] = [
                        'code' => $coa->kode_akun,
                        'name' => $coa->nama_akun,
                        'balance' => $balance,
                    ];
                    $typeData['total_balance'] += $balance;
                }

                $groupData['types'][] = $typeData;
                $groupData['total_balance'] += $typeData['total_balance'];
            }

            if (in_array($group->kode_kelompok, [4000, 7001])) {
                $totalRevenue += $groupData['total_balance'];
            } else {
                $totalExpense += $groupData['total_balance'];
            }

            $report[] = $groupData;
        }

        return [
            'report' => $report,
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_profit' => $totalRevenue - $totalExpense,
            'period_start' => $startDate->format('Y-m-d'),
            'period_end' => $endDate->format('Y-m-d'),
        ];
    }
}
