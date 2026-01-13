<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockLocationController extends Controller
{
    public function index()
    {
        $locations = StockLocation::latest()->get();
        return apiResponse(true, 'Data lokasi stok', $locations);
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

        $location = StockLocation::create($request->all());
        return apiResponse(true, 'Lokasi stok berhasil dibuat', $location);
    }

    public function show($id)
    {
        $location = StockLocation::find($id);
        if (!$location) {
            return apiResponse(false, 'Lokasi tidak ditemukan', null, null, 404);
        }
        return apiResponse(true, 'Detail lokasi stok', $location);
    }

    public function update(Request $request, $id)
    {
        $location = StockLocation::find($id);
        if (!$location) {
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
        $location = StockLocation::find($id);
        if (!$location) {
            return apiResponse(false, 'Lokasi tidak ditemukan', null, null, 404);
        }

        $location->delete();
        return apiResponse(true, 'Lokasi stok berhasil dihapus');
    }
}
