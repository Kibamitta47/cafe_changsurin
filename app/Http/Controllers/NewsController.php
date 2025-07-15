<?php

namespace App\Http\Controllers;

use App\Models\News; // <-- บรรทัดนี้สำคัญมาก ตรวจสอบการสะกด
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function show($id)
    {
        // ดึงข่าวสารจากฐานข้อมูลด้วย ID หรือแสดง Error 404 หากไม่พบ
        $newsItem = News::findOrFail($id);

        // ส่งข้อมูลข่าวสารไปยัง View ที่ชื่อ 'news.show'
        return view('news.show', compact('newsItem'));
    }
}