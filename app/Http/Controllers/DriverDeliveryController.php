<?php

namespace App\Http\Controllers;

use App\Events\DriverLocationUpdated;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderFleet;
use App\Models\DeliveryOrderInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverDeliveryController extends Controller
{
    public function index()
    {
        $driverId = Auth::id();

        // Get assigned fleets/DOs
        // Filter: Status not completed or completed recently?
        // Let's show active ones first.
        $assigned = DeliveryOrderFleet::with(['deliveryOrder', 'fleet'])
            ->where('driver_id', $driverId)
            ->whereIn('status', ['assigned', 'in_transit'])
            ->latest()
            ->get();

        $completed = DeliveryOrderFleet::with(['deliveryOrder', 'fleet'])
            ->where('driver_id', $driverId)
            ->where('status', 'completed')
            ->latest()
            ->limit(10)
            ->get();

        return view('Driver.index', compact('assigned', 'completed'));
    }

    public function show($id)
    {
        // $id is delivery_order_id or delivery_order_fleet_id?
        // Let's assume delivery_order_id for URL cleanliness, but we need to ensure the driver is assigned.

        $driverId = Auth::id();

        $fleet = DeliveryOrderFleet::where('delivery_order_id', $id)
            ->where('driver_id', $driverId)
            ->firstOrFail();

        $do = DeliveryOrder::with(['invoices' => function ($q) {
            $q->orderBy('delivery_sequence', 'asc');
        }, 'invoices.invoice.mitra'])
            ->findOrFail($id);

        return view('Driver.show', compact('do', 'fleet'));
    }

    public function startTrip(Request $request, $id)
    {
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

        // Also update DO status if it's the first fleet moving?
        // Or just keep DO status as 'in_transit'
        $do = DeliveryOrder::find($id);
        if ($do->status == 'Scheduled' || $do->status == 'draft') {
            $do->update([
                'status' => 'in_transit',
                'departed_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function updateLocation(Request $request, $id)
    {
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

        // Broadcast event
        broadcast(new DriverLocationUpdated($id, $driverId, $request->latitude, $request->longitude));

        return response()->json(['success' => true]);
    }

    public function arriveAtStop(Request $request, $id, $invoiceId)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|image|max:10240', // Max 10MB
            'notes' => 'nullable|string',
        ]);

        $driverId = Auth::id();

        // Verify Fleet
        $fleet = DeliveryOrderFleet::where('delivery_order_id', $id)
            ->where('driver_id', $driverId)
            ->firstOrFail();

        // Verify Invoice
        $doInvoice = DeliveryOrderInvoice::where('delivery_order_id', $id)
            ->where('invoice_id', $invoiceId)
            ->firstOrFail();

        // Validate Location (Optional: Distance Check)
        // For now, we trust the driver but could log the distance deviation.

        // Upload Photo
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
    }

    public function finishTrip(Request $request, $id)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $driverId = Auth::id();
        $fleet = DeliveryOrderFleet::where('delivery_order_id', $id)
            ->where('driver_id', $driverId)
            ->firstOrFail();

        // Optional: Check if back at start location
        // Calculate distance between current and start
        // $distance = ...

        $fleet->update([
            'status' => 'completed',
            'last_latitude' => $request->latitude,
            'last_longitude' => $request->longitude,
        ]);

        // Check if all invoices delivered?
        // Update DO status
        $do = DeliveryOrder::find($id);
        $do->update([
            'status' => 'completed', // Or partially_delivered
            'returned_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
