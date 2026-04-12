<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupplierBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SupplierBrandController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->search;

            $data = SupplierBrand::with(['supplier', 'brand'])
                ->where('office_id', session('active_office_id'))
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($sub) use ($search) {
                        $sub->whereHas('brand', function ($qq) use ($search) {
                            $qq->where('nama_brand', 'like', "%{$search}%");
                        })->orWhereHas('supplier', function ($qq) use ($search) {
                            $qq->where('nama', 'like', "%{$search}%");
                        });
                    });
                })
                ->latest();

            $perPage = $request->get('per_page', 10);
            if ($perPage >= 1000) {
                $data = $data->get();
            } else {
                $data = $data->paginate($perPage)->withQueryString();
            }

            return apiResponse(true, 'Data relasi supplier-brand berhasil diambil', $data);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal mengambil data relasi supplier-brand', null, $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $data = SupplierBrand::with(['supplier', 'brand'])
                ->where('office_id', session('active_office_id'))
                ->find($id);

            if (! $data) {
                return apiResponse(false, 'Data relasi tidak ditemukan', null, null, 404);
            }

            return apiResponse(true, 'Detail relasi supplier-brand', $data);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal mengambil detail relasi', null, $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            if (! session()->has('active_office_id')) {
                return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
            }

            $validator = Validator::make($request->all(), [
                'supplier_id' => 'required|exists:mitras,id',
                'brand_id' => 'required|exists:brands,id',
            ]);

            if ($validator->fails()) {
                return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
            }

            $officeId = session('active_office_id');

            $exists = SupplierBrand::where('office_id', $officeId)
                ->where('supplier_id', $request->supplier_id)
                ->where('brand_id', $request->brand_id)
                ->exists();

            if ($exists) {
                return apiResponse(false, 'Relasi supplier dan brand di office ini sudah ada', null, null, 422);
            }

            $data = SupplierBrand::create([
                'office_id' => $officeId,
                'supplier_id' => $request->supplier_id,
                'brand_id' => $request->brand_id,
            ]);

            return apiResponse(true, 'Supplier berhasil dikaitkan dengan brand', $data, null, 201);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal menyimpan relasi supplier-brand', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = SupplierBrand::where('office_id', session('active_office_id'))->find($id);

            if (! $data) {
                return apiResponse(false, 'Data relasi tidak ditemukan', null, null, 404);
            }

            $dataSebelum = $data->toArray();

            $validator = Validator::make($request->all(), [
                'supplier_id' => 'required|exists:mitras,id',
                'brand_id' => 'required|exists:brands,id',
            ]);

            if ($validator->fails()) {
                return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
            }

            $officeId = session('active_office_id');

            $exists = SupplierBrand::where('office_id', $officeId)
                ->where('supplier_id', $request->supplier_id)
                ->where('brand_id', $request->brand_id)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return apiResponse(false, 'Relasi supplier dan brand di office ini sudah ada', null, null, 422);
            }

            $data->update([
                'supplier_id' => $request->supplier_id,
                'brand_id' => $request->brand_id,
            ]);

            return apiResponse(true, 'Relasi supplier-brand berhasil diperbarui', $data);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal memperbarui relasi', null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $data = SupplierBrand::where('office_id', session('active_office_id'))->find($id);

            if (! $data) {
                return apiResponse(false, 'Data relasi tidak ditemukan', null, null, 404);
            }

            $dataSebelum = $data->toArray();

            $data->delete();

            return apiResponse(true, 'Relasi supplier-brand berhasil dihapus');
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal menghapus relasi', null, $e->getMessage(), 500);
        }
    }
}
