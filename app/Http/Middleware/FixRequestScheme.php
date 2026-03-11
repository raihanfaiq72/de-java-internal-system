<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FixRequestScheme
{
    public function handle(Request $request, Closure $next)
    {
        $proto = $request->headers->get('x-forwarded-proto');
        $forwardedSsl = $request->headers->get('x-forwarded-ssl');
        $frontEndHttps = $request->headers->get('front-end-https');
        $cfVisitor = $request->headers->get('cf-visitor');

        $isHttps = false;

        if (is_string($proto) && str_contains(strtolower($proto), 'https')) {
            $isHttps = true;
        } elseif (is_string($forwardedSsl) && strtolower($forwardedSsl) === 'on') {
            $isHttps = true;
        } elseif (is_string($frontEndHttps) && strtolower($frontEndHttps) === 'on') {
            $isHttps = true;
        } elseif (is_string($cfVisitor) && str_contains(strtolower($cfVisitor), '"scheme":"https"')) {
            $isHttps = true;
        } elseif (is_string(config('app.url')) && str_starts_with(strtolower(config('app.url')), 'https://')) {
            $isHttps = true;
        }

        if ($isHttps) {
            $request->server->set('HTTPS', 'on');
            $request->server->set('SERVER_PORT', '443');
        }

        return $next($request);
    }
}
