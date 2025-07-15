<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\AdminID;

class AdminAuthController extends Controller
{
    public function showRegister()
    {
        return view('admin.register-admin');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_id,Email',
            'password' => 'required|min:6',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        AdminID::create([
            'UserName' => $request->name,
            'Email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login.admin')->with('success', 'สมัครสมาชิกสำเร็จ');
    }

    public function showLogin()
    {
        return view('admin.login-admin');
    }

    public function login(Request $request)
    {
        Log::info('Admin login attempt:', $request->all());

        $request->validate([
            'Email' => 'required|email',
            'Password' => 'required',
        ]);

        $admin = AdminID::where('Email', $request->Email)->first();

        if (!$admin) {
            Log::info('Admin not found with email: ' . $request->Email);
            return back()->withErrors([
                'Email' => 'ไม่พบอีเมลนี้ในระบบ',
            ])->withInput($request->only('Email'));
        }

        if (!Hash::check($request->Password, $admin->password)) {
            Log::info('Password mismatch for admin: ' . $request->Email);
            return back()->withErrors([
                'Password' => 'รหัสผ่านไม่ถูกต้อง',
            ])->withInput($request->only('Email'));
        }

        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();

        Log::info('Admin login successful: ' . $request->Email);

        return redirect()->intended('/home-admin')->with('success', 'เข้าสู่ระบบสำเร็จ');
    }

    public function logout(Request $request)
    {
        Log::info('Admin logout: ' . Auth::guard('admin')->user()->Email);

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.admin')->with('success', 'ออกจากระบบสำเร็จ');
    }

    public function home()
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return redirect()->route('login.admin');
        }

        Log::info('Admin accessing home: ' . $admin->Email);
        return view('admin.home', compact('admin'));
    }

    public function editProfile()
    {
        return view('admin.edit-profileadmin');
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_id,Email,' . $admin->AdminID . ',AdminID',
            'password' => 'nullable|min:6',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $admin->UserName = $request->input('name');
        $admin->Email = $request->input('email');

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            if ($admin->profile_image) {
                \Storage::disk('public')->delete('profile_images/' . $admin->profile_image);
            }

            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('profile_images', $imageName, 'public');
            $admin->profile_image = $imageName;
        }

        $admin->save();

        return redirect()->back()->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }
}
