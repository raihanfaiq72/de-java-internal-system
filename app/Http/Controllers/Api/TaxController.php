<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaxController extends Controller
{
    public function index(Request $request)
    {
        $query = Tax::where('office_id', session('active_office_id'))->latest();

        $perPage = $request->input('per_page', 10);
        if ($perPage >= 1000) {
            $data = $query->get();
        } else {
            $data = $query->paginate($perPage)->withQueryString();
        }

        return apiResponse(true, 'Data pajak', $data);
    }

    public function show($id)
    {
        $data = Tax::where('office_id', session('active_office_id'))->find($id);
        if (! $data) {
            return apiResponse(false, 'Pajak tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail pajak', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pajak' => 'required|max:100',
            'persentase' => 'required|numeric',
            'tipe_pajak' => 'required|in:Exclusive,Inclusive,Gross Up',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (! session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        $input = $request->all();
        $input['office_id'] = session('active_office_id');

        $data = Tax::create($input);

        return apiResponse(true, 'Pajak berhasil ditambahkan', $data, null, 201);
    }

    public function update(Request $request, $id)
    {
        $data = Tax::where('office_id', session('active_office_id'))->find($id);
        if (! $data) {
            return apiResponse(false, 'Pajak tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->update($request->all());

        return apiResponse(true, 'Pajak berhasil diperbarui', $data);
    }

    public function destroy($id)
    {
        $data = Tax::where('office_id', session('active_office_id'))->find($id);
        if (! $data) {
            return apiResponse(false, 'Pajak tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        return apiResponse(true, 'Pajak berhasil dihapus');
    }

    public function search(Request $request, $value)
    {
        $data = Tax::where('office_id', session('active_office_id'))
            ->where('nama_pajak', 'LIKE', "%$value%")
            ->latest()
            ->paginate($request->input('per_page', 10))
            ->withQueryString();

        return apiResponse(true, 'Hasil pencarian pajak', $data);
    }
}
