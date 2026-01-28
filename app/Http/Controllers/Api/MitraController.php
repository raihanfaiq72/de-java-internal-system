<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Mitra;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MitraController extends Controller
{
    public function index()
    {
        try {
            $data = Mitra::with(['akunHutang', 'akunPiutang'])
                ->where('office_id', session('active_office_id'))
                ->latest()
                ->paginate(10);

            return apiResponse(true, 'Data mitra berhasil diambil', $data);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal mengambil data mitra', null, $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $mitra = Mitra::with(['akunHutang', 'akunPiutang'])
                ->where('office_id', session('active_office_id'))
                ->find($id);

            if (!$mitra) {
                return apiResponse(false, 'Mitra tidak ditemukan', null, null, 404);
            }

            return apiResponse(true, 'Detail mitra', $mitra);
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

            $mitra = Mitra::create($data);

            $this->logActivity(
                'Create',
                'mitras',
                $mitra->id,
                null,
                $mitra->toArray()
            );

            return apiResponse(true, 'Mitra berhasil dibuat', $mitra, null, 201);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal membuat mitra', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $mitra = Mitra::find($id);

            if (!$mitra) {
                return apiResponse(false, 'Mitra tidak ditemukan', null, null, 404);
            }

            $dataSebelum = $mitra->toArray();

            $validator = Validator::make($request->all(), [
                'nama' => 'sometimes|required|string|max:150',
                'tipe_mitra' => 'sometimes|required|in:Supplier,Client,Both',
                'email' => 'nullable|email',
                'nomor_mitra' => 'nullable|unique:mitras,nomor_mitra,' . $id,
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

            $mitra->update($dataToUpdate);

            $this->logActivity(
                'Update',
                'mitras',
                $mitra->id,
                $dataSebelum,
                $mitra->fresh()->toArray()
            );

            return apiResponse(true, 'Mitra berhasil diperbarui', $mitra);
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal memperbarui mitra', null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $mitra = Mitra::where('office_id', session('active_office_id'))->find($id);

            if (!$mitra) {
                return apiResponse(false, 'Mitra tidak ditemukan', null, null, 404);
            }

            $dataSebelum = $mitra->toArray();

            $mitra->delete();

            $this->logActivity(
                'Soft Delete',
                'mitras',
                $mitra->id,
                $dataSebelum,
                null
            );

            return apiResponse(true, 'Mitra berhasil dihapus');
        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal menghapus mitra', null, $e->getMessage(), 500);
        }
    }

    public function search($value)
    {
        try {
            $data = Mitra::with(['akunHutang', 'akunPiutang'])
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

    private function generateNomorMitra()
    {
        $prefix = 'M-' . date('ym') . '-';
        
        $lastMitra = Mitra::where('nomor_mitra', 'LIKE', $prefix . '%')
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