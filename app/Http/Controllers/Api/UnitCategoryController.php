<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\UnitCategorie;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Throwable;

class UnitCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = UnitCategorie::where('office_id', session('active_office_id'));

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('nama_kategori', 'like', "%{$search}%");
        }

        $data = $query->latest()->paginate(10);

        return apiResponse(true, 'Data kategori unit', $data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'konversi_nilai' => 'numeric'
        ]);

        if (!session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        $data['office_id'] = session('active_office_id');

        $unitCategory = UnitCategorie::create($data);

        $this->logActivity('Create', 'unit_categories', $unitCategory->id, null, $unitCategory);

        return apiResponse(true, 'Berhasil disimpan', $unitCategory, null, 201);
    }

    public function show($id)
    {
        $data = UnitCategorie::find($id);

        if (!$data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail kategori unit', $data);
    }

    public function update(Request $request, $id)
    {
        $data = UnitCategorie::where('office_id', session('active_office_id'))->find($id);

        if (!$data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();

        $data->update($request->all());

        $this->logActivity('Update', 'unit_categories', $data->id, $before, $data);

        return apiResponse(true, 'Berhasil diperbarui', $data);
    }

    public function destroy($id)
    {
        $data = UnitCategorie::where('id', $id)
            ->where('office_id', session('active_office_id'))
            ->first();

        if (!$data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        $this->logActivity('Soft Delete', 'unit_categories', $id, $before, null);

        return apiResponse(true, 'Berhasil dihapus');
    }

    public function search($value)
    {
        $data = UnitCategorie::where('office_id', session('active_office_id'))
            ->where('nama_kategori', 'LIKE', "%$value%")
            ->paginate(10);

        return apiResponse(true, 'Hasil pencarian', $data);
    }

    private function logActivity($tindakan, $tabel, $dataId, $sebelum, $sesudah)
    {
        ActivityLog::create([
            'user_id' => 1,
            'tindakan' => $tindakan,
            'tabel_terkait' => $tabel,
            'data_id' => $dataId,
            'data_sebelum' => $sebelum,
            'data_sesudah' => $sesudah,
            'ip_address' => request()->ip(),
            'office_id' => session('active_office_id')
        ]);
    }
}
