<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\InvoiceItemTaxe;
use Illuminate\Http\Request;

class InvoiceItemTaxController extends Controller
{
    public function index(Request $request)
    {
        $query = InvoiceItemTaxe::with(['invoiceItem']);

        if ($request->invoice_item_id) {
            $query->where('invoice_item_id', $request->invoice_item_id);
        }

        $data = $query->latest()->paginate(10);

        return apiResponse(true, 'Data pajak item invoice', $data);
    }

    public function store(Request $request)
    {
        $data = InvoiceItemTaxe::create($request->all());

        $this->logActivity('Create', 'invoice_item_taxes', $data->id, null, $data);

        return apiResponse(true, 'Pajak item invoice ditambahkan', $data, null, 201);
    }

    public function destroy($id)
    {
        $data = InvoiceItemTaxe::find($id);
        if (!$data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        $this->logActivity('Soft Delete', 'invoice_item_taxes', $id, $before, null);

        return apiResponse(true, 'Pajak item invoice dihapus');
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
