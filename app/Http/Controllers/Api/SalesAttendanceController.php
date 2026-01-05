<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesAttendance;
use Illuminate\Http\Request;

class SalesAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesAttendance::with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->tanggal) {
            $query->where('tgl_presensi', $request->tanggal);
        }

        $data = $query->latest()->paginate(10);

        return apiResponse(true, 'Data presensi sales', $data);
    }

    public function store(Request $request)
    {
        $data = SalesAttendance::create([
            'user_id' => 1,
            'tgl_presensi' => now()->toDateString(),
            'jam_masuk' => now()->toTimeString(),
            'lokasi_gps' => $request->lokasi_gps,
            'keterangan' => $request->keterangan
        ]);

        return apiResponse(true, 'Presensi berhasil dicatat', $data, null, 201);
    }

    public function update(Request $request, $id)
    {
        $data = SalesAttendance::find($id);
        if (!$data) {
            return apiResponse(false, 'Data presensi tidak ditemukan', null, null, 404);
        }

        $data->update([
            'jam_keluar' => now()->toTimeString()
        ]);

        return apiResponse(true, 'Presensi pulang berhasil dicatat', $data);
    }
}
