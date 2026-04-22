<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckModuleAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $routeName = $request->route()->getName();
        $officeId = session('active_office_id');

        $alwaysAllowed = ['dashboard', 'syo', 'set.outlet', 'login', 'logout', 'offices.store'];

        if (in_array($routeName, $alwaysAllowed)) {
            return $next($request);
        }

        if (! $officeId) {
            return redirect()->route('syo')->with('error', 'Silahkan pilih kantor terlebih dahulu.');
        }

        $hasAccess = \App\Models\UserOfficeRole::where('user_id', $user->id)
            ->where('office_id', $officeId)
            ->whereHas('role', function ($query) use ($routeName) {
                $query->whereHas('permissions', function ($query) use ($routeName) {
                    $query->where('name', $routeName);
                });
            })
            ->exists();

        if ($hasAccess) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki wewenang untuk mengakses modul ini di kantor yang dipilih.');
    }
}
