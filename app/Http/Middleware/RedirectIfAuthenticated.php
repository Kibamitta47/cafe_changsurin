<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
{
    $guards = empty($guards) ? [null] : $guards;

    foreach ($guards as $guard) {
        if (Auth::guard($guard)->check()) {
            // ถ้าเป็น admin ให้ redirect ไป /home-admin
            if ($guard === 'admin') {
                return redirect()->route('home.admin');
            }
            // ถ้าเป็น user ให้ redirect ไป /welcome
            else {
                return redirect()->route('welcome');
            }
        }
    }

    return $next($request);
}
}
