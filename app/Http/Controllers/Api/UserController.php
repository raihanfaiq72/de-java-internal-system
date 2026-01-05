<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UserController extends Controller
{
    public function index()
    {
        $data = User::latest()->paginate(10);
        return apiResponse(true, 'Data user', $data);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return apiResponse(false, 'User tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail user', $user);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'username' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        try {
            $user = User::create([
                'name'     => $request->name,
                'username' => $request->username,
                'email'    => $request->email,
                'password' => $request->password, 
            ]);

            $this->logActivity(
                'Create',
                'users',
                $user->id,
                null,
                $user
            );

            return apiResponse(true, 'User berhasil dibuat', $user, null, 201);

        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal membuat user', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return apiResponse(false, 'User tidak ditemukan', null, null, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:150',
            'username' => 'sometimes|required|string|max:150',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        try {
            $before = $user->toArray();

            $data = $request->only(['name', 'email', 'username']);

            if ($request->filled('password')) {
                $data['password'] = $request->password; 
            }

            $user->update($data);

            $this->logActivity(
                'Update',
                'users',
                $user->id,
                $before,
                $user
            );

            return apiResponse(true, 'User berhasil diperbarui', $user);

        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal memperbarui user', null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return apiResponse(false, 'User tidak ditemukan', null, null, 404);
        }

        try {
            $before = $user->toArray();
            $user->delete();

            $this->logActivity(
                'Delete',
                'users',
                $id,
                $before,
                null
            );

            return apiResponse(true, 'User berhasil dihapus');

        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal menghapus user', null, $e->getMessage(), 500);
        }
    }

    public function search($value)
    {
        try {
            $data = User::where(function ($q) use ($value) {
                    $q->where('name', 'LIKE', "%{$value}%")
                    ->orWhere('username', 'LIKE', "%{$value}%")
                    ->orWhere('email', 'LIKE', "%{$value}%");
                })
                ->latest()
                ->paginate(10);

            return apiResponse(
                true,
                'Hasil pencarian users',
                $data
            );

        } catch (Throwable $e) {
            return apiResponse(
                false,
                'Gagal melakukan pencarian users',
                null,
                $e->getMessage(),
                500
            );
        }
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
            'ip_address' => request()->ip(),
        ]);
    }
}
