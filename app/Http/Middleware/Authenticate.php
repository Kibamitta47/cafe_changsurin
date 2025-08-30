<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(Request $request): ?string
    {
        // ตรวจสอบว่า request ที่เข้ามาไม่ใช่ JSON request ใช่หรือไม่
        if (! $request->expectsJson()) {

            // =================================================================
            // [หัวใจของการแก้ไข]
            // ตรวจสอบว่า URL ที่ผู้ใช้พยายามจะเข้า ขึ้นต้นด้วย 'admin/' หรือไม่
            // หรือ Route ที่เรียกใช้ มีชื่อขึ้นต้นด้วย 'admin.' หรือไม่
            // =================================================================
            if ($request->is('admin/*') || $request->routeIs('admin.*')) {
                // ถ้าใช่ ให้ส่งไปที่หน้า login ของ Admin
                return route('login.admin');
            }

            // ถ้าไม่ใช่เงื่อนไขข้างบน ก็ให้ส่งไปที่หน้า login ของ User ตามปกติ
            return route('login');
        }

        return null;
    }
}