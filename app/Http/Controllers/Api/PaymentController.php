<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('invoice');

        if ($request->invoice_id) {
            $query->where('invoice_id', $request->invoice_id);
        }

        $data = $query->latest()->paginate(10);
        return apiResponse(true, 'Data pembayaran', $data);
    }

    public function show($id)
    {
        $data = Payment::with('invoice')->find($id);
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

            $invoice = Invoice::lockForUpdate()->find($request->invoice_id);

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

            $payment = Payment::create($request->all());

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

    public function update(Request $request, $id)
    {
        $data = Payment::find($id);
        if (!$data) {
            return apiResponse(false, 'Pembayaran tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->update($request->all());

        $this->logActivity('Update', 'payments', $id, $before, $data);

        return apiResponse(true, 'Pembayaran berhasil diperbarui', $data);
    }

    public function destroy($id)
    {
        $data = Payment::find($id);
        if (!$data) {
            return apiResponse(false, 'Pembayaran tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        $this->logActivity('Soft Delete', 'payments', $id, $before, null);

        return apiResponse(true, 'Pembayaran berhasil dihapus');
    }

    private function logActivity($tindakan, $tabel, $dataId, $before, $after)
    {
        ActivityLog::create([
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
