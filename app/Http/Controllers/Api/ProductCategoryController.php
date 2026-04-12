<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductCategory::where('office_id', session('active_office_id'));

        if ($request->search) {
            $query->where('nama_kategori', 'LIKE', "%{$request->search}%");
        }

        $perPage = $request->get('per_page', 10);
        if ($perPage >= 1000) {
            $data = $query->orderBy('nama_kategori')->get();
        } else {
            $data = $query->orderBy('nama_kategori')->paginate($perPage)->withQueryString();
        }

        return apiResponse(true, 'Data kategori produk', $data);
    }

    public function show($id)
    {
        $data = ProductCategory::where('office_id', session('active_office_id'))
            ->find($id);
        if (! $data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail kategori produk', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|max:100',
            'parent_id' => 'nullable|exists:product_categories,id',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (! session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        $input = $request->all();
        $input['office_id'] = session('active_office_id');

        $data = ProductCategory::create($input);

        return apiResponse(true, 'Kategori produk berhasil ditambahkan', $data, null, 201);
    }

    public function update(Request $request, $id)
    {
        $data = ProductCategory::where('office_id', session('active_office_id'))->find($id);
        if (! $data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->update($request->all());

        return apiResponse(true, 'Kategori produk berhasil diperbarui', $data);
    }

    public function destroy($id)
    {
        $data = ProductCategory::where('id', $id)
            ->where('office_id', session('active_office_id'))
            ->first();

        if (! $data) {
            return apiResponse(false, 'Data tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        return apiResponse(true, 'Kategori produk berhasil dihapus');
    }

    public function search(Request $request, $value)
    {
        $data = ProductCategory::where('office_id', session('active_office_id'))
            ->where('nama_kategori', 'LIKE', "%$value%")
            ->orderBy('nama_kategori')
            ->paginate($request->get('per_page', 10))
            ->withQueryString();

        return apiResponse(true, 'Hasil pencarian kategori produk', $data);
    }
}
