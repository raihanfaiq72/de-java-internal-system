<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\JournalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index(Request $request)
    {
        $query = Payment::with([
            'invoice' => fn($q) => $q->withSum('payment', 'jumlah_bayar'),
            'invoice.items.product',
            'invoice.mitra',
            'akun_keuangan'
        ])
            ->where('office_id', session('active_office_id'));

        if ($request->tipe_receipt) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('tipe_invoice', $request->tipe_receipt);
            });
        }

        if ($request->metode_pembayaran) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_pembayaran', 'like', '%' . $request->search . '%')
                    ->orWhereHas('invoice', function ($qi) use ($request) {
                        $qi->where('nomor_invoice', 'like', '%' . $request->search . '%')
                            ->orWhereHas('mitra', function ($qm) use ($request) {
                                $qm->where('nama', 'like', '%' . $request->search . '%');
                            });
                    });
            });
        }

        if ($request->invoice_id) {
            $query->where('invoice_id', $request->invoice_id);
        }

        $perPage = $request->get('per_page', 10);
        if ($perPage >= 1000) {
            $data = $query->latest()->get();
        } else {
            $data = $query->latest()->paginate($perPage)->withQueryString();
        }

        return apiResponse(true, 'Data pembayaran', $data);
    }

    public function show($id)
    {
        $data = Payment::with([
                'invoice' => fn($q) => $q->withSum('payment', 'jumlah_bayar'),
                'invoice.mitra',
                'akun_keuangan'
            ])
            ->where('office_id', session('active_office_id'))
            ->find($id);
        if (! $data) {
            return apiResponse(false, 'Pembayaran tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail pembayaran', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'nomor_pembayaran' => 'required|unique:payments,nomor_pembayaran',
            'tgl_pembayaran' => 'required|date',
            'metode_pembayaran' => 'required|in:Cash,Transfer,Lainnya,Cek/Giro',
            'jumlah_bayar' => 'required|numeric|min:0.01',
            'akun_keuangan_id' => 'required|exists:financial_accounts,id',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        return DB::transaction(function () use ($request) {

            if (! session()->has('active_office_id')) {
                return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
            }

            $invoice = Invoice::where('office_id', session('active_office_id'))
                ->lockForUpdate()
                ->find($request->invoice_id);

            if (! $invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            $totalSudahDibayar = $invoice->payment()->sum('jumlah_bayar');

            $sisaTagihan = $invoice->total_akhir - $totalSudahDibayar;

            if ($request->jumlah_bayar > $sisaTagihan) {
                return apiResponse(
                    false,
                    'Jumlah bayar melebihi sisa tagihan',
                    [
                        'sisa_tagihan' => $sisaTagihan,
                    ],
                    null,
                    422
                );
            }

            if (! session()->has('active_office_id')) {
                return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
            }

            $paymentData = $request->all();
            $paymentData['office_id'] = session('active_office_id');

            $payment = Payment::create($paymentData);

            $totalSetelahBayar = round($totalSudahDibayar + $request->jumlah_bayar, 2);
            $totalInvoice = round($invoice->total_akhir, 2);

            if ($totalSetelahBayar <= 0) {
                $statusPembayaran = 'Unpaid';
            } elseif ($totalSetelahBayar < $totalInvoice) {
                $statusPembayaran = 'Partially Paid';
            } else {
                $statusPembayaran = 'Paid';
            }

            if (
                $invoice->status_pembayaran === 'Overdue' &&
                $totalSetelahBayar < $invoice->total_akhir
            ) {
                $statusPembayaran = 'Overdue';
            }

            $invoice->update([
                'status_pembayaran' => $statusPembayaran,
            ]);

            // Automatic Journal Entry
            $this->journalService->recordPayment($payment);

            return apiResponse(true, 'Pembayaran berhasil dicatat', [
                'payment' => $payment,
                'invoice' => [
                    'id' => $invoice->id,
                    'total_akhir' => $invoice->total_akhir,
                    'total_dibayar' => $totalSetelahBayar,
                    'sisa_tagihan' => $invoice->total_akhir - $totalSetelahBayar,
                    'status_pembayaran' => $statusPembayaran,
                ],
            ], null, 201);
        });
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nomor_pembayaran' => 'required|string|max:255',
            'tgl_pembayaran' => 'required|date',
            'metode_pembayaran' => 'required|string|max:255',
            'jumlah_bayar' => 'required|numeric|min:0.01',
            'catatan' => 'nullable|string|max:1000',
            'ref_no' => 'nullable|string|max:255',
            'akun_keuangan_id' => 'required|exists:financial_accounts,id',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        return DB::transaction(function () use ($request, $id) {
            $payment = Payment::where('office_id', session('active_office_id'))
                ->lockForUpdate()
                ->find($id);

            if (! $payment) {
                return apiResponse(false, 'Pembayaran tidak ditemukan', null, null, 404);
            }

            $invoice = Invoice::lockForUpdate()->find($payment->invoice_id);
            if (! $invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            // Calculate old and new amounts
            $oldJumlahBayar = $payment->jumlah_bayar;
            $newJumlahBayar = $request->jumlah_bayar;
            $difference = $newJumlahBayar - $oldJumlahBayar;

            // Check if new amount exceeds remaining balance
            $totalSudahDibayar = $invoice->payment()
                ->where('id', '!=', $id) // Exclude current payment
                ->sum('jumlah_bayar');
            
            $totalSetelahUpdate = $totalSudahDibayar + $newJumlahBayar;
            $totalInvoice = $invoice->total_akhir;

            if ($totalSetelahUpdate > $totalInvoice) {
                return apiResponse(false, 'Total pembayaran melebihi jumlah invoice', [
                    'total_invoice' => $totalInvoice,
                    'total_dibayar' => $totalSetelahUpdate,
                    'max_bayar' => $totalInvoice - $totalSudahDibayar,
                ], null, 422);
            }

            // Update payment
            $payment->update([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'tgl_pembayaran' => $request->tgl_pembayaran,
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah_bayar' => $newJumlahBayar,
                'catatan' => $request->catatan,
                'ref_no' => $request->ref_no,
                'akun_keuangan_id' => $request->akun_keuangan_id,
            ]);

            // Update invoice status
            if ($totalSetelahUpdate <= 0) {
                $statusPembayaran = 'Unpaid';
            } elseif ($totalSetelahUpdate < $totalInvoice) {
                $statusPembayaran = 'Partially Paid';
            } else {
                $statusPembayaran = 'Paid';
            }

            if (
                $invoice->status_pembayaran === 'Overdue' &&
                $totalSetelahUpdate < $totalInvoice
            ) {
                $statusPembayaran = 'Overdue';
            }

            $invoice->update([
                'status_pembayaran' => $statusPembayaran,
            ]);

            // Update journal entries if amount changed
            if ($oldJumlahBayar != $newJumlahBayar) {
                // Delete old journal entries and create new ones
                $this->journalService->updatePaymentJournal($payment);
            }

            return apiResponse(true, 'Kuitansi berhasil diperbarui', [
                'payment' => $payment->fresh(['invoice.mitra', 'akun_keuangan']),
                'invoice' => [
                    'id' => $invoice->id,
                    'total_akhir' => $invoice->total_akhir,
                    'total_dibayar' => $totalSetelahUpdate,
                    'sisa_tagihan' => $invoice->total_akhir - $totalSetelahUpdate,
                    'status_pembayaran' => $statusPembayaran,
                ],
            ]);
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {

            $payment = Payment::where('office_id', session('active_office_id'))
                ->lockForUpdate()
                ->find($id);

            if (! $payment) {
                return apiResponse(false, 'Pembayaran tidak ditemukan', null, null, 404);
            }

            $invoice = Invoice::lockForUpdate()->find($payment->invoice_id);

            if (! $invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            $before = $payment->toArray();

            $payment->delete();

            // Delete associated journal
            $this->journalService->deletePaymentJournal($payment);

            $totalBayar = round(
                $invoice->payment()->sum('jumlah_bayar'),
                2
            );

            $totalInvoice = round($invoice->total_akhir, 2);
            $hariIni = Carbon::today();

            if ($totalBayar <= 0) {

                if ($invoice->tgl_jatuh_tempo && Carbon::parse($invoice->tgl_jatuh_tempo)->lt($hariIni)) {
                    $statusPembayaran = 'Overdue';
                } else {
                    $statusPembayaran = 'Unpaid';
                }

            } elseif ($totalBayar < $totalInvoice) {

                if ($invoice->tgl_jatuh_tempo && Carbon::parse($invoice->tgl_jatuh_tempo)->lt($hariIni)) {
                    $statusPembayaran = 'Overdue';
                } else {
                    $statusPembayaran = 'Partially Paid';
                }

            } else {
                $statusPembayaran = 'Paid';
            }

            $invoice->update([
                'status_pembayaran' => $statusPembayaran,
            ]);

            return apiResponse(true, 'Pembayaran berhasil dihapus dan status invoice diperbarui', [
                'invoice_id' => $invoice->id,
                'total_invoice' => $totalInvoice,
                'total_dibayar' => $totalBayar,
                'status_pembayaran' => $statusPembayaran,
            ]);
        });
    }
}
