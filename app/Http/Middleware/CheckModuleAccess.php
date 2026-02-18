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

        $hasAccess = DB::table('user_office_roles')
            ->join('role_permissions', 'user_office_roles.role_id', '=', 'role_permissions.role_id')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('user_office_roles.user_id', $user->id)
            ->where('user_office_roles.office_id', $officeId)
            ->where('permissions.name', $routeName)
            ->exists();

        if ($hasAccess) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki wewenang untuk mengakses modul ini di kantor yang dipilih.');
    }
}
