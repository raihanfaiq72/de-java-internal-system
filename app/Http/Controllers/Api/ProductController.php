<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $data = Product::with(['category', 'unit'])->latest()->paginate(10);
        return apiResponse(true, 'Data produk', $data);
    }

    public function show($id)
    {
        $data = Product::with(['category', 'unit'])->find($id);
        if (!$data) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }
        return apiResponse(true, 'Detail produk', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sku_kode' => 'required|unique:products,sku_kode',
            'nama_produk' => 'required',
            'unit_id' => 'required|exists:units,id'
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        $data = Product::create($request->all());

        $this->logActivity('Create', 'products', $data->id, null, $data);

        return apiResponse(true, 'Produk berhasil ditambahkan', $data, null, 201);
    }

    public function update(Request $request, $id)
    {
        $data = Product::find($id);
        if (!$data) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->update($request->all());

        $this->logActivity('Update', 'products', $id, $before, $data);

        return apiResponse(true, 'Produk berhasil diperbarui', $data);
    }

    public function destroy($id)
    {
        $data = Product::find($id);
        if (!$data) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        $this->logActivity('Soft Delete', 'products', $id, $before, null);

        return apiResponse(true, 'Produk berhasil dihapus');
    }

    public function search($value)
    {
        $data = Product::where('nama_produk', 'LIKE', "%$value%")
            ->orWhere('sku_kode', 'LIKE', "%$value%")
            ->paginate(10);

        return apiResponse(true, 'Hasil pencarian produk', $data);
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
