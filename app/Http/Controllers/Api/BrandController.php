<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Partner;
use App\Models\SupplierBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->search;

            $data = Brand::with(['suppliers:id,nama'])
                ->where('office_id', session('active_office_id'))
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

    public function suppliers($supplier_id)
    {
        try {
            $officeId = session('active_office_id');

            $supplier = Partner::where('office_id', $officeId)
                ->where('tipe_mitra', 'supplier')
                ->find($supplier_id);

            if (! $supplier) {
                return apiResponse(false, 'Supplier tidak ditemukan', null, null, 404);
            }

            $brands = Brand::where('office_id', $officeId)
                ->whereHas('suppliers', function ($q) use ($supplier_id) {
                    $q->where('mitras.id', $supplier_id);
                })
                ->with(['suppliers:id,nama'])
                ->orderBy('nama_brand')
                ->get();

            return apiResponse(true, 'Daftar brand milik supplier', [
                'supplier' => [
                    'id' => $supplier->id,
                    'nama' => $supplier->nama,
                ],
                'brands' => $brands,
            ]);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal mengambil data brand supplier', null, $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $brand = Brand::with(['suppliers:id,nama'])
                ->where('office_id', session('active_office_id'))
                ->find($id);

            if (! $brand) {
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
            if (! session()->has('active_office_id')) {
                return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
            }

            $validator = Validator::make($request->all(), [
                'nama_brand' => 'required|string|max:100',
                'supplier_ids' => 'nullable|array',
                'supplier_ids.*' => 'exists:mitras,id',
            ]);

            if ($validator->fails()) {
                return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
            }

            $brand = DB::transaction(function () use ($request) {
                // Upsert Brand: Cari berdasarkan nama & office, jika tidak ada buat baru
                $brand = Brand::withTrashed()->firstOrCreate(
                    [
                        'nama_brand' => $request->nama_brand,
                        'office_id' => session('active_office_id'),
                    ],
                    [
                        'office_id' => session('active_office_id'),
                    ]
                );

                // Kembalikan jika sebelumnya didelete
                if ($brand->trashed()) {
                    $brand->restore();
                }

                // Append Suppliers (Sync Without Detaching)
                if ($request->filled('supplier_ids')) {
                    $attachData = collect($request->supplier_ids)->mapWithKeys(function ($id) {
                        return [$id => ['office_id' => session('active_office_id')]];
                    });

                    // Gunakan syncWithoutDetaching agar tidak menduplikasi dan tidak menghapus yang sudah ada
                    $brand->suppliers()->syncWithoutDetaching($attachData);
                }

                return $brand;
            });

            return apiResponse(true, 'Brand berhasil ditambahkan', $brand->load('suppliers'), null, 201);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal menambahkan brand', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $brand = Brand::where('office_id', session('active_office_id'))->find($id);

            if (! $brand) {
                return apiResponse(false, 'Brand tidak ditemukan', null, null, 404);
            }

            $validator = Validator::make($request->all(), [
                'nama_brand' => 'required|string|max:100|unique:brands,nama_brand,'.$id.',id,office_id,'.session('active_office_id'),
                'supplier_ids' => 'nullable|array',
                'supplier_ids.*' => 'exists:mitras,id',
            ]);

            if ($validator->fails()) {
                return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
            }

            $brand = DB::transaction(function () use ($request, $brand) {

                $dataSebelum = $brand->load('suppliers')->toArray();

                $brand->update([
                    'nama_brand' => $request->nama_brand,
                ]);

                if ($request->has('supplier_ids')) {
                    $attachData = collect($request->supplier_ids)->mapWithKeys(function ($id) {
                        return [$id => ['office_id' => session('active_office_id')]];
                    });

                    // Append logic: hanya tambahkan yang baru, jangan hapus yang lama
                    $brand->suppliers()->syncWithoutDetaching($attachData);
                }

                return $brand;
            });

            return apiResponse(true, 'Brand berhasil diperbarui', $brand->load('suppliers'));
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal memperbarui brand', null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $brand = Brand::where('office_id', session('active_office_id'))->find($id);

            if (! $brand) {
                return apiResponse(false, 'Brand tidak ditemukan', null, null, 404);
            }

            $dataSebelum = $brand->toArray();

            DB::transaction(function () use ($brand) {
                SupplierBrand::where('brand_id', $brand->id)->delete();
                $brand->delete();
            });

            return apiResponse(true, 'Brand berhasil dihapus');
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal menghapus brand', null, $e->getMessage(), 500);
        }
    }
}
