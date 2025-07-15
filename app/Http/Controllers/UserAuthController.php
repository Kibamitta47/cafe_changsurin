<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // **ต้องมีบรรทัดนี้สำหรับการจัดการรูปภาพ**

class UserAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/')->with('success', 'สมัครสมาชิกเรียบร้อยแล้ว');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'ข้อมูลเข้าสู่ระบบไม่ถูกต้อง',
        ]);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * อัปเดตข้อมูลโปรไฟล์ของผู้ใช้ (รวมการจัดการรูปภาพ)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // ตรวจสอบและ Validate ข้อมูลที่ผู้ใช้ส่งมา
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, 
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // **เพิ่ม validation สำหรับรูปภาพ**
        ]);

        $data = $request->only('name', 'email');

        // จัดการการอัปโหลดรูปภาพ (หากมีการอัปโหลดใหม่)
        if ($request->hasFile('profile_image')) {
            // ลบรูปเก่าถ้ามี
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            // บันทึกรูปใหม่
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        // อัปเดตข้อมูลผู้ใช้ในฐานข้อมูล
        $user->update($data);

        return redirect()->route('user.profile.show')->with('success', 'อัปเดตข้อมูลโปรไฟล์เรียบร้อยแล้ว');
    }
}
