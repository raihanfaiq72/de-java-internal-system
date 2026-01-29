<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ActivityLog;
use Throwable;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->search;

            $data = Brand::where('office_id', session('active_office_id'))
                ->when($search, function ($q) use ($search) {
                    $q->where('nama_brand', 'like', "%{$search}%");
                })
                ->orderBy('nama_brand')
                ->paginate(10);

            return apiResponse(true, 'Data brand berhasil diambil', $data);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal mengambil data brand', null, $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $brand = Brand::where('office_id', session('active_office_id'))->find($id);

            if (!$brand) {
                return apiResponse(false, 'Brand tidak ditemukan', null, null, 404);
            }

            return apiResponse(true, 'Detail brand', $brand);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal mengambil detail brand', null, $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            if (!session()->has('active_office_id')) {
                return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
            }

            $validator = Validator::make($request->all(), [
                'nama_brand' => 'required|string|max:100|unique:brands,nama_brand,NULL,id,office_id,' . session('active_office_id'),
            ]);

            if ($validator->fails()) {
                return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
            }

            $brand = Brand::create([
                'office_id'  => session('active_office_id'),
                'nama_brand' => $request->nama_brand,
            ]);

            $this->logActivity(
                'Create',
                'brands',
                $brand->id,
                null,
                $brand->toArray()
            );

            return apiResponse(true, 'Brand berhasil ditambahkan', $brand, null, 201);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal menambahkan brand', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $brand = Brand::where('office_id', session('active_office_id'))->find($id);

            if (!$brand) {
                return apiResponse(false, 'Brand tidak ditemukan', null, null, 404);
            }

            $dataSebelum = $brand->toArray();

            $validator = Validator::make($request->all(), [
                'nama_brand' => 'required|string|max:100|unique:brands,nama_brand,' . $id . ',id,office_id,' . session('active_office_id'),
            ]);

            if ($validator->fails()) {
                return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
            }

            $brand->update([
                'nama_brand' => $request->nama_brand,
            ]);

            $this->logActivity(
                'Update',
                'brands',
                $brand->id,
                $dataSebelum,
                $brand->fresh()->toArray()
            );

            return apiResponse(true, 'Brand berhasil diperbarui', $brand);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal memperbarui brand', null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $brand = Brand::where('office_id', session('active_office_id'))->find($id);

            if (!$brand) {
                return apiResponse(false, 'Brand tidak ditemukan', null, null, 404);
            }

            $dataSebelum = $brand->toArray();

            $brand->delete();

            $this->logActivity(
                'Soft Delete',
                'brands',
                $brand->id,
                $dataSebelum,
                null
            );

            return apiResponse(true, 'Brand berhasil dihapus');
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal menghapus brand', null, $e->getMessage(), 500);
        }
    }

    private function logActivity(
        string $tindakan,
        string $tabel,
        int $dataId,
        $dataSebelum = null,
        $dataSesudah = null
    ) {
        ActivityLog::create([
            'office_id'     => session('active_office_id'),
            'user_id'       => '1',
            'tindakan'      => $tindakan,
            'tabel_terkait' => $tabel,
            'data_id'       => $dataId,
            'data_sebelum'  => $dataSebelum,
            'data_sesudah'  => $dataSesudah,
            'ip_address'    => request()->ip(),
        ]);
    }
}