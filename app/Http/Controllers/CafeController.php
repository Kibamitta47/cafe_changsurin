<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CafeController extends Controller
{
    // ... เมธอดอื่นๆ เช่น create, store, edit ...

    /**
     * แสดงหน้าคาเฟ่ทั้งหมดที่ผู้ใช้เป็นเจ้าของ
     */
    public function myCafes(): View
    {
        $user = Auth::user();

        // ✅ [ส่วนสำคัญ] แก้ไข Query เพื่อดึงข้อมูลคาเฟ่
        $cafes = $user->cafes() // 1. ดึงคาเฟ่ที่ผู้ใช้คนนี้เป็นเจ้าของ
            ->withCount('likers') // 2. นับจำนวนความสัมพันธ์ 'likers' และสร้าง field 'likers_count' อัตโนมัติ
            ->latest() // 3. จัดเรียงจากใหม่ไปเก่า
            ->paginate(9); // 4. แบ่งหน้า (ปรับจำนวนได้ตามต้องการ)

        return view('user.cafes.my', compact('cafes'));
    }

    // ... เมธอดอื่นๆ เช่น update, destroy ...
}