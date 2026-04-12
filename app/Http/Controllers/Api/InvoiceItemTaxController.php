<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvoiceItemTax;
use Illuminate\Http\Request;

class InvoiceItemTaxController extends Controller
{
    public function index(Request $request)
    {
        $query = InvoiceItemTax::with(['invoiceItem'])
            ->whereHas('invoiceItem.invoice', function ($q) {
                $q->where('office_id', session('active_office_id'));
            });

        if ($request->invoice_item_id) {
            $query->where('invoice_item_id', $request->invoice_item_id);
        }

        $data = $query->latest()->paginate($request->get('per_page', 10))->withQueryString();

        return apiResponse(true, 'Data pajak item invoice', $data);
    }

    public function store(Request $request)
    {
        if (! session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        $data = InvoiceItemTax::create($request->all());

        return apiResponse(true, 'Pajak item invoice ditambahkan', $data, null, 201);
    }

    public function destroy($id)
    {
        $data = InvoiceItemTax::whereHas('invoiceItem.invoice', function ($q) {
            $q->where('office_id', session('active_office_id'));
        })
            ->find($id);

        if (! $data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        return apiResponse(true, 'Pajak item invoice dihapus');
    }
}
