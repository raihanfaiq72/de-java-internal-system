<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DeliveryOrder;
use Throwable;

class DeliveryOrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->search;

            $data = DeliveryOrder::with('invoices', 'fleets')
                ->when($search, function ($q) use ($search) {
                    $q->where('do_number', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                })
                ->latest()
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
                'delivery_order_number' => 'required|unique:delivery_orders',
                'delivery_date' => 'required|date',
            ]);

            if ($validator->fails())
                return apiResponse(false, 'Validation failed', null, $validator->errors(), 422);

            $do = DeliveryOrder::create([
                'office_id' => session('active_office_id'),
                'delivery_order_number' => $request->delivery_order_number,
                'delivery_date' => $request->delivery_date,
                'notes' => $request->notes,
            ]);

            return apiResponse(true, 'Created', $do, null, 201);
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to create', null, $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $do = DeliveryOrder::with(['invoices.invoice.mitra', 'fleets.fleet'])
                ->where('office_id', session('active_office_id'))
                ->find($id);

            if (!$do) return apiResponse(false, 'Not found', null, null, 404);

            return apiResponse(true, 'Detail', $do);
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to retrieve', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $do = DeliveryOrder::where('office_id', session('active_office_id'))->find($id);
            if (!$do) return apiResponse(false, 'Not found', null, null, 404);

            $do->update($request->only([
                'delivery_date',
                'status',
                'scheduled_at',
                'departed_at',
                'returned_at',
                'notes'
            ]));

            return apiResponse(true, 'Updated', $do);
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to update', null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $do = DeliveryOrder::where('office_id', session('active_office_id'))->find($id);
            if (!$do) return apiResponse(false, 'Not found', null, null, 404);

            $do->delete();
            return apiResponse(true, 'Deleted');
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to delete', null, $e->getMessage(), 500);
        }
    }
}