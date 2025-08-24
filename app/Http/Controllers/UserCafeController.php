<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cafe;
use App\Models\Review; // ตรวจสอบว่าคุณมี Model นี้
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View; // ✅ เพิ่ม use statement ที่จำเป็น

class UserCafeController extends Controller
{
    /**
     * แสดงฟอร์มสำหรับสร้างคาเฟ่ใหม่
     */
    public function create(): View
    {
       return view('user.cafes.create'); 
    }

    /**
     * บันทึกข้อมูลคาเฟ่ที่สร้างใหม่ลงฐานข้อมูล
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cafe_name' => 'required|string|max:255',
            'price_range' => 'nullable|string|max:255',
            'place_name' => 'required|string|max:255',
            'address' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'facebook_page' => 'nullable|string|max:2048',
            'instagram_page' => 'nullable|string|max:2048',
            'line_id' => 'nullable|string|max:255',
            'open_day' => 'nullable|string|max:255',
            'close_day' => 'nullable|string|max:255',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
            'payment_methods' => 'nullable|array',
            'facilities' => 'nullable|array',
            'other_services' => 'nullable|array',
            'facilities.*' => 'string',
            'payment_methods.*' => 'string',
            'other_services.*' => 'string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'cafe_styles' => 'nullable|array',
            'cafe_styles.*' => 'string',
            'other_style' => 'nullable|string|max:255',
            'parking' => 'nullable|boolean',
            'credit_card' => 'nullable|boolean',
        ]);

        $data['user_id'] = Auth::id();
        $data['status'] = 'pending'; // ตั้งสถานะเริ่มต้นเป็น 'รอการตรวจสอบ'

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('cafes', 'public');
            }
        }
        $data['images'] = $imagePaths;

        // กำหนดค่า default เป็น array ว่าง เพื่อป้องกัน error หากไม่มีการส่งค่ามา
        $data['payment_methods'] = $request->input('payment_methods', []);
        $data['facilities'] = $request->input('facilities', []);
        $data['other_services'] = $request->input('other_services', []);
        $data['cafe_styles'] = $request->input('cafe_styles', []);

        Cafe::create($data);

        return redirect()->route('user.cafes.my')->with('success', 'ส่งข้อมูลคาเฟ่ให้ผู้ดูแลระบบตรวจสอบแล้ว');
    }
    
    /**
     * แสดงหน้าคาเฟ่ทั้งหมดที่ผู้ใช้คนปัจจุบันเป็นเจ้าของ
     */
    public function myCafes(): View
    {
        $user = Auth::user();

        $cafes = $user->cafes()        // ดึงคาเฟ่ที่ผู้ใช้คนนี้เป็นเจ้าของ
            ->withCount('likers')  // นับจำนวนคนที่ไลค์
            ->latest()             // จัดเรียงจากใหม่ไปเก่า
            ->paginate(9);         // แบ่งหน้า

        return view('user.cafes.my', compact('cafes'));
    }

    /**
     * แสดงรายการคาเฟ่ที่ผู้ใช้คนปัจจุบันกดถูกใจ
     */
    public function myLikedCafes(): View
    {
        $user = Auth::user();

        // ดึงข้อมูลคาเฟ่ที่ผู้ใช้คนนี้กดถูกใจ ผ่าน relationship 'likedCafes'
        // และเรียงตามเวลาที่กดไลค์ล่าสุด (pivot_created_at)
        $likedCafes = $user->likedCafes()->latest('pivot_created_at')->paginate(12);

        return view('user.liked-cafes', compact('likedCafes'));
    }
    
    /**
     * แสดงฟอร์มแก้ไขคาเฟ่
     */
    public function edit(Cafe $cafe): View
    {
        // ตรวจสอบสิทธิ์ความเป็นเจ้าของ
        if (Auth::id() !== $cafe->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.editcafe', compact('cafe')); // ตรวจสอบว่า path view ถูกต้อง
    }

    /**
     * อัปเดตข้อมูลคาเฟ่ในฐานข้อมูล
     */
    public function update(Request $request, Cafe $cafe)
    {
        // ตรวจสอบสิทธิ์ความเป็นเจ้าของ
        if (Auth::id() !== $cafe->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'cafe_name' => 'required|string|max:255',
            'price_range' => 'nullable|string|max:255',
            'place_name' => 'required|string|max:255',
            'address' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'facebook_page' => 'nullable|string|max:2048',
            'instagram_page' => 'nullable|string|max:2048',
            'line_id' => 'nullable|string|max:255',
            'open_day' => 'nullable|string|max:255',
            'close_day' => 'nullable|string|max:255',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
            'payment_methods' => 'nullable|array',
            'facilities' => 'nullable|array',
            'other_services' => 'nullable|array',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'string',
            'cafe_styles' => 'nullable|array',
            'cafe_styles.*' => 'string',
            'other_style' => 'nullable|string|max:255',
            'parking' => 'nullable|boolean',
            'credit_card' => 'nullable|boolean',
        ]);

        $newImagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $newImagePaths[] = $image->store('cafes', 'public');
            }
        }
        
        $keptExistingImages = $request->input('existing_images', []);
        $data['images'] = array_merge($keptExistingImages, $newImagePaths);

        $data['payment_methods'] = $request->input('payment_methods', []);
        $data['facilities'] = $request->input('facilities', []);
        $data['other_services'] = $request->input('other_services', []);
        $data['cafe_styles'] = $request->input('cafe_styles', []);

        $cafe->update($data);

        return redirect()->route('user.cafes.my')->with('success', 'ข้อมูลคาเฟ่ได้รับการอัปเดตแล้ว');
    }

    /**
     * ลบคาเฟ่ออกจากฐานข้อมูล
     */
    public function destroy(Cafe $cafe)
    {
        // ตรวจสอบสิทธิ์ความเป็นเจ้าของ
        if (Auth::id() !== $cafe->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        if (is_array($cafe->images)) {
            foreach ($cafe->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $cafe->delete();

        return redirect()->route('user.cafes.my')->with('success', 'คาเฟ่ถูกลบเรียบร้อยแล้ว');
    }

    /**
     * สลับสถานะการกดไลค์ (Like/Unlike) สำหรับคาเฟ่
     */
    public function toggleLike(Request $request, Cafe $cafe)
    {
        $user = Auth::user();

        // ใช้ toggle() เพื่อเพิ่มหรือลบข้อมูลในตารางกลาง (cafe_likes) โดยอัตโนมัติ
        $user->likedCafes()->toggle($cafe->id);

        $isLiked = $user->likedCafes()->where('cafe_id', $cafe->id)->exists();

        return response()->json([
            'status' => 'success',
            'is_liked' => $isLiked,
            'message' => $isLiked ? 'Cafe liked successfully.' : 'Cafe unliked successfully.'
        ]);
    }
}