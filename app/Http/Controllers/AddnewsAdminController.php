<?php

namespace App\Http\Controllers;

use App\Models\AddnewsAdmin; // ตรวจสอบให้แน่ใจว่า Model นี้ถูกต้อง
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AddnewsAdminController extends Controller
{
    public function addNews()
    {
        $news = AddnewsAdmin::orderByDesc('created_at')->get();
        return view('admin.Addnews-admin', compact('news'));
    }

    public function storeNews(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('news_images', 'public');
            }
        }

        AddnewsAdmin::create([
            'title' => $request->title,
            'content' => $request->content,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
            'images' => $imagePaths,
            'is_visible' => false,
        ]);

        // การ redirect ตรงนี้ถูกต้องแล้ว เพราะไปที่ 'admin.news.add' ซึ่งคือหน้าแสดงรายการ/เพิ่มข่าวสาร
        return redirect()->route('admin.news.add')->with('success', 'เพิ่มข่าวสารเรียบร้อยแล้ว');
    }

    public function editNews($id)
    {
        $news = AddnewsAdmin::findOrFail($id);
        return view('admin.edit-news', compact('news'));
    }

    public function updateNews(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $news = AddnewsAdmin::findOrFail($id);
        $existingImagesToKeep = $request->input('existing_images', []);
        $existingImagesToKeep = is_array($existingImagesToKeep) ? array_filter($existingImagesToKeep, 'is_string') : [];

        $currentImagePaths = $news->images && is_array($news->images) ? $news->images : [];
        $newImagePaths = [];

        foreach ($currentImagePaths as $oldImagePath) {
            if (!in_array($oldImagePath, $existingImagesToKeep)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $newImagePaths[] = $image->store('news_images', 'public');
            }
        }

        $news->title = $request->title;
        $news->content = $request->content;
        $news->start_datetime = $request->start_datetime;
        $news->end_datetime = $request->end_datetime;
        $news->images = array_values(array_merge($existingImagesToKeep, $newImagePaths));
        $news->save();

        // การ redirect ตรงนี้ถูกต้องแล้ว
        return redirect()->route('admin.news.add')->with('success', 'อัปเดตข่าวสารเรียบร้อยแล้ว');
    }

    public function deleteNews($id)
    {
        $news = AddnewsAdmin::findOrFail($id);

        if ($news->images && is_array($news->images)) {
            foreach ($news->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $news->delete();

        // การ redirect ตรงนี้ถูกต้องแล้ว
        return redirect()->route('admin.news.add')->with('success', 'ลบข่าวสารเรียบร้อยแล้ว');
    }

    public function toggleVisibility(Request $request, $id)
    {
        $news = AddnewsAdmin::findOrFail($id);
        // ตรวจสอบว่า checkbox ถูกติ๊กหรือไม่
        $news->is_visible = $request->has('is_visible');
        $news->save();

        // การ redirect ตรงนี้ถูกต้องแล้ว
        return redirect()->route('admin.news.add')->with('success', 'อัปเดตการแสดงผลหน้าแรกเรียบร้อย');
    }

    public function deleteImage(Request $request, $id)
    {
        $request->validate([
            'image_path' => 'required|string'
        ]);

        $news = AddnewsAdmin::findOrFail($id);
        $imagePath = $request->input('image_path');

        if ($news->images && is_array($news->images) && in_array($imagePath, $news->images)) {
            Storage::disk('public')->delete($imagePath);

            $updatedImages = array_filter($news->images, function($img) use ($imagePath) {
                return $img !== $imagePath;
            });

            $news->images = array_values($updatedImages);
            $news->save();

            return response()->json(['success' => true, 'message' => 'ลบรูปภาพเรียบร้อยแล้ว']);
        }

        return response()->json(['success' => false, 'message' => 'ไม่พบรูปภาพหรือรูปภาพไม่เกี่ยวข้องกับข่าวสารนี้'], 404);
    }
}
