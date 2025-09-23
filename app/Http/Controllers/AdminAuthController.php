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
use App\Models\SearchLog; // ✅ เพิ่มสำหรับสถิติการค้นหา
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

class AdminAuthController extends Controller
{
    public function showRegister() { 
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

    public function showLogin() { 
        return view('admin.login-admin'); 
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $ok = Auth::guard('admin')->attempt(
            ['Email' => $credentials['email'], 'password' => $credentials['password']],
            $request->boolean('remember')
        );

        if ($ok) {
            $request->session()->regenerate();
            return redirect()->route('admin.home')->with('success', 'เข้าสู่ระบบสำเร็จ');
        }

        return back()->withErrors(['email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            Log::info('Admin logout: ' . Auth::guard('admin')->user()->Email);
        }
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login.admin')->with('success','ออกจากระบบสำเร็จ');
    }

    public function home()
    {
        // =========================
        // การ์ดสรุป
        // =========================
        $totalUsers   = User::count();
        $totalCafes   = Cafe::count();
        $pendingCafes = Cafe::where('status','pending')->count();
        $totalNews    = AddnewsAdmin::count();

        // =========================
        // ผู้สมัครใหม่ 15 วันล่าสุด
        // =========================
        $userRegistrationData = User::select(
                DB::raw('DATE(created_at) as registration_date'),
                DB::raw('count(*) as user_count')
            )
            ->where('created_at','>=', now()->subDays(15))
            ->groupBy('registration_date')->orderBy('registration_date','asc')
            ->get()->keyBy('registration_date');

        $chartLabels = [];
        $chartData   = [];
        for ($i=14; $i>=0; $i--) {
            $date = now()->subDays($i);
            $dateString   = $date->format('Y-m-d');
            $chartLabels[] = $date->translatedFormat('j M');
            $chartData[]   = (int)($userRegistrationData[$dateString]->user_count ?? 0);
        }

        // =========================
        // สัดส่วนสถานะคาเฟ่ (ป้ายภาษาไทย + คุมลำดับ)
        // =========================
        $statusMap = [
            'approved' => 'อนุมัติแล้ว',
            'pending'  => 'รอตรวจสอบ',
            'rejected' => 'ไม่ผ่าน',
        ];

        $countsByStatus = Cafe::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();

        $cafeStatusLabels = [];
        $cafeStatusCounts = [];
        foreach ($statusMap as $key => $labelTh) {
            $cafeStatusLabels[] = $labelTh;
            $cafeStatusCounts[] = (int) ($countsByStatus[$key] ?? 0);
        }

        // =========================
        // Top 10 คาเฟ่ (คะแนนรีวิวเฉลี่ย)
        // =========================
        $topCafes = Cafe::where('status','approved')
                        ->withAvg('reviews','rating')
                        ->orderBy('reviews_avg_rating','desc')
                        ->take(10)
                        ->get();

        $topCafeLabels = $topCafes->pluck('cafe_name');
        $topCafeData   = $topCafes->pluck('reviews_avg_rating')->map(fn($v)=> round((float)$v,2));

        // ======================================================
        // ✅ ชุด "วิเคราะห์การค้นหาคาเฟ่" สำหรับแดชบอร์ด (Chart.js)
        // ======================================================

        // ---- 15 วันล่าสุด: แนวโน้มจำนวนการค้นหา ----
        $days = collect(range(14,0))->map(fn($d)=>Carbon::today()->subDays($d));
        $searchTrendLabels = $days->map->translatedFormat('j M')->values();

        $trendRaw = SearchLog::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('created_at','>=',Carbon::today()->subDays(14))
            ->groupBy('d')->pluck('c','d');

        $searchTrendData = $days->map(fn($d)=> (int)($trendRaw[$d->toDateString()] ?? 0))->values();

        // ---- Top 10 Keywords (กันค่าว่าง) ----
        $topKeywords = SearchLog::whereNotNull('keyword')
            ->whereRaw("TRIM(keyword) <> ''")
            ->select('keyword', DB::raw('COUNT(*) as cnt'))
            ->groupBy('keyword')->orderByDesc('cnt')->limit(10)->get();

        $topKeywordLabels = $topKeywords->pluck('keyword');
        $topKeywordCounts = $topKeywords->pluck('cnt')->map(fn($v)=> (int)$v);

        // ---- อัตราเจอ/ไม่เจอผลลัพธ์ ----
        $success  = (int) SearchLog::where('results','>',0)->count();
        $noResult = (int) SearchLog::where('results',0)->count();
        $searchOutcomeLabels = ['พบคาเฟ่','ไม่พบคาเฟ่'];
        $searchOutcomeData   = [$success, $noResult];

        // ---- ชั่วโมงยอดนิยม (สัปดาห์ล่าสุด) ----
        $hourAgg = SearchLog::where('created_at','>=',Carbon::now()->subWeek())
            ->selectRaw('HOUR(created_at) as h, COUNT(*) as c')
            ->groupBy('h')->pluck('c','h'); // [hour => count]

        $hourLabels = range(0,23);
        $hourCounts = array_map(fn($h)=> (int)($hourAgg[$h] ?? 0), $hourLabels);

        // ---- วันในสัปดาห์ยอดนิยม (4 สัปดาห์ล่าสุด) ----
        // MySQL WEEKDAY(): 0=Mon..6=Sun
        $weekdayAgg = SearchLog::where('created_at','>=',Carbon::now()->subWeeks(4))
            ->selectRaw('WEEKDAY(created_at) as w, COUNT(*) as c')
            ->groupBy('w')->pluck('c','w');

        $weekdayLabels = ['จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์','อาทิตย์'];
        $weekdayCounts = [];
        for ($i=0;$i<7;$i++){
            $weekdayCounts[] = (int)($weekdayAgg[$i] ?? 0);
        }

        return view('admin.home', [
            // การ์ดสรุป
            'totalUsers'        => $totalUsers,
            'totalCafes'        => $totalCafes,
            'pendingCafes'      => $pendingCafes,
            'totalNews'         => $totalNews,

            // ผู้สมัครใหม่ (15 วัน)
            'chartLabels'       => $chartLabels,
            'chartData'         => $chartData,

            // สถานะคาเฟ่
            'cafeStatusLabels'  => $cafeStatusLabels,
            'cafeStatusCounts'  => $cafeStatusCounts,

            // Top คาเฟ่
            'topCafeLabels'     => $topCafeLabels,
            'topCafeData'       => $topCafeData,

            // ✅ Analytics การค้นหา
            'searchTrendLabels'   => $searchTrendLabels,
            'searchTrendData'     => $searchTrendData,
            'topKeywordLabels'    => $topKeywordLabels,
            'topKeywordCounts'    => $topKeywordCounts,
            'searchOutcomeLabels' => $searchOutcomeLabels,
            'searchOutcomeData'   => $searchOutcomeData,
            'hourLabels'          => $hourLabels,
            'hourCounts'          => $hourCounts,
            'weekdayLabels'       => $weekdayLabels,
            'weekdayCounts'       => $weekdayCounts,
        ]);
    }

    public function editProfile()
    {
        return view('admin.edit-profileadmin', ['admin'=>Auth::guard('admin')->user()]);
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => ['required','email', Rule::unique('admin_id','Email')->ignore($admin->admin_id,'admin_id')],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'password'      => 'nullable|min:8|confirmed',
        ]);

        try {
            $admin->UserName = $request->name;
            $admin->Email    = $request->email;

            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }

            if ($request->hasFile('profile_image')) {
                // ลบไฟล์เดิมถ้ามี
                if (!empty($admin->profile_image) && Storage::disk('public')->exists($admin->profile_image)) {
                    Storage::disk('public')->delete($admin->profile_image);
                }

                $uploaded = $request->file('profile_image');

                $driver  = extension_loaded('imagick') ? new ImagickDriver() : new GdDriver();
                $manager = new ImageManager($driver);

                // ครอปสี่เหลี่ยมจตุรัส 512x512 แล้วบีบเป็น webp
                $image   = $manager->read($uploaded->getPathname())->cover(512, 512);
                $encoded = $image->toWebp(75);

                $filename = 'profile_'.$admin->admin_id.'_'.time().'.webp';
                Storage::disk('public')->put('profile_images/'.$filename, (string) $encoded);
                $admin->profile_image = 'profile_images/'.$filename;
            }

            $admin->save();

            return back()->with('success','แก้ไขโปรไฟล์สำเร็จ');
        } catch (\Throwable $e) {
            Log::error('Update profile failed', ['error'=>$e->getMessage()]);
            return back()->with('error','ไม่สามารถแก้ไขโปรไฟล์ได้ โปรดลองอีกครั้ง')->withInput();
        }
    }
}
