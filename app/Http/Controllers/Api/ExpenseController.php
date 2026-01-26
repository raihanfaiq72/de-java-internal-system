<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query()->where('office_id', session('active_office_id'));

        if ($request->tgl_mulai && $request->tgl_selesai) {
            $query->whereBetween('tgl_biaya', [$request->tgl_mulai, $request->tgl_selesai]);
        }

        $data = $query->latest()->paginate(10);
        return apiResponse(true, 'Data biaya operasional', $data);
    }

    public function show($id)
    {
        $data = Expense::where('office_id', session('active_office_id'))->find($id);
        if (!$data) {
            return apiResponse(false, 'Biaya tidak ditemukan', null, null, 404);
        }
        return apiResponse(true, 'Detail biaya', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_biaya' => 'required',
            'tgl_biaya' => 'required|date',
            'jumlah' => 'required|numeric|min:0.01',
            'akun_keuangan_id' => 'required|exists:chart_of_accounts,id',
            'akun_beban_id' => 'required|exists:chart_of_accounts,id'
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (!session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        $input = $request->all();
        $input['office_id'] = session('active_office_id');

        $data = Expense::create($input);

        $this->logActivity('Create', 'expenses', $data->id, null, $data);

        return apiResponse(true, 'Biaya berhasil dicatat', $data, null, 201);
    }

    public function update(Request $request, $id)
    {
        $data = Expense::where('office_id', session('active_office_id'))->find($id);
        if (!$data) {
            return apiResponse(false, 'Biaya tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->update($request->all());

        $this->logActivity('Update', 'expenses', $id, $before, $data);

        return apiResponse(true, 'Biaya berhasil diperbarui', $data);
    }

    public function destroy($id)
    {
        $data = Expense::where('office_id', session('active_office_id'))->find($id);
        if (!$data) {
            return apiResponse(false, 'Biaya tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        $this->logActivity('Soft Delete', 'expenses', $id, $before, null);

        return apiResponse(true, 'Biaya berhasil dihapus');
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
