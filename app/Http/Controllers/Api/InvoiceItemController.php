<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceItemController extends Controller
{
    public function index(Request $request)
    {
        $query = InvoiceItem::with(['product', 'taxes.tax'])
            ->whereHas('invoice', function ($q) {
                $q->where('office_id', session('active_office_id'));
            });

        if ($request->invoice_id) {
            $query->where('invoice_id', $request->invoice_id);
        }

        $data = $query->latest()->paginate(10);

        return apiResponse(true, 'Data item invoice', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'qty' => 'required|numeric|min:0.01',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (!session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        // Validate Invoice ownership
        $invoice = \App\Models\Invoice::where('id', $request->invoice_id)
            ->where('office_id', session('active_office_id'))
            ->first();

        if (!$invoice) {
            return apiResponse(false, 'Invoice tidak valid untuk outlet ini', null, null, 422);
        }

        $item = InvoiceItem::create($request->all());

        return apiResponse(true, 'Item invoice ditambahkan', $item, null, 201);
    }

    public function show($id)
    {
        $item = InvoiceItem::with(['product', 'taxes.tax'])
            ->whereHas('invoice', function ($q) {
                $q->where('office_id', session('active_office_id'));
            })
            ->find($id);

        if (!$item) {
            return apiResponse(false, 'Item invoice tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail item invoice', $item);
    }

    public function update(Request $request, $id)
    {
        $item = InvoiceItem::whereHas('invoice', function ($q) {
                $q->where('office_id', session('active_office_id'));
            })
            ->find($id);

        if (!$item) {
            return apiResponse(false, 'Item invoice tidak ditemukan', null, null, 404);
        }

        $before = $item->toArray();
        $item->update($request->all());

        return apiResponse(true, 'Item invoice diperbarui', $item);
    }

    public function destroy($id)
    {
        $item = InvoiceItem::whereHas('invoice', function ($q) {
                $q->where('office_id', session('active_office_id'));
            })
            ->find($id);

        if (!$item) {
            return apiResponse(false, 'Item invoice tidak ditemukan', null, null, 404);
        }

        $before = $item->toArray();
        $item->delete();

        return apiResponse(true, 'Item invoice dihapus');
    }

}