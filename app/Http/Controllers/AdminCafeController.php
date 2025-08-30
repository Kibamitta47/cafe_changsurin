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
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminCafeController extends Controller
{
    public function create(): View
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
        } elseif ($authenticatedUser instanceof User) {
            $cafe->user_id = $authenticatedUserId;
        } else {
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

    public function index(): View
    {
        $cafes = Cafe::with(['user', 'admin'])->orderBy('created_at', 'desc')->paginate(10); 
        $approvedCount = Cafe::where('status', 'approved')->count();
        $pendingCount = Cafe::where('status', 'pending')->count();
        $rejectedCount = Cafe::where('status', 'rejected')->count();
        $totalCount = $cafes->total(); 

        return view('admin.itemscafe', compact('cafes', 'approvedCount', 'pendingCount', 'rejectedCount', 'totalCount'));
    }

    public function edit(Cafe $cafe): View
    {
        return view('admin.edit-cafe', compact('cafe'));
    }

    public function update(Request $request, Cafe $cafe)
    {
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
            $query->where('cafe_id', '!=', $request->cafe_id);
        }

        return response()->json(['is_duplicate' => $query->exists()]);
    }
    
    public function welcome(): View
    {
        $cafes = Cafe::where('status', 'approved')
                     ->withAvg('reviews', 'rating')
                     ->latest()
                     ->get();

        $likedCafeIds = [];
        if (Auth::check() && Auth::user()->relationLoaded('likedCafes')) {
            $likedCafeIds = Auth::user()->likedCafes()->pluck('cafe_id')->toArray();
        }

        $news = AddnewsAdmin::where('is_visible', true)
                            ->latest()
                            ->take(5)
                            ->get();

        return view('welcome', compact('cafes', 'news', 'likedCafeIds'));
    }

    public function show(Cafe $cafe): View
    {
        $cafe->loadAvg('reviews', 'rating'); 
        $reviews = $cafe->reviews()->latest()->paginate(5);
        return view('cafes.show', compact('cafe', 'reviews'));
    }
    
   

    public function recommend(): View
    {
        $allCafes = Cafe::where('status', 'approved')->withAvg('reviews', 'rating')->get();

        return view('admin.recommend', [
            'topRatedCafes'   => $this->getTopRated($allCafes),
            'newCafes'        => $this->getNewCafes($allCafes),
            'cafesByStyle'    => $this->groupByStyle($allCafes),
            'cafesByPrice'    => $this->groupByPrice($allCafes),
            'cafesByFacility' => $this->groupByFacility($allCafes),
        ]);
    }

    

    /* ================= Helper Methods ================= */
    private function getTopRated(Collection $cafes): Collection
    {
        return $cafes->sortByDesc('reviews_avg_rating')->take(10);
    }

    private function getNewCafes(Collection $cafes): Collection
    {
        return $cafes->where('is_new_opening', true)->sortByDesc('created_at')->take(5);
    }

    private function groupByStyle(Collection $cafes): Collection
    {
        return $cafes->flatMap(function ($cafe) {
            $styles = is_array($cafe->cafe_styles) ? $cafe->cafe_styles : json_decode($cafe->cafe_styles, true) ?? [];
            return collect($styles)->map(fn($style) => ['style' => $style, 'cafe' => $cafe]);
        })->groupBy('style')->map(fn($group) => $group->pluck('cafe')->take(5));
    }

    private function groupByPrice(Collection $cafes): Collection
    {
        return $cafes->groupBy('price_range')->map(fn($group) => $group->take(5));
    }

    private function groupByFacility(Collection $cafes): Collection
    {
        return $cafes->flatMap(function ($cafe) {
            $facilities = is_array($cafe->facilities) ? $cafe->facilities : json_decode($cafe->facilities, true) ?? [];
            return collect($facilities)->map(fn($f) => ['facility' => $f, 'cafe' => $cafe]);
        })->groupBy('facility')->map(fn($group) => $group->take(5));
    }

    
}
