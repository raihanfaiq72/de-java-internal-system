<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fleet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class FleetController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->search;

            $data = Fleet::where('office_id', '1')
                ->when($search, function ($q) use ($search) {
                    $q->where('fleet_name', 'like', "%{$search}%")
                        ->orWhere('license_plate', 'like', "%{$search}%")
                        ->orWhere('fuel_type', 'like', "%{$search}%");
                })
                ->latest()
                ->paginate(10);

            return apiResponse(true, 'Fleet list', $data);
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to retrieve fleet data', null, $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fleet_name' => 'required',
                'license_plate' => 'required|unique:fleets',
            ]);

            if ($validator->fails())
                return apiResponse(false, 'Validation failed', null, $validator->errors(), 422);

            $fleet = Fleet::create([
                'office_id' => '1',
                'fleet_name' => $request->fleet_name,
                'fuel_type' => $request->fuel_type,
                'license_plate' => $request->license_plate,
                'km_per_liter' => $request->km_per_liter,
            ]);

            return apiResponse(true, 'Created', $fleet);
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to create', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $fleet = Fleet::find($id);
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
            $fleet = Fleet::find($id);
            if (!$fleet) return apiResponse(false, 'Not found', null, null, 404);

            $fleet->delete();
            return apiResponse(true, 'Deleted');
        } catch (Throwable $e) {
            return apiResponse(false, 'Failed to delete', null, $e->getMessage(), 500);
        }
    }
}