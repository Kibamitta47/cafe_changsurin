<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Cafe;
use Illuminate\Support\Facades\Storage; // เพิ่มบรรทัดนี้สำหรับจัดการ storage

class ReviewController extends Controller
{
    // แสดงฟอร์มเขียนรีวิว
    public function create($cafe_id) // เปลี่ยน $id เป็น $cafe_id เพื่อให้สอดคล้องกับ Route
    {
        $cafe = Cafe::findOrFail($cafe_id); // ใช้ $cafe_id ตรงนี้
        return view('review', compact('cafe'));
    }

    // บันทึกรีวิว
    public function store(Request $request)
    {
        // ตรวจสอบการเข้าสู่ระบบก่อน
        if (!Auth::check()) {
            // ใช้ route('login') โดยตรง แทนที่จะใช้ redirect()->route('login')
            // เพราะถ้าไม่มีการล็อกอินจริง ๆ Laravel จะจัดการให้ไปหน้า login อยู่แล้ว
            // แต่การตรวจสอบแบบนี้ช่วยให้ข้อความผิดพลาดชัดเจนขึ้น
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อนเขียนรีวิว');
        }

        $validated = $request->validate([
            'cafe_id' => 'required|exists:cafes,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            // ตรวจสอบขนาดไฟล์ภาพให้ใหญ่ขึ้นเล็กน้อย (เช่น 5MB = 5120 KB)
            // หรือปรับตามความเหมาะสมของ server และความต้องการ
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // เพิ่ม max size เป็น 5MB
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // ตรวจสอบว่าไฟล์ถูกต้องก่อนเก็บ
                if ($image->isValid()) {
                    $path = $image->store('public/review_images');
                    // เก็บเฉพาะชื่อไฟล์ที่ relative กับ storage/app/public
                    // เพื่อให้ asset() helper สามารถสร้าง URL ที่ถูกต้องได้
                    $imagePaths[] = str_replace('public/', '', $path);
                }
            }
        }

        // สร้างรีวิวใหม่
        Review::create([
            'cafe_id' => $request->cafe_id,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name, // ตรวจสอบว่าโมเดล User มีคอลัมน์ 'name'
            'title' => $request->title,
            'content' => $request->content,
            'rating' => $request->rating,
            'images' => json_encode($imagePaths), // เก็บ array ของ path รูปภาพเป็น JSON string
        ]);

        // เปลี่ยนเส้นทางกลับไปที่หน้ารายละเอียดคาเฟ่
        return redirect()->route('cafes.show', $request->cafe_id)
            ->with('success', 'ส่งรีวิวเรียบร้อยแล้ว');
    }
}