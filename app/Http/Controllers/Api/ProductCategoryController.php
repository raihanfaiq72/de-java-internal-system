<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ProductCategorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $data = ProductCategorie::with('parent')->latest()->paginate(10);
        return apiResponse(true, 'Data kategori produk', $data);
    }

    public function show($id)
    {
        $data = ProductCategorie::with('parent')->find($id);
        if (!$data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }
        return apiResponse(true, 'Detail kategori produk', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|max:100',
            'parent_id' => 'nullable|exists:product_categories,id'
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        $data = ProductCategorie::create($request->all());

        $this->logActivity('Create', 'product_categories', $data->id, null, $data);

        return apiResponse(true, 'Kategori produk berhasil ditambahkan', $data, null, 201);
    }

    public function update(Request $request, $id)
    {
        $data = ProductCategorie::find($id);
        if (!$data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->update($request->all());

        $this->logActivity('Update', 'product_categories', $id, $before, $data);

        return apiResponse(true, 'Kategori produk berhasil diperbarui', $data);
    }

    public function destroy($id)
    {
        $data = ProductCategorie::find($id);
        if (!$data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        $this->logActivity('Soft Delete', 'product_categories', $id, $before, null);

        return apiResponse(true, 'Kategori produk berhasil dihapus');
    }

    public function search($value)
    {
        $data = ProductCategorie::where('nama_kategori', 'LIKE', "%$value%")
            ->paginate(10);

        return apiResponse(true, 'Hasil pencarian kategori produk', $data);
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
            'ip_address' => request()->ip()
        ]);
    }
}
