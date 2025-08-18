<?php

namespace App\Http\Controllers;

use App\Models\AddnewsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AddnewsAdminController extends Controller
{
    /**
     * แสดงหน้าจัดการข่าวสาร (รายการข่าวทั้งหมดและฟอร์มเพิ่มข่าว)
     */
    public function addNews()
    {
        // ดึงข่าวทั้งหมดโดยเรียงจากใหม่ไปเก่า
        $news = AddnewsAdmin::orderByDesc('created_at')->get();
        return view('admin.Addnews-admin', compact('news'));
    }

    /**
     * บันทึกข่าวสารใหม่ลงในฐานข้อมูล
     */
    public function storeNews(Request $request)
    {
        // ตรวจสอบความถูกต้องของข้อมูล
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
             'link_url' => 'nullable|url|max:2048',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB ต่อไฟล์
        ]);

        // จัดการการอัปโหลดรูปภาพ (ถ้ามี)
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                // เก็บไฟล์ใน storage/app/public/news_images และรับ path คืนมา
                $imagePaths[] = $image->store('news_images', 'public');
            }
            $validatedData['images'] = $imagePaths;
        }

        // กำหนดค่าเริ่มต้นสำหรับการแสดงผล
        $validatedData['is_visible'] = false;

        // สร้างข่าวใหม่ด้วยข้อมูลที่ผ่านการตรวจสอบแล้ว
        AddnewsAdmin::create($validatedData);

        return redirect()->route('admin.news.add')->with('success', 'เพิ่มข่าวสารเรียบร้อยแล้ว');
    }

    /**
     * แสดงหน้าฟอร์มสำหรับแก้ไขข่าวสาร
     */
    public function editNews($id)
    {
        $news = AddnewsAdmin::findOrFail($id);
        return view('admin.edit-news', compact('news'));
    }

    /**
     * อัปเดตข้อมูลข่าวสาร
     */
    public function updateNews(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'link_url' => 'nullable|url|max:2048',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        
        $news = AddnewsAdmin::findOrFail($id);

        // --- ส่วนจัดการรูปภาพ ---
        $existingImagesToKeep = $request->input('existing_images', []);
        $currentImagePaths = $news->images ?? [];
        $newImagePaths = [];

        // 1. ลบรูปเก่าที่ผู้ใช้ไม่ต้องการเก็บไว้
        foreach ($currentImagePaths as $oldImagePath) {
            if (!in_array($oldImagePath, $existingImagesToKeep)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        // 2. อัปโหลดรูปใหม่ (ถ้ามี)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $newImagePaths[] = $image->store('news_images', 'public');
            }
        }

        // 3. รวม path รูปเก่าที่เก็บไว้กับรูปใหม่
        $validatedData['images'] = array_values(array_merge($existingImagesToKeep, $newImagePaths));

        // อัปเดตข้อมูลข่าวสาร
        $news->update($validatedData);

        return redirect()->route('admin.news.add')->with('success', 'อัปเดตข่าวสารเรียบร้อยแล้ว');
    }

    /**
     * ลบข่าวสาร (รวมถึงไฟล์รูปภาพทั้งหมดที่เกี่ยวข้อง)
     */
    public function deleteNews($id)
    {
        $news = AddnewsAdmin::findOrFail($id);

        // ลบไฟล์รูปภาพทั้งหมดที่เชื่อมกับข่าวนี้
        if ($news->images && is_array($news->images)) {
            Storage::disk('public')->delete($news->images);
        }

        $news->delete();

        return redirect()->route('admin.news.add')->with('success', 'ลบข่าวสารเรียบร้อยแล้ว');
    }

    /**
     * สลับสถานะการแสดงผลหน้าแรก (เปิด/ปิด)
     */
    public function toggleVisibility(Request $request, $id)
    {
        $news = AddnewsAdmin::findOrFail($id);
        $news->is_visible = $request->has('is_visible'); // ถ้ามี checkbox 'is_visible' ส่งมาคือ true
        $news->save();

        return redirect()->route('admin.news.add')->with('success', 'อัปเดตการแสดงผลหน้าแรกเรียบร้อย');
    }

    /**
     * ลบรูปภาพเพียงรูปเดียว (สำหรับใช้กับ AJAX)
     */
    public function deleteImage(Request $request, $id)
    {
        $request->validate(['image_path' => 'required|string']);

        $news = AddnewsAdmin::findOrFail($id);
        $imagePath = $request->input('image_path');

        // ตรวจสอบว่ารูปภาพนี้เป็นของข่าวนี้จริงหรือไม่
        if ($news->images && is_array($news->images) && in_array($imagePath, $news->images)) {
            // ลบไฟล์ออกจาก Storage
            Storage::disk('public')->delete($imagePath);

            // ลบ path ออกจาก array ในฐานข้อมูล
            $updatedImages = array_filter($news->images, fn($img) => $img !== $imagePath);
            $news->images = array_values($updatedImages);
            $news->save();

            return response()->json(['success' => true, 'message' => 'ลบรูปภาพเรียบร้อยแล้ว']);
        }

        return response()->json(['success' => false, 'message' => 'ไม่พบรูปภาพ'], 404);
    }
}