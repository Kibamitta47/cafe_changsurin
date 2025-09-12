<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminID;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Cafe;
use App\Models\AddnewsAdmin;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showRegister()
    {
        return view('admin.register-admin');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:admin_id,Email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        AdminID::create([
            'UserName' => $request->name,
            'Email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login.admin')->with('success', 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ');
    }

    public function showLogin()
    {
        return view('admin.login-admin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // ใช้คอลัมน์ Email ให้ตรง DB
        $ok = Auth::guard('admin')->attempt(
            ['Email' => $credentials['email'], 'password' => $credentials['password']],
            $request->boolean('remember')
        );

        if ($ok) {
            $request->session()->regenerate();
            return redirect()->route('admin.home')->with('success', 'เข้าสู่ระบบสำเร็จ');
        }

        return back()->withErrors([
            'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            Log::info('Admin logout: ' . Auth::guard('admin')->user()->Email);
        }

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.admin')->with('success', 'ออกจากระบบสำเร็จ');
    }

    public function home()
    {
        $totalUsers   = User::count();
        $totalCafes   = Cafe::count();
        $pendingCafes = Cafe::where('status', 'pending')->count();
        $totalNews    = AddnewsAdmin::count();

        $userRegistrationData = User::select(
                DB::raw('DATE(created_at) as registration_date'),
                DB::raw('count(*) as user_count')
            )
            ->where('created_at', '>=', now()->subDays(15))
            ->groupBy('registration_date')
            ->orderBy('registration_date', 'asc')
            ->get()
            ->keyBy('registration_date');

        $chartLabels = [];
        $chartData   = [];
        for ($i = 14; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $chartLabels[] = $date->translatedFormat('j M');
            $chartData[]   = $userRegistrationData[$dateString]->user_count ?? 0;
        }

        $cafeStatusData   = Cafe::select('status', DB::raw('count(*) as count'))->groupBy('status')->get();
        $cafeStatusLabels = $cafeStatusData->pluck('status')->map(function ($status) {
            return $status;
        });
        $cafeStatusCounts = $cafeStatusData->pluck('count');

        $topCafes      = Cafe::where('status', 'approved')->withAvg('reviews', 'rating')
                            ->orderBy('reviews_avg_rating', 'desc')->take(10)->get();
        $topCafeLabels = $topCafes->pluck('cafe_name');
        $topCafeData   = $topCafes->pluck('reviews_avg_rating');

        return view('admin.home', compact(
            'totalUsers','totalCafes','pendingCafes','totalNews',
            'chartLabels','chartData','cafeStatusLabels','cafeStatusCounts',
            'topCafeLabels','topCafeData'
        ));
    }

    public function editProfile()
    {
        return view('admin.edit-profileadmin', [
            'admin' => Auth::guard('admin')->user()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'required','email',
                Rule::unique('admin_id','Email')->ignore($admin->admin_id, 'admin_id'),
            ],
            'password'       => 'nullable|min:8|confirmed',
            'profile_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $admin->UserName = $request->name;
        $admin->Email    = $request->email;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            // ถ้าเก็บ path เต็ม เช่น "profile_images/xxxx.jpg" ก็ลบได้เลย
            if (!empty($admin->profile_image)) {
                Storage::disk('public')->delete($admin->profile_image);
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $admin->profile_image = $path;
        }

        $admin->save();

        return back()->with('success', 'อัปเดตข้อมูลโปรไฟล์เรียบร้อยแล้ว');
    }
}
