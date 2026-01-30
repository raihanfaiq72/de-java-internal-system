<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrderFleet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class DeliveryOrderFleetController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->search;

            $data = DeliveryOrderFleet::with('fleet', 'deliveryOrder')
                ->when($search, function ($q) use ($search) {
                    $q->whereHas('fleet', fn($q2) =>
                    $q2->where('plate_number', 'like', "%{$search}%"));
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
                'fleet_id' => 'required|exists:fleets,id',
            ]);

            if ($validator->fails())
                return apiResponse(false, 'Validation failed', null, $validator->errors(), 422);

            $data = DeliveryOrderFleet::create($request->all());
            return apiResponse(true, 'Fleet assigned', $data);
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to assign fleet', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $fleet = DeliveryOrderFleet::find($id);
            if (!$fleet) return apiResponse(false, 'Not found', null, null, 404);

            $fleet->update($request->all());
            return apiResponse(true, 'Updated', $fleet);
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to update', null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $fleet = DeliveryOrderFleet::find($id);
            if (!$fleet) return apiResponse(false, 'Not found', null, null, 404);

            $fleet->delete();
            return apiResponse(true, 'Removed');
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to delete', null, $e->getMessage(), 500);
        }
    }
}