<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\Cafe;
use App\Models\AddnewsAdmin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AdminID;
use App\Models\User;


class AdminCafeController extends Controller
{
    public function create()
    {
        return view('admin.increase-admin');
    }

    public function store(Request $request)
    {
        $authenticatedUser = Auth::user();
        $authenticatedUserId = Auth::id();
        
        $validatedData = $request->validate([
            'cafe_name' => 'required|string|max:255',
            'place_name' => 'required|string|max:255',
            'address' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'price_range' => 'required|string|max:50',
            'phone' => 'nullable|string|regex:/^\d{9,10}$/',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:2048',
            'facebook_page' => 'nullable|string|max:2048',
            'instagram_page' => 'nullable|string|max:2048',
            'line_id' => 'nullable|string|max:255',
            'open_day' => 'nullable|string|max:255',
            'close_day' => 'nullable|string|max:255',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
            'is_new_opening' => 'nullable|boolean',
            'payment_methods' => 'nullable|array',
            'facilities' => 'nullable|array',
            'other_services' => 'nullable|array',
            'cafe_styles' => 'nullable|array',
            'other_style' => 'nullable|string|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $cafe = new Cafe($validatedData);
        
        if ($authenticatedUser instanceof AdminID) {
            $cafe->admin_id = $authenticatedUserId;
            $cafe->user_id = null; 
        } elseif ($authenticatedUser instanceof User) {
            $cafe->user_id = $authenticatedUserId;
            $cafe->admin_id = null; 
        } else {
            $cafe->user_id = null;
            $cafe->admin_id = null;
            Log::warning('Cafe created by unidentifiable user type.');
        }
        
        $cafe->status = 'approved'; 

        $cafe->is_new_opening = $request->has('is_new_opening');
        $cafe->payment_methods = $request->input('payment_methods', []);
        $cafe->facilities = $request->input('facilities', []);
        $cafe->other_services = $request->input('other_services', []);
        $cafe->cafe_styles = $request->input('cafe_styles', []);
        
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('cafes', 'public');
                $imagePaths[] = $path;
            }
        }
        $cafe->images = $imagePaths;

        try {
            $cafe->save();
            return redirect()->route('admin.cafe.index')->with('success', 'เพิ่มคาเฟ่ใหม่และอนุมัติเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            Log::error('Error saving cafe by admin: ' . $e->getMessage());
            foreach ($imagePaths as $path) {
                Storage::disk('public')->delete($path);
            }
            return redirect()->back()->withInput()->with('db_error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        }
    }

    public function index()
    {
        $cafes = Cafe::with(['user', 'admin'])->orderBy('created_at', 'desc')->paginate(10); 
        
        $approvedCount = Cafe::where('status', 'approved')->count();
        $pendingCount = Cafe::where('status', 'pending')->count();
        $rejectedCount = Cafe::where('status', 'rejected')->count();
        $totalCount = $cafes->total(); 

        return view('admin.itemscafe', compact('cafes', 'approvedCount', 'pendingCount', 'rejectedCount', 'totalCount'));
    }

    public function edit(Cafe $cafe)
    {
        return view('admin.edit-cafe', compact('cafe'));
    }

    public function update(Request $request, Cafe $cafe)
    {
        // ใช้ Primary Key ที่ถูกต้องในการ Log
        Log::info('Admin updating cafe ID: ' . $cafe->cafe_id);

        $validatedData = $request->validate([
            'cafe_name' => 'required|string|max:255',
            'place_name' => 'required|string|max:255',
            'address' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'price_range' => 'required|string|max:50',
            'phone' => 'nullable|string|regex:/^\d{9,10}$/',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:2048',
            'facebook_page' => 'nullable|string|max:2048',
            'instagram_page' => 'nullable|string|max:2048',
            'line_id' => 'nullable|string|max:255',
            'open_day' => 'nullable|string|max:255',
            'close_day' => 'nullable|string|max:255',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
            'is_new_opening' => 'nullable|boolean',
            'payment_methods' => 'nullable|array',
            'facilities' => 'nullable|array',
            'other_services' => 'nullable|array',
            'cafe_styles' => 'nullable|array',
            'other_style' => 'nullable|string|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $cafe->fill($validatedData);

        $cafe->is_new_opening = $request->has('is_new_opening');
        $cafe->payment_methods = $request->input('payment_methods', []);
        $cafe->facilities = $request->input('facilities', []);
        $cafe->other_services = $request->input('other_services', []);
        $cafe->cafe_styles = $request->input('cafe_styles', []);
        
        if ($request->hasFile('images')) {
            if ($cafe->images && is_array($cafe->images)) {
                foreach ($cafe->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $newImagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('cafes', 'public');
                $newImagePaths[] = $path;
            }
            $cafe->images = $newImagePaths;
        }

        try {
            $cafe->save();
            return redirect()->route('admin.cafe.index')->with('success', 'อัปเดตข้อมูลคาเฟ่เรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            Log::error('Error updating cafe: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('db_error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล');
        }
    }

    public function destroy(Cafe $cafe)
    {
        try {
            if ($cafe->images && is_array($cafe->images)) {
                foreach ($cafe->images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $cafe->delete();
            return redirect()->route('admin.cafe.index')->with('success', 'ลบข้อมูลคาเฟ่เรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            Log::error('Error deleting cafe: ' . $e->getMessage());
            return redirect()->back()->with('db_error', 'เกิดข้อผิดพลาดในการลบข้อมูล');
        }
    }
    
    public function updateStatus(Request $request, Cafe $cafe)
    {
        $request->validate(['status' => 'required|in:pending,approved,rejected']);
        
        try {
            $cafe->status = $request->status;
            $cafe->save();
            return redirect()->route('admin.cafe.index')->with('success', 'อัปเดตสถานะคาเฟ่เรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            Log::error('Error updating cafe status: ' . $e->getMessage());
            return redirect()->back()->with('db_error', 'เกิดข้อผิดพลาดในการอัปเดตสถานะ');
        }
    }

    public function checkCoordinates(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'cafe_id' => 'nullable|integer',
        ]);

        $query = Cafe::where('lat', $request->lat)->where('lng', $request->lng);

        if ($request->filled('cafe_id')) {
            // ✅ แก้ไข: ใช้ชื่อคอลัมน์ Primary Key ที่ถูกต้อง ('cafe_id')
            $query->where('cafe_id', '!=', $request->cafe_id);
        }

        return response()->json(['is_duplicate' => $query->exists()]);
    }
    
    public function welcome()
    {
        $cafes = Cafe::where('status', 'approved')
                     ->withAvg('reviews', 'rating')
                     ->latest()
                     ->get();

        //แก้ด้วยนะ
        // $news = DB::table('addnews_admins')->where('is_visible', true)->latest()->get();
        $news = AddnewsAdmin::where('is_visible', true);

        $likedCafeIds = [];
        if (Auth::check()) {
            $likedCafeIds = Auth::user()->likedCafes()->pluck('cafe_id')->toArray();
        }

        return view('welcome', compact('cafes', 'news', 'likedCafeIds'));
    }

    /**
     * ✅ แก้ไข: ฟังก์ชัน show() ทั้งหมด
     * ใช้ Route Model Binding (Cafe $cafe)
     */
    public function show(Cafe $cafe)
    {
        // โหลดค่าเฉลี่ย rating เข้ามาใน Model ที่มีอยู่แล้ว
        $cafe->loadAvg('reviews', 'rating'); 
        
        $reviews = $cafe->reviews()->latest()->paginate(5);
        return view('cafes.show', compact('cafe', 'reviews'));
    }
    
    public function toggleRecommend(Request $request, Cafe $cafe)
    {
        $cafe->is_recommended = !$cafe->is_recommended;
        $cafe->save();
        return back()->with('success', 'เปลี่ยนสถานะแนะนำสำเร็จ!');
    }

    public function recommend()
    {
        $allCafes = Cafe::where('status', 'approved')->withAvg('reviews', 'rating')->get();
        $topRatedCafes = $allCafes->sortByDesc('reviews_avg_rating')->take(10);
        $newCafes = $allCafes->where('is_new_opening', true)->sortByDesc('created_at')->take(5);
        $cafesByStyle = $allCafes->flatMap(function ($cafe) {
            $styles = is_array($cafe->cafe_styles) ? $cafe->cafe_styles : json_decode($cafe->cafe_styles, true) ?? [];
            return collect($styles)->map(function ($style) use ($cafe) {
                return ['style' => $style, 'cafe' => $cafe];
            });
        })->groupBy('style')->map(function ($group) {
            return $group->pluck('cafe')->take(5);
        });

        return view('admin.recommend', compact('topRatedCafes', 'newCafes', 'cafesByStyle'));
    }

    public function showRecommendPage()
    {
        $recommendedCafes = Cafe::where('status', 'approved')
                             ->where('is_recommended', true)
                             ->withAvg('reviews', 'rating')
                             ->get();
    
        $topRatedCafes = $recommendedCafes->sortByDesc('reviews_avg_rating')->take(10);
        $newCafes = $recommendedCafes->where('is_new_opening', true)->sortByDesc('created_at')->take(5);
        $cafesByStyle = $recommendedCafes->flatMap(function ($cafe) {
            $styles = is_array($cafe->cafe_styles) ? $cafe->cafe_styles : json_decode($cafe->cafe_styles, true) ?? [];
            return collect($styles)->map(function ($style) use ($cafe) {
                return ['style' => $style, 'cafe' => $cafe];
            });
        })->groupBy('style')->map(function ($group) {
            return $group->pluck('cafe')->take(5);
        });

        return view('cafes.recommend', compact('recommendedCafes', 'topRatedCafes', 'newCafes', 'cafesByStyle'));
    }
}