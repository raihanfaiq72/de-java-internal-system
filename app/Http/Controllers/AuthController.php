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
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

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
    
        $availableOffices = DB::table('user_office_roles')->join('offices', 'user_office_roles.office_id', '=', 'offices.id')->where('user_id', $user->id)->select('offices.*')->get();

        return view('Auth.syo', compact('availableOffices'));
    }

    public function setOutlet(Request $request)
    {
        $request->validate(['office_id' => 'required']);
        
        session(['active_office_id' => $request->office_id]);
        
        return response()->json([
            'success' => true,
            'redirect_url' => route('dashboard')
        ]);
    }
}