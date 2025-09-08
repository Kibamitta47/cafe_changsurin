<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // แสดงฟอร์ม
    public function edit(Request $request): View
    {
        return view('user.profile-edit', ['user' => $request->user()]);
    }

    // อัปเดตเฉพาะชื่อ + รูปโปรไฟล์
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'profile_image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:3072'], // 3MB
        ], [
            'name.required' => 'กรุณากรอกชื่อผู้ใช้',
            'profile_image.image' => 'ไฟล์รูปไม่ถูกต้อง',
            'profile_image.mimes' => 'รองรับเฉพาะ jpg, jpeg, png, webp',
            'profile_image.max' => 'ขนาดไฟล์ต้องไม่เกิน 3MB',
        ]);

        // อัปโหลดรูป (ถ้ามี)
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('avatars', 'public');
            $user->profile_image = $path;
        }

        // อัปเดตชื่อเท่านั้น
        $user->name = $validated['name'];
        $user->save();

        return Redirect::route('user.profile.show')->with('success', 'บันทึกโปรไฟล์เรียบร้อยแล้ว');
    }
}
