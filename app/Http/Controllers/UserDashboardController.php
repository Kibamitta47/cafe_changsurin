<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    /**
     * แสดงหน้าแดชบอร์ดของผู้ใช้ (เวอร์ชั่นการ์ดสถิติ)
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $user = Auth::user();

        // 1. ดึงข้อมูลคาเฟ่ทั้งหมดของผู้ใช้มาก่อน
        $userCafes = Cafe::where('user_id', $user->id)->get();

        // 2. คำนวณค่าสถิติต่างๆ ที่ View ต้องการ
        $totalCafes = $userCafes->count();
        $approvedCafes = $userCafes->where('status', 'approved')->count();
        $pendingCafes = $userCafes->where('status', 'pending')->count();

        // 3. ส่งตัวแปรสถิติทั้งหมดไปที่ View
        return view('user.dashboard', [
            'totalCafes' => $totalCafes,
            'approvedCafes' => $approvedCafes,
            'pendingCafes' => $pendingCafes,
        ]);
    }
}