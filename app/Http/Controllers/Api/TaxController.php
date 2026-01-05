<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use App\Models\ActivityLog;
use App\Models\Taxe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class TaxController extends Controller
{
    public function index()
    {
        $data = Taxe::latest()->paginate(10);
        return apiResponse(true, 'Data pajak', $data);
    }

    public function show($id)
    {
        $data = Taxe::find($id);
        if (!$data) {
            return apiResponse(false, 'Pajak tidak ditemukan', null, null, 404);
        }
        return apiResponse(true, 'Detail pajak', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pajak' => 'required|max:100',
            'persentase' => 'required|numeric',
            'tipe_pajak' => 'required|in:Exclusive,Inclusive,Gross Up'
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        $data = Taxe::create($request->all());

        $this->logActivity('Create', 'taxes', $data->id, null, $data);

        return apiResponse(true, 'Pajak berhasil ditambahkan', $data, null, 201);
    }

    public function update(Request $request, $id)
    {
        $data = Taxe::find($id);
        if (!$data) {
            return apiResponse(false, 'Pajak tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->update($request->all());

        $this->logActivity('Update', 'taxes', $id, $before, $data);

        return apiResponse(true, 'Pajak berhasil diperbarui', $data);
    }

    public function destroy($id)
    {
        $data = Taxe::find($id);
        if (!$data) {
            return apiResponse(false, 'Pajak tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        $this->logActivity('Soft Delete', 'taxes', $id, $before, null);

        return apiResponse(true, 'Pajak berhasil dihapus');
    }

    public function search($value)
    {
        $data = Taxe::where('nama_pajak', 'LIKE', "%$value%")
            ->paginate(10);

        return apiResponse(true, 'Hasil pencarian pajak', $data);
    }

    private function logActivity($tindakan, $tabel, $dataId, $before, $after)
    {
        ActivityLog::create([
            'user_id' => 1,
            'tindakan' => $tindakan,
            'tabel_terkait' => $tabel,
            'data_id' => $dataId,
            'data_sebelum' => $before,
            'data_sesudah' => $after,
            'ip_address' => request()->ip(),
        ]);
    }
}
