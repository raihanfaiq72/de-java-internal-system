<?php

namespace App\Http\Controllers\Api;

use App\Events\DriverLocationUpdated;
use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderFleet;
use App\Models\DeliveryOrderInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class DriverDeliveryController extends Controller
{
    public function show($id)
    {
        try {
            $driverId = Auth::id();

            $fleet = DeliveryOrderFleet::with(['fleet', 'driver'])
                ->where('delivery_order_id', $id)
                ->where('driver_id', $driverId)
                ->firstOrFail();

            $do = DeliveryOrder::with([
                'office',
                'invoices' => function ($q) {
                    $q->orderBy('delivery_sequence', 'asc');
                },
                'invoices.invoice.mitra',
            ])->findOrFail($id);

            return response()->json(['success' => true, 'data' => compact('do', 'fleet')]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function startTrip(Request $request, $id)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $driverId = Auth::id();
            $fleet = DeliveryOrderFleet::where('delivery_order_id', $id)
                ->where('driver_id', $driverId)
                ->firstOrFail();

            if ($fleet->status !== 'assigned') {
                return response()->json(['success' => false, 'message' => 'Trip already started or completed.']);
            }

            $fleet->update([
                'status' => 'in_transit',
                'start_latitude' => $request->latitude,
                'start_longitude' => $request->longitude,
                'last_latitude' => $request->latitude,
                'last_longitude' => $request->longitude,
            ]);

            $do = DeliveryOrder::find($id);
            if ($do && ($do->status === 'Scheduled' || $do->status === 'draft')) {
                $do->update([
                    'status' => 'in_transit',
                    'departed_at' => now(),
                ]);
            }

            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateLocation(Request $request, $id)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $driverId = Auth::id();
            $fleet = DeliveryOrderFleet::where('delivery_order_id', $id)
                ->where('driver_id', $driverId)
                ->firstOrFail();

            $fleet->update([
                'last_latitude' => $request->latitude,
                'last_longitude' => $request->longitude,
            ]);

            broadcast(new DriverLocationUpdated($id, $driverId, $request->latitude, $request->longitude));

            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function arriveAtStop(Request $request, $id, $invoiceId)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'photo' => 'required|image|max:10240',
                'notes' => 'nullable|string',
            ]);

            $driverId = Auth::id();

            DeliveryOrderFleet::where('delivery_order_id', $id)
                ->where('driver_id', $driverId)
                ->firstOrFail();

            $doInvoice = DeliveryOrderInvoice::where('delivery_order_id', $id)
                ->where('invoice_id', $invoiceId)
                ->firstOrFail();

            $path = null;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = 'proof_'.$id.'_'.$invoiceId.'_'.time().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('proofs', $filename, 'public');
            }

            $doInvoice->update([
                'delivery_status' => 'delivered',
                'arrived_at' => now(),
                'proof_photo' => $path,
                'delivery_notes' => $request->notes,
            ]);

            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getProof($id, $invoiceId)
    {
        try {
            $driverId = Auth::id();

            DeliveryOrderFleet::where('delivery_order_id', $id)
                ->where('driver_id', $driverId)
                ->firstOrFail();

            $doInvoice = DeliveryOrderInvoice::where('delivery_order_id', $id)
                ->where('invoice_id', $invoiceId)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'photo_url' => $doInvoice->proof_photo ? asset('storage/' . $doInvoice->proof_photo) : null,
                    'notes' => $doInvoice->delivery_notes,
                    'arrived_at' => $doInvoice->arrived_at,
                ],
            ]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function finishTrip(Request $request, $id)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'odo_end' => 'required|numeric|min:0',
                'gas_leftover' => 'required|numeric|min:0',
            ]);

            $driverId = Auth::id();
            $fleet = DeliveryOrderFleet::with('fleet')
                ->where('delivery_order_id', $id)
                ->where('driver_id', $driverId)
                ->firstOrFail();

            $fleet->update([
                'status' => 'completed',
                'last_latitude' => $request->latitude,
                'last_longitude' => $request->longitude,
                'odo_end' => $request->odo_end,
                'gas_leftover' => $request->gas_leftover,
            ]);

            if ($fleet->fleet) {
                $fleet->fleet->update([
                    'last_odometer' => $request->odo_end,
                    'last_fuel_leftover' => $request->gas_leftover,
                ]);
            }

            $do = DeliveryOrder::find($id);
            if ($do) {
                $do->update([
                    'status' => 'completed',
                    'returned_at' => now(),
                ]);
            }

            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

