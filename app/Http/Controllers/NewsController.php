<?php

namespace App\Http\Controllers;

// เรียกใช้ Model ที่เราเพิ่งแก้ไขไป
use App\Models\AddnewsAdmin;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * แสดงหน้ารวมข่าวสารทั้งหมด
     * (สำหรับ Route 'news.index')
     */
    public function index()
    {
        // ดึงเฉพาะข่าวที่ is_visible เป็น true, เรียงตามวันที่ล่าสุด, และแบ่งหน้า
        $news = AddnewsAdmin::where('is_visible', true)
                            ->latest('start_datetime') // เรียงตามวันที่เริ่มแสดงผล
                            ->paginate(12); // แสดง 12 ข่าวต่อหน้า

        return view('news.index', ['news' => $news]);
    }

    /**
     * แสดงรายละเอียดข่าว 1 ชิ้น
     * (สำหรับ Route 'news.show')
     */
    public function show($id)
    {
        // 1. ค้นหาข่าวหลักที่กำลังดูอยู่ (ถ้าไม่เจอจะแสดง 404)
        $newsItem = AddnewsAdmin::findOrFail($id);

        // 2. ค้นหาข่าวแนะนำ (ข่าวอื่นๆ) สำหรับ Sidebar
        //    - ไม่ใช่ข่าวที่กำลังดูอยู่
        //    - เป็นข่าวที่ is_visible = true
        //    - เรียงตามวันที่ล่าสุด
        //    - เอามา 5 ข่าว
        $recommendedNews = AddnewsAdmin::where('id', '!=', $id)
                                    ->where('is_visible', true)
                                    ->latest('start_datetime')
                                    ->take(5)
                                    ->get();

        // 3. ส่งข้อมูลทั้งหมดไปที่ View 'news.show'
        return view('news.show', [
            'newsItem' => $newsItem,
            'recommendedNews' => $recommendedNews
        ]);
    }
}
