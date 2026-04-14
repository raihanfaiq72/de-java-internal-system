<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockLocationController extends Controller
{
    public function index(Request $request)
    {
        $query = StockLocation::where('office_id', session('active_office_id'))
            ->latest();

        $perPage = $request->input('per_page', 10);
        if ($perPage >= 1000) {
            $data = $query->get();
        } else {
            $data = $query->paginate($perPage)->withQueryString();
        }

        $data->map(function ($location) {
            $location->type = ucwords(strtolower($location->type));

            return $location;
        });

        return apiResponse(true, 'Data lokasi stok', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:stock,virtual',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (! session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        $input = $request->all();
        $input['office_id'] = session('active_office_id');

        $location = StockLocation::create($input);

        return apiResponse(true, 'Lokasi stok berhasil dibuat', $location);
    }

    public function show($id)
    {
        $location = StockLocation::where('office_id', session('active_office_id'))->find($id);
        if (! $location) {
            return apiResponse(false, 'Lokasi tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail lokasi stok', $location);
    }

    public function update(Request $request, $id)
    {
        $location = StockLocation::find($id);
        if (! $location) {
            return apiResponse(false, 'Lokasi tidak ditemukan', null, null, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:stock,virtual',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        $location->update($request->all());

        return apiResponse(true, 'Lokasi stok berhasil diperbarui', $location);
    }

    public function destroy($id)
    {
        $location = StockLocation::where('id', $id)
            ->where('office_id', session('active_office_id'))
            ->first();

        if (! $location) {
            return apiResponse(false, 'Lokasi tidak ditemukan', null, null, 404);
        }

        $location->delete();

        return apiResponse(true, 'Lokasi stok berhasil dihapus');
    }
}
