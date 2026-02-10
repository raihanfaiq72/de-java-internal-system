<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Partner::with(['akunHutang', 'akunPiutang'])
                ->where('office_id', session('active_office_id'));

            if ($request->has('trashed') && $request->trashed == '1') {
                $query->onlyTrashed();
            }

            if ($request->filled('search')) {
                $value = $request->search;
                $query->where(function ($q) use ($value) {
                    $q->where('nama', 'LIKE', "%{$value}%")
                        ->orWhere('nomor_mitra', 'LIKE', "%{$value}%")
                        ->orWhere('email', 'LIKE', "%{$value}%")
                        ->orWhere('no_hp', 'LIKE', "%{$value}%");
                });
            }

            if ($request->filled('tipe_mitra')) {
                $query->where('tipe_mitra', $request->tipe_mitra);
            }

            $data = $query->latest()->paginate(10);

            return apiResponse(true, 'Data mitra berhasil diambil', $data);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal mengambil data mitra', null, $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $partner = Partner::with(['akunHutang', 'akunPiutang'])
                ->where('office_id', session('active_office_id'))
                ->find($id);

            if (!$partner) {
                return apiResponse(false, 'Mitra tidak ditemukan', null, null, 404);
            }

            return apiResponse(true, 'Detail mitra', $partner);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal mengambil detail mitra', null, $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:150',
                'tipe_mitra' => 'required|in:Supplier,Client,Both',
                'email' => 'nullable|email',
                'nomor_mitra' => 'nullable|unique:mitras,nomor_mitra',
                'payment_terms' => 'nullable|string|max:50',
                'identity_card' => 'nullable|string|max:50',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            if ($validator->fails()) {
                return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
            }

            if (!session()->has('active_office_id')) {
                return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
            }

            $data = $request->all();
            if (empty($data['nomor_mitra'])) {
                $data['nomor_mitra'] = $this->generateNomorMitra();
            }
            $data['office_id'] = session('active_office_id');
            $data['akun_hutang_id']  = 18;
            $data['akun_piutang_id'] = 5;

            $partner = Partner::create($data);

            return apiResponse(true, 'Mitra berhasil dibuat', $partner, null, 201);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal membuat Mitra', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $partner = Partner::find($id);

            if (!$partner) {
                return apiResponse(false, 'Mitra tidak ditemukan', null, null, 404);
            }

            $dataSebelum = $partner->toArray();

            $validator = Validator::make($request->all(), [
                'nama' => 'sometimes|required|string|max:150',
                'tipe_mitra' => 'sometimes|required|in:Supplier,Client,Both',
                'email' => 'nullable|email',
                'nomor_mitra' => 'nullable|unique:mitras,nomor_mitra,' . $id,
                'payment_terms' => 'nullable|string|max:50',
                'identity_card' => 'nullable|string|max:50',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            if ($validator->fails()) {
                return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
            }

            $dataToUpdate = $request->all();
            if (empty($dataToUpdate['nomor_mitra'])) {
                if (empty($mitra->nomor_mitra)) {
                    $dataToUpdate['nomor_mitra'] = $this->generateNomorMitra();
                } else {
                    unset($dataToUpdate['nomor_mitra']);
                }
            }

            $partner->update($dataToUpdate);

            return apiResponse(true, 'Mitra berhasil diperbarui', $partner);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal memperbarui mitra', null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $mitra = Partner::where('office_id', session('active_office_id'))->find($id);

            if (!$mitra) {
                return apiResponse(false, 'Mitra tidak ditemukan', null, null, 404);
            }

            $dataSebelum = $mitra->toArray();

            $mitra->delete();

            return apiResponse(true, 'Mitra berhasil dihapus');
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal menghapus mitra', null, $e->getMessage(), 500);
        }
    }

    public function search($value)
    {
        try {
            $data = Partner::with(['akunHutang', 'akunPiutang'])
                ->where('office_id', session('active_office_id'))
                ->where(function ($q) use ($value) {
                    $q->where('nama', 'LIKE', "%{$value}%")
                        ->orWhere('nomor_mitra', 'LIKE', "%{$value}%")
                        ->orWhere('email', 'LIKE', "%{$value}%")
                        ->orWhere('no_hp', 'LIKE', "%{$value}%")
                        ->orWhere('tipe_mitra', 'LIKE', "%{$value}%")
                        ->orWhere('badan_usaha', 'LIKE', "%{$value}%")
                        ->orWhere('alamat', 'LIKE', "%{$value}%")
                        ->orWhere('kontak_nama', 'LIKE', "%{$value}%")
                        ->orWhere('kontak_jabatan', 'LIKE', "%{$value}%")
                        ->orWhere('kontak_no_hp', 'LIKE', "%{$value}%")
                        ->orWhere('kontak_email', 'LIKE', "%{$value}%");
                })
                ->latest()
                ->paginate(10);

            return apiResponse(
                true,
                'Hasil pencarian mitra',
                $data
            );
        } catch (Throwable $e) {
            return apiResponse(
                false,
                'Gagal melakukan pencarian mitra',
                null,
                $e->getMessage(),
                500
            );
        }
    }

    public function restore($id)
    {
        try {
            $mitra = Partner::onlyTrashed()
                ->where('office_id', session('active_office_id'))
                ->find($id);

            if (!$mitra) {
                return apiResponse(false, 'Mitra tidak ditemukan atau belum dihapus', null, null, 404);
            }

            $mitra->restore();

            return apiResponse(true, 'Mitra berhasil dipulihkan');
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal memulihkan mitra', null, $e->getMessage(), 500);
        }
    }

    public function forceDestroy($id)
    {
        try {
            $mitra = Partner::onlyTrashed()
                ->where('office_id', session('active_office_id'))
                ->find($id);

            if (!$mitra) {
                return apiResponse(false, 'Mitra tidak ditemukan', null, null, 404);
            }

            $mitra->forceDelete();

            return apiResponse(true, 'Mitra berhasil dihapus permanen');
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal menghapus mitra permanen', null, $e->getMessage(), 500);
        }
    }

    private function generateNomorMitra()
    {
        $prefix = 'M-' . date('ym') . '-';

        $lastMitra = Partner::where('nomor_mitra', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastMitra) {
            $parts = explode('-', $lastMitra->nomor_mitra);
            if (count($parts) >= 3 && is_numeric(end($parts))) {
                $nextNumber = intval(end($parts)) + 1;
            }
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

}