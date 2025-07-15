<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cafe;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserCafeController extends Controller
{
    // แสดงฟอร์มสำหรับสร้างคาเฟ่ใหม่
    public function create()
    {
        return view('user.cafes.create');
    }

    // บันทึกข้อมูลคาเฟ่ที่สร้างใหม่ลงฐานข้อมูล
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
        $data['status'] = 'pending';

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('cafes', 'public');
            }
        }
        $data['images'] = $imagePaths;

        // กำหนดค่า array fields ให้แม้จะไม่มีข้อมูล
        $data['payment_methods'] = $request->input('payment_methods', []);
        $data['facilities'] = $request->input('facilities', []);
        $data['other_services'] = $request->input('other_services', []);
        $data['cafe_styles'] = $request->input('cafe_styles', []);

        Cafe::create($data);

        // คำสั่ง Redirect ไปยัง 'user.cafes.my'
        return redirect()->route('user.cafes.my')->with('success', 'ส่งข้อมูลคาเฟ่ให้ผู้ดูแลระบบตรวจสอบแล้ว');
    }

    // แสดงรายการคาเฟ่ของผู้ใช้
    public function myCafes()
    {
        $cafes = Auth::user()->cafes()->latest()->paginate(9);
        return view('user.my-cafes', compact('cafes'));
    }

    // แสดงฟอร์มแก้ไขคาเฟ่
    public function edit(Cafe $cafe)
    {
        if (Auth::id() !== $cafe->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.editcafe', compact('cafe'));
    }

    // อัปเดตข้อมูลคาเฟ่
    public function update(Request $request, Cafe $cafe)
    {
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
            'facilities.*' => 'string',
            'payment_methods.*' => 'string',
            'other_services.*' => 'string',
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

        // คำสั่ง Redirect ไปยัง 'user.cafes.my'
        return redirect()->route('user.cafes.my')->with('success', 'ข้อมูลคาเฟ่ได้รับการอัปเดตแล้ว');
    }

    // ลบคาเฟ่
    public function destroy(Cafe $cafe)
    {
        if (Auth::id() !== $cafe->user_id) {
            abort(403, 'Unauthorized action.');
        }

        if (is_array($cafe->images)) {
            foreach ($cafe->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $cafe->delete();

        return redirect()->route('user.cafes.my')->with('success', 'คาเฟ่ถูกลบเรียบร้อยแล้ว');
    }

    // แสดงรายละเอียดคาเฟ่และรีวิว
    public function show($id)
    {
        $cafe = Cafe::findOrFail($id);
        $reviews = Review::where('cafe_id', $cafe->id)->latest()->get();

        return view('user.cafe-detail', compact('cafe', 'reviews'));
    }

    // เพิ่มเมธอด myLikedCafes เพื่อแสดงรายการคาเฟ่ที่ผู้ใช้ชื่นชอบ
    public function myLikedCafes()
    {
        // ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
        if (Auth::check()) {
            $user = Auth::user();

            // ดึงคาเฟ่ที่ผู้ใช้กดไลค์ (Liked)
            // โดยสมมติว่าคุณมี Relationship ชื่อ 'likedCafes' ใน User Model ของคุณ
            $likedCafes = $user->likedCafes()->paginate(10); // หรือใช้ get() หากคุณไม่ต้องการ pagination

            // ส่งข้อมูลไปยัง view ที่คุณต้องการแสดงรายการคาเฟ่ที่ชอบ
            return view('user.liked-cafes', compact('likedCafes'));
        }
        
        // หากผู้ใช้ไม่ได้ล็อกอิน ให้เปลี่ยนเส้นทางไปยังหน้าล็อกอิน
        return redirect()->route('login');
    }
}