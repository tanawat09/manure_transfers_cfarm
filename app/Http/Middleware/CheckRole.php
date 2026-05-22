<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Admin has access to everything
        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        foreach ($roles as $role) {
            if ($request->user()->role === $role) {
                return $next($request);
            }
        }

        return redirect()->route('dashboard')->with('error', 'ขออภัย คุณไม่มีสิทธิ์เข้าถึงเมนูนี้');
    }
}
