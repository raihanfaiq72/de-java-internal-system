<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * setup login pertama kali
     */
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('syo');
        }

        return view('Auth.login');
    }

    public function loginProses(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Auto-set office jika user hanya punya 1 office
            $user = Auth::user();
            $availableOffices = DB::table('user_office_roles')
                ->where('user_id', $user->id)
                ->pluck('office_id')
                ->toArray();

            if (count($availableOffices) === 1) {
                session(['active_office_id' => $availableOffices[0]]);
                return redirect()->route('dashboard');
            }

            return redirect()->route('syo');
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function syo()
    {
        $user = Auth::user();

        $availableOffices = DB::table('user_office_roles')
            ->join('offices', 'user_office_roles.office_id', '=', 'offices.id')
            ->join('roles', 'user_office_roles.role_id', '=', 'roles.id')
            ->where('user_office_roles.user_id', $user->id)
            ->select('offices.*', 'roles.name as role_name')
            ->get();

        return view('Auth.syo', compact('availableOffices'));
    }

    public function destroyOutlet($id)
    {
        $user = Auth::user();

        // Cek apakah user memiliki role Superadmin atau Owner untuk outlet ini
        $hasAccess = DB::table('user_office_roles')
            ->join('roles', 'user_office_roles.role_id', '=', 'roles.id')
            ->where('user_office_roles.user_id', $user->id)
            ->where('user_office_roles.office_id', $id)
            ->whereIn('roles.name', ['Superadmin', 'Owner'])
            ->exists();

        if (! $hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus outlet ini.',
            ], 403);
        }

        try {
            // Karena cascading delete sudah diatur di migration,
            // menghapus office akan menghapus data terkait.
            \App\Models\Office::findOrFail($id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Outlet dan seluruh data terkait berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus outlet: '.$e->getMessage(),
            ], 500);
        }
    }

    public function setOutlet(Request $request)
    {
        $request->validate(['office_id' => 'required']);

        session(['active_office_id' => $request->office_id]);

        return response()->json([
            'success' => true,
            'redirect_url' => route('dashboard'),
        ]);
    }
}
