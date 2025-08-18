<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View; // สำหรับ Type Hinting
use Illuminate\Http\RedirectResponse; // สำหรับ Type Hinting

class ReviewController extends Controller
{
    // เพิ่ม Middleware เพื่อตรวจสอบการล็อกอินสำหรับทุกเมธอด ยกเว้น create
    public function __construct()
    {
        $this->middleware('auth')->except(['create', 'store']);
        // Store จะมีการเช็คข้างในอีกทีเพื่อ redirect พร้อมข้อความที่ชัดเจน
    }

    // แสดงฟอร์มเขียนรีวิว
    public function create(string $cafe_id): View
    {
        $cafe = Cafe::findOrFail($cafe_id);
        return view('review', compact('cafe'));
    }

    // บันทึกรีวิวใหม่
    public function store(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อนเขียนรีวิว');
        }

        $validated = $request->validate([
            'cafe_id' => 'required|exists:cafes,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'images' => 'nullable|array|max:5', // จำกัดจำนวนไฟล์ไม่เกิน 5 รูป
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $path = $image->store('public/review_images');
                    $imagePaths[] = str_replace('public/', '', $path);
                }
            }
        }

        Review::create([
            'cafe_id' => $validated['cafe_id'],
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'rating' => $validated['rating'],
            'images' => json_encode($imagePaths), // ตอนบันทึกยังคงต้อง encode เป็น string
        ]);

        return redirect()->route('cafes.show', $validated['cafe_id'])
            ->with('success', 'ส่งรีวิวเรียบร้อยแล้ว ขอบคุณสำหรับความคิดเห็นครับ!');
    }

    // *** START: เมธอดที่เพิ่มเข้ามาใหม่ ***

    // แสดงฟอร์มแก้ไขรีวิว
    public function edit(Review $review): View
    {
        // ใช้ Policy ตรวจสอบสิทธิ์
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review')); // สร้าง view: resources/views/reviews/edit.blade.php
    }

    // อัปเดตข้อมูลรีวิว
    public function update(Request $request, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'images_to_delete' => 'nullable|array' // รับ path รูปเก่าที่ต้องการลบ
        ]);

        // 1. จัดการรูปภาพเก่าที่ต้องการลบ
        // ---- FIX: บรรทัดที่ 95 ----
        // ลบ json_decode() ออกไป เพราะ $review->images เป็น array อยู่แล้ว
        $currentImagePaths = $review->images ?? [];
        if (!empty($validated['images_to_delete'])) {
            foreach ($validated['images_to_delete'] as $imageToDelete) {
                Storage::delete('public/' . $imageToDelete);
            }
            // กรอง path ที่ถูกลบออกจาก array เดิม
            $currentImagePaths = array_diff($currentImagePaths, $validated['images_to_delete']);
        }

        // 2. จัดการรูปภาพใหม่ที่อัปโหลดเข้ามา
        $newImagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $path = $image->store('public/review_images');
                    $newImagePaths[] = str_replace('public/', '', $path);
                }
            }
        }

        // 3. รวม path รูปเก่าที่เหลือกับรูปใหม่
        $finalImagePaths = array_merge(array_values($currentImagePaths), $newImagePaths);

        // 4. อัปเดตข้อมูล
        $review->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'rating' => $validated['rating'],
            'images' => $finalImagePaths, // เมื่อใช้ casting ไม่ต้อง json_encode() เอง Laravel จัดการให้
        ]);

        return redirect()->route('user.dashboard')->with('success', 'อัปเดตรีวิวเรียบร้อยแล้ว');
    }

    // ลบรีวิว
    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        // ---- FIX: บรรทัดที่ 135 ----
        // ลบ json_decode() ออกไป เพราะ $review->images เป็น array อยู่แล้ว
        $imagePaths = $review->images ?? [];
        if (!empty($imagePaths)) {
            foreach ($imagePaths as $path) {
                Storage::delete('public/' . $path);
            }
        }

        // ลบข้อมูลรีวิวออกจากฐานข้อมูล
        $review->delete();

        return redirect()->route('user.dashboard')->with('success', 'ลบรีวิวเรียบร้อยแล้ว');
    }

    // *** END: เมธอดที่เพิ่มเข้ามาใหม่ ***
}