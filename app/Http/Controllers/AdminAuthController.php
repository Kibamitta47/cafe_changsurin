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
use App\Models\SearchLog; // ✅ เพิ่ม
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
        // ====== การ์ดสรุป ======
        $totalUsers   = User::count();
        $totalCafes   = Cafe::count();
        $pendingCafes = Cafe::where('status','pending')->count();
        $totalNews    = AddnewsAdmin::count();

        // ====== ผู้สมัครใหม่ 15 วันล่าสุด ======
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
            $dateString = $date->format('Y-m-d');
            $chartLabels[] = $date->translatedFormat('j M');
            $chartData[]   = $userRegistrationData[$dateString]->user_count ?? 0;
        }

        // ====== สัดส่วนสถานะคาเฟ่ ======
        $statusMap = [
            'approved' => 'อนุมัติแล้ว',
            'pending'  => 'รอตรวจสอบ',
            'rejected' => 'ไม่ผ่าน',
        ];
        $countsByStatus = Cafe::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')->pluck('c', 'status')->toArray();

        $cafeStatusLabels = [];
        $cafeStatusCounts = [];
        foreach ($statusMap as $key => $labelTh) {
            $cafeStatusLabels[] = $labelTh;
            $cafeStatusCounts[] = (int) ($countsByStatus[$key] ?? 0);
        }

        // ====== Top 10 คาเฟ่ (คะแนนรีวิวเฉลี่ย) ======
        $topCafes = Cafe::where('status','approved')
                        ->withAvg('reviews','rating')
                        ->orderBy('reviews_avg_rating','desc')
                        ->take(10)
                        ->get();

        $topCafeLabels = $topCafes->pluck('cafe_name');
        $topCafeData   = $topCafes->pluck('reviews_avg_rating')->map(fn($v)=> round((float)$v,2));

        // =================================================
        // ✅ Analytics "พฤติกรรมการค้นหา" สำหรับแดชบอร์ด
        // =================================================

        // --- 15 วันล่าสุด: แนวโน้มจำนวนการค้นหา ---
        $days = collect(range(14,0))->map(fn($d)=>Carbon::today()->subDays($d));
        $trendRaw = SearchLog::select(DB::raw('DATE(created_at) d'), DB::raw('COUNT(*) c'))
            ->where('created_at','>=', Carbon::today()->subDays(14))
            ->groupBy('d')->pluck('c','d');

        $searchTrendLabels = $days->map(fn($d)=>$d->translatedFormat('j M'));
        $searchTrendData   = $days->map(fn($d)=> (int) ($trendRaw[$d->format('Y-m-d')] ?? 0));

        // --- อัตราพบ/ไม่พบผลลัพธ์ (30 วัน) ---
        $since30 = Carbon::now()->subDays(30);
        $found   = SearchLog::where('created_at','>=',$since30)->where('has_results',true)->count();
        $notFound= SearchLog::where('created_at','>=',$since30)->where('has_results',false)->count();
        $searchOutcomeLabels = ['พบผลลัพธ์', 'ไม่พบผลลัพธ์'];
        $searchOutcomeData   = [(int)$found, (int)$notFound];

        // --- ชั่วโมงยอดนิยม (7 วันล่าสุด) ---
        $since7 = Carbon::now()->subDays(7);
        $hourRaw = SearchLog::where('created_at','>=',$since7)
            ->select(DB::raw('HOUR(created_at) h'), DB::raw('COUNT(*) c'))
            ->groupBy('h')->pluck('c','h'); // [0=>12, 1=>7, ...]
        $hourLabels = range(0,23);
        $hourCounts = array_map(fn($h)=> (int)($hourRaw[$h] ?? 0), $hourLabels);

        // --- คีย์เวิร์ดยอดฮิต Top 10 (30 วัน) ---
        // ใช้ normalized_query ถ้ามี; ถ้ายังไม่บันทึก ให้ normalize ด้วย SQL
        $topKeywords = SearchLog::where('created_at','>=',$since30)
            ->select(
                DB::raw("COALESCE(NULLIF(normalized_query,''), LOWER(TRIM(query))) as nq"),
                DB::raw('COUNT(*) as c')
            )
            ->groupBy('nq')
            ->orderByDesc('c')
            ->limit(10)
            ->get();

        $topKeywordLabels = $topKeywords->pluck('nq');
        $topKeywordCounts = $topKeywords->pluck('c')->map(fn($v)=>(int)$v);

        // --- วันในสัปดาห์ยอดนิยม (4 สัปดาห์) ---
        $since28 = Carbon::now()->subDays(28);
        // iso_weekday: 1=Mon ... 7=Sun
        $weekdayRaw = SearchLog::where('created_at','>=',$since28)
            ->select(DB::raw('WEEKDAY(created_at) wd'), DB::raw('COUNT(*) c'))
            ->groupBy('wd')->pluck('c','wd'); // 0=Mon ... 6=Sun (MySQL WEEKDAY)

        $weekdayMap = ['จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์','อาทิตย์'];
        $weekdayLabels = $weekdayMap;
        $weekdayCounts = [];
        for ($i=0; $i<7; $i++) {
            $weekdayCounts[] = (int) ($weekdayRaw[$i] ?? 0);
        }

        return view('admin.home', compact(
            'totalUsers','totalCafes','pendingCafes','totalNews',
            'chartLabels','chartData','cafeStatusLabels','cafeStatusCounts',
            'topCafeLabels','topCafeData',
            // ชุด analytics การค้นหา
            'searchTrendLabels','searchTrendData',
            'searchOutcomeLabels','searchOutcomeData',
            'hourLabels','hourCounts',
            'topKeywordLabels','topKeywordCounts',
            'weekdayLabels','weekdayCounts'
        ));
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
                if (!empty($admin->profile_image) && Storage::disk('public')->exists($admin->profile_image)) {
                    Storage::disk('public')->delete($admin->profile_image);
                }

                $uploaded = $request->file('profile_image');

                $driver  = extension_loaded('imagick') ? new ImagickDriver() : new GdDriver();
                $manager = new ImageManager($driver);

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
