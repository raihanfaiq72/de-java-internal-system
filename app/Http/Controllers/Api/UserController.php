<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        if ($perPage >= 1000 || $request->has('all')) {
            $data = User::latest()->get();
        } else {
            $data = User::latest()->paginate($perPage)->withQueryString();
        }

        return apiResponse(true, 'Data user', $data);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (! $user) {
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
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            return apiResponse(true, 'User berhasil dibuat', $user, null, 201);

        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal membuat user', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (! $user) {
            return apiResponse(false, 'User tidak ditemukan', null, null, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:150',
            'username' => 'sometimes|required|string|max:150',
            'email' => 'sometimes|required|email|unique:users,email,'.$id,
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

            return apiResponse(true, 'User berhasil diperbarui', $user);

        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal memperbarui user', null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (! $user) {
            return apiResponse(false, 'User tidak ditemukan', null, null, 404);
        }

        try {
            $before = $user->toArray();
            $user->delete();

            return apiResponse(true, 'User berhasil dihapus');

        } catch (Throwable $e) {
            return apiResponse(false, 'Gagal menghapus user', null, $e->getMessage(), 500);
        }
    }

    public function search(Request $request, $value)
    {
        try {
            $query = User::where(function ($q) use ($value) {
                $q->where('name', 'LIKE', "%{$value}%")
                    ->orWhere('username', 'LIKE', "%{$value}%")
                    ->orWhere('email', 'LIKE', "%{$value}%");
            })
                ->latest();

            $perPage = $request->get('per_page', 10);
            if ($perPage >= 1000) {
                $data = $query->get();
            } else {
                $data = $query->paginate($perPage)->withQueryString();
            }

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

    public function getStaffByPermission(Request $request)
    {
        $permission = $request->permission;
        $officeId = session('active_office_id');

        if (! $officeId) {
            return apiResponse(false, 'Office ID not found in session', [], null, 400);
        }

        $users = User::join('user_office_roles', 'users.id', '=', 'user_office_roles.user_id')
            ->join('roles', 'user_office_roles.role_id', '=', 'roles.id')
            ->leftJoin('role_permissions', 'roles.id', '=', 'role_permissions.role_id')
            ->leftJoin('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('user_office_roles.office_id', $officeId)
            ->where(function ($q) use ($permission) {
                // Include if user is superadmin or has the target permission
                $q->whereRaw('LOWER(roles.name) = ?', ['superadmin'])
                    ->orWhere('permissions.name', $permission)
                    ->orWhere('permissions.name', 'like', $permission.'.%');
            })
            ->select('users.id', 'users.name')
            ->distinct()
            ->orderBy('users.name')
            ->get();

        return apiResponse(true, 'Data staff', $users);
    }
}
