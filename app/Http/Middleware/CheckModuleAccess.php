<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class CheckModuleAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();
        $routeName = $request->route()->getName();
        
        $path = database_path('otorisasi.json');
        if (!File::exists($path)) {
            return $next($request); 
        }

        $permissions = json_decode(File::get($path), true);
        $allowedRoutes = $permissions[$user->username] ?? [];

        if ($routeName == 'dashboard' || in_array($routeName, $allowedRoutes)) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki wewenang untuk mengakses modul ini.');
    }
}
