<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['invoice.items.product', 'invoice.mitra', 'akun_keuangan'])
            ->where('office_id', session('active_office_id'));

        if ($request->invoice_id) {
            $query->where('invoice_id', $request->invoice_id);
        }

        $data = $query->latest()->paginate(10);
        return apiResponse(true, 'Data pembayaran', $data);
    }

    public function show($id)
    {
        $data = Payment::with('invoice.items.product')
            ->where('office_id', session('active_office_id'))
            ->find($id);
        if (!$data) {
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
            'metode_pembayaran' => 'required|in:Cash,Transfer,Lainnya',
            'jumlah_bayar' => 'required|numeric|min:0.01',
            'akun_keuangan_id' => 'required|exists:chart_of_accounts,id'
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        return DB::transaction(function () use ($request) {

            if (!session()->has('active_office_id')) {
                return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
            }

            $invoice = Invoice::where('office_id', session('active_office_id'))
                ->lockForUpdate()
                ->find($request->invoice_id);

            if (!$invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            $totalSudahDibayar = $invoice->payment()->sum('jumlah_bayar');

            $sisaTagihan = $invoice->total_akhir - $totalSudahDibayar;

            if ($request->jumlah_bayar > $sisaTagihan) {
                return apiResponse(
                    false,
                    'Jumlah bayar melebihi sisa tagihan',
                    [
                        'sisa_tagihan' => $sisaTagihan
                    ],
                    null,
                    422
                );
            }

            if (!session()->has('active_office_id')) {
                return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
            }

            $paymentData = $request->all();
            $paymentData['office_id'] = session('active_office_id');

            $payment = Payment::create($paymentData);

            $totalSetelahBayar = round($totalSudahDibayar + $request->jumlah_bayar, 2);
            $totalInvoice      = round($invoice->total_akhir, 2);

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
                'status_pembayaran' => $statusPembayaran
            ]);

            $this->logActivity(
                'Create',
                'payments',
                $payment->id,
                null,
                $payment
            );

            return apiResponse(true, 'Pembayaran berhasil dicatat', [
                'payment' => $payment,
                'invoice' => [
                    'id' => $invoice->id,
                    'total_akhir' => $invoice->total_akhir,
                    'total_dibayar' => $totalSetelahBayar,
                    'sisa_tagihan' => $invoice->total_akhir - $totalSetelahBayar,
                    'status_pembayaran' => $statusPembayaran
                ]
            ], null, 201);
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {

            $payment = Payment::where('office_id', session('active_office_id'))
                ->lockForUpdate()
                ->find($id);

            if (!$payment) {
                return apiResponse(false, 'Pembayaran tidak ditemukan', null, null, 404);
            }

            $invoice = Invoice::lockForUpdate()->find($payment->invoice_id);

            if (!$invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            $before = $payment->toArray();

            $payment->delete();

            $totalBayar = round(
                $invoice->payment()->sum('jumlah_bayar'),
                2
            );

            $totalInvoice = round($invoice->total_akhir, 2);
            $hariIni      = Carbon::today();

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
                'status_pembayaran' => $statusPembayaran
            ]);

            $this->logActivity(
                'Soft Delete',
                'payments',
                $payment->id,
                $before,
                [
                    'invoice_id' => $invoice->id,
                    'status_pembayaran_baru' => $statusPembayaran,
                    'total_bayar_setelah_delete' => $totalBayar
                ]
            );

            return apiResponse(true, 'Pembayaran berhasil dihapus dan status invoice diperbarui', [
                'invoice_id' => $invoice->id,
                'total_invoice' => $totalInvoice,
                'total_dibayar' => $totalBayar,
                'status_pembayaran' => $statusPembayaran
            ]);
        });
    }

    private function logActivity($tindakan, $tabel, $dataId, $before, $after)
    {
        ActivityLog::create([
            'office_id' => session('active_office_id'),
            'user_id' => 1,
            'tindakan' => $tindakan,
            'tabel_terkait' => $tabel,
            'data_id' => $dataId,
            'data_sebelum' => $before,
            'data_sesudah' => $after,
            'ip_address' => request()->ip()
        ]);
    }
}
