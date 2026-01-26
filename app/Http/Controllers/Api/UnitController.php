<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::with('category')
            ->where('office_id', session('active_office_id'));

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_unit', 'like', "%{$search}%")
                ->orWhere('simbol', 'like', "%{$search}%");
            });
        }

        $data = $query->latest()->paginate(10);

        return apiResponse(true, 'Data unit', $data);
    }

    public function show($id)
    {
        $data = Unit::with('category')
            ->where('office_id', session('active_office_id'))
            ->find($id);
        if (!$data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }
        return apiResponse(true, 'Detail unit', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unit_category_id' => 'required|exists:unit_categories,id',
            'nama_unit' => 'required|max:50',
            'simbol' => 'required|max:10'
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (!session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        $input = $request->all();
        $input['office_id'] = session('active_office_id');

        $data = Unit::create($input);

        $this->logActivity('Create', 'units', $data->id, null, $data);

        return apiResponse(true, 'Unit berhasil ditambahkan', $data, null, 201);
    }

    public function update(Request $request, $id)
    {
        $data = Unit::where('office_id', session('active_office_id'))->find($id);
        if (!$data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->update($request->all());

        $this->logActivity('Update', 'units', $id, $before, $data);

        return apiResponse(true, 'Unit berhasil diperbarui', $data);
    }

    public function destroy($id)
    {
        $data = Unit::where('office_id', session('active_office_id'))->find($id);
        if (!$data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        $this->logActivity('Soft Delete', 'units', $id, $before, null);

        return apiResponse(true, 'Unit berhasil dihapus');
    }

    public function search($value)
    {
        $data = Unit::where('office_id', session('active_office_id'))
            ->where(function($q) use ($value) {
                $q->where('nama_unit', 'LIKE', "%$value%")
                  ->orWhere('simbol', 'LIKE', "%$value%");
            })
            ->paginate(10);

        return apiResponse(true, 'Hasil pencarian unit', $data);
    }

    private function logActivity($tindakan, $tabel, $dataId, $before, $after)
    {
        ActivityLog::create([
            'office_id' => session('active_office_id'),
            'user_id' => 1,
            'tindakan' => $tindakan,
            'tabel_terkait' => $tabel,
            'data_id' => $dataId,
            'data_sebelum' => $before,
            'data_sesudah' => $after,
            'ip_address' => request()->ip()
        ]);
    }
}
