<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function index()
    {
        $data = Invoice::with(['mitra', 'items.product', 'payment'])
            ->latest()
            ->paginate(10);

        return apiResponse(true, 'Data invoice', $data);
    }

    public function show($id)
    {
        $data = Invoice::with(['mitra', 'items.product', 'payment'])
            ->find($id);

        if (!$data) {
            return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail invoice', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe_invoice' => 'required|in:Sales,Purchase',
            'nomor_invoice' => 'required|unique:invoices,nomor_invoice',
            'tgl_invoice' => 'required|date',
            'mitra_id' => 'required|exists:mitras,id'
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        $invoice = Invoice::create($request->all());

        $this->logActivity('Create', 'invoices', $invoice->id, null, $invoice);

        return apiResponse(true, 'Invoice berhasil dibuat', $invoice, null, 201);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
        }

        $before = $invoice->toArray();
        $invoice->update($request->all());

        $this->logActivity('Update', 'invoices', $id, $before, $invoice);

        return apiResponse(true, 'Invoice berhasil diperbarui', $invoice);
    }

    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
        }

        $before = $invoice->toArray();
        $invoice->delete();

        $this->logActivity('Soft Delete', 'invoices', $id, $before, null);

        return apiResponse(true, 'Invoice berhasil dihapus');
    }

    public function search($value)
    {
        $data = Invoice::where('nomor_invoice', 'LIKE', "%$value%")
            ->orWhere('ref_no', 'LIKE', "%$value%")
            ->paginate(10);

        return apiResponse(true, 'Hasil pencarian invoice', $data);
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
            'ip_address' => request()->ip(),
        ]);
    }
}
