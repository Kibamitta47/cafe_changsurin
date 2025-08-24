<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate; // เพิ่ม Gate สำหรับ Authorization

class UserReviewController extends Controller
{
    /**
     * Display a listing of the user's reviews.
     */
    public function index()
    {
        $reviews = auth()->user()
            ->reviews()
            ->with('cafe')
            ->latest()
            ->paginate(10);

        return view('user.reviews.my', compact('reviews'));
    }

    /**
     * Show the form for editing the specified review.
     */
    public function edit(Review $review)
    {
        // ใช้ Gate/Policy (วิธีที่แนะนำ) หรือ check ownership โดยตรง
        if (auth()->id() !== $review->user_id) {
            abort(403, 'UNAUTHORIZED ACTION.');
        }

        // ไม่จำเป็นต้อง load('cafe') เพราะ View น่าจะเรียกใช้ $review->cafe โดยตรง
        // Eloquent จะ Lazy Load ให้เอง
        return view('user.reviews.edit', compact('review'));
    }

    /**
     * ✅ แก้ไข: ฟังก์ชัน update() ทั้งหมด
     */
    public function update(Request $request, Review $review)
    {
        // 1. ตรวจสอบสิทธิ์ (Authorization) ก่อนเสมอ
        if (auth()->id() !== $review->user_id) {
            abort(403, 'UNAUTHORIZED ACTION.');
        }

        // 2. ตรวจสอบความถูกต้องของข้อมูล (Validation)
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'images' => 'nullable|array|max:5', // จำกัดจำนวนไฟล์ทั้งหมด
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // 3. จัดการรูปภาพ
        $newImagePaths = $review->images; // เริ่มต้นด้วยรูปภาพเก่า (เนื่องจากเรามี casting เป็น array แล้ว)

        // ถ้ามีการอัปโหลดไฟล์ใหม่เข้ามา
        if ($request->hasFile('images')) {
            
            // 3.1 ลบรูปภาพเก่าทั้งหมดออกจาก Storage
            // การมี Casting 'images' => 'array' ทำให้ $review->images เป็น array เสมอ
            if (!empty($review->images)) {
                Storage::disk('public')->delete($review->images);
            }

            // 3.2 อัปโหลดรูปภาพใหม่และเก็บ Path
            $newImagePaths = []; // เริ่มต้น array ใหม่สำหรับเก็บ path รูปใหม่
            foreach ($request->file('images') as $file) {
                $newImagePaths[] = $file->store('reviews', 'public');
            }
        }
        
        // 4. เตรียมข้อมูลทั้งหมดสำหรับอัปเดต
        $reviewDataToUpdate = [
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'rating' => $validatedData['rating'],
            'images' => $newImagePaths, // อัปเดตคอลัมน์ images ด้วย path ใหม่ (หรือของเดิมถ้าไม่มีการอัปโหลด)
        ];

        // 5. อัปเดตข้อมูลลงฐานข้อมูล
        $review->update($reviewDataToUpdate);
        
        // 6. ส่งกลับไปหน้า "รีวิวของฉัน" พร้อมข้อความ Success
        return redirect()->route('user.reviews.my')->with('success', 'แก้ไขรีวิวเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review)
    {
        if (auth()->id() !== $review->user_id) {
            abort(403, 'UNAUTHORIZED ACTION.');
        }

        // การมี Casting 'images' => 'array' ทำให้ $review->images เป็น array เสมอ
        if (!empty($review->images)) {
            Storage::disk('public')->delete($review->images);
        }

        $review->delete();

        return redirect()->route('user.reviews.my')->with('success', 'ลบรีวิวเรียบร้อยแล้ว!');
    }
}