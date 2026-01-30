<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrderInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class DeliveryOrderInvoiceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->search;

            $data = DeliveryOrderInvoice::with('invoice', 'deliveryOrder')
                ->when($search, function ($q) use ($search) {
                    $q->whereHas('invoice', fn($q2) =>
                    $q2->where('nomor_invoice', 'like', "%{$search}%"));
                })
                ->paginate(10);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'delivery_order_id' => 'required|exists:delivery_orders,id',
                'invoice_id' => 'required|exists:invoices,id',
                'delivery_sequence' => 'required|integer'
            ]);

            if ($validator->fails())
                return apiResponse(false, 'Validation failed', null, $validator->errors(), 422);

            $data = DeliveryOrderInvoice::create($request->all());
            return apiResponse(true, 'Added', $data);
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to add invoice', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $item = DeliveryOrderInvoice::find($id);
            if (!$item) return apiResponse(false, 'Not found', null, null, 404);

            $item->update($request->only([
                'delivery_status',
                'arrived_at',
                'delivery_notes',
                'proof_photo'
            ]));

            return apiResponse(true, 'Updated', $item);
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to update', null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = DeliveryOrderInvoice::find($id);
            if (!$item) return apiResponse(false, 'Not found', null, null, 404);

            $item->delete();
            return apiResponse(true, 'Removed');
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to delete', null, $e->getMessage(), 500);
        }
    }
}