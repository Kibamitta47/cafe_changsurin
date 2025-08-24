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
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($cafe_id): View
    {
        $cafe = Cafe::where('cafe_id', $cafe_id)->firstOrFail(); 
        return view('review', compact('cafe'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'cafe_id' => 'required|exists:cafes,cafe_id',
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'rating'  => 'required|integer|min:1|max:5',
            'images'   => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $dataToStore = [
            'cafe_id'   => $validatedData['cafe_id'],
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name,
            'title'     => $validatedData['title'],
            'content'   => $validatedData['content'],
            'rating'    => $validatedData['rating'],
            'images'    => [],
        ];

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $imageFile) {
                if ($imageFile->isValid()) {
                    $path = $imageFile->store('reviews', 'public');
                    $imagePaths[] = $path;
                }
            }
            $dataToStore['images'] = $imagePaths;
        }

        Review::create($dataToStore);

        return redirect()->route('cafes.show', $dataToStore['cafe_id'])
                         ->with('success', 'ขอบคุณสำหรับรีวิวของคุณ!');
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