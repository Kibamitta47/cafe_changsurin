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
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        // กำหนด guard เริ่มต้น ถ้าไม่ได้ระบุมา
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            // ตรวจสอบว่า guard นี้มีการล็อกอินอยู่หรือไม่
            if (Auth::guard($guard)->check()) {

                // =============================================================
                // [หัวใจของการแก้ไข]
                // ถ้าเป็น guard 'admin' ที่ล็อกอินอยู่ ให้ส่งไปที่ 'home.admin'
                // =============================================================
                if ($guard === 'admin') {
                    return redirect()->route('home.admin');
                }
                
                // สำหรับ guard อื่นๆ (ซึ่งก็คือ 'web' หรือ User)
                // ให้ส่งไปที่หน้า HOME ปกติ (ซึ่งคือ /dashboard)
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}