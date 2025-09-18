<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cafe;

class PageController extends Controller
{
    /**
     * เพิ่มเพื่อน LINE / ติดต่อผ่านไลน์
     * View: resources/views/pages/line.blade.php
     */
    public function showLinePage()
    {
        $lineOfficialId = '@363tvzhr';
        $lineAddUrl     = 'https://line.me/R/ti/p/' . ltrim($lineOfficialId, '@');

        return view('pages.line', [
            'lineOfficialId' => $lineOfficialId,
            'lineAddUrl'     => $lineAddUrl,
        ]);
    }

    /**
     * หน้าแพ็กเกจโฆษณา
     * View: resources/views/pages/advertising-packages.blade.php
     */
    public function showAdvertisingPackages()
    {
        $lineOfficialId = '@363tvzhr';
        $lineAddUrl     = 'https://line.me/R/ti/p/' . ltrim($lineOfficialId, '@');

        return view('pages.advertising-packages', [
            'lineId'     => $lineOfficialId,
            'lineAddUrl' => $lineAddUrl,
        ]);
    }

    /**
     * หน้าการรายงานปัญหา
     * View: resources/views/pages/report-problem-info.blade.php
     */
    public function showProblemInfoPage()
    {
        $problemEmail = 'snongchangsaren@gmail.com';

        $emailBodyTemplate = "สวัสดีทีมงานน้องช้างสะเร็น%0A%0A"
            ."ฉันต้องการแจ้งปัญหาการใช้งานดังนี้:%0A%0A"
            ."1. ชื่อผู้แจ้ง: [กรุณากรอกชื่อของคุณ]%0A%0A"
            ."2. URL ที่พบปัญหา: [กรุณาวางลิงก์ที่นี่ ถ้ามี]%0A%0A"
            ."3. รายละเอียดปัญหา: [กรุณาอธิบายปัญหาที่พบ]%0A%0A"
            ."(หากเป็นไปได้ กรุณาแนบภาพหน้าจอของปัญหามาด้วย)%0A%0A"
            ."ขอบคุณครับ/ค่ะ";

        return view('pages.report-problem-info', [
            'problemEmail'      => $problemEmail,
            'emailBodyTemplate' => $emailBodyTemplate,
        ]);
    }

    /**
     * เกี่ยวกับเรา
     * View: resources/views/pages/about-us.blade.php
     */
    public function showAboutPage()
    {
        return view('pages.about-us');
    }

    /**
     * Top 10 คาเฟ่ยอดนิยม (จากการรีวิวเท่านั้น)
     * View: resources/views/Top10.blade.php
     */
      public function showTop10Page()
    {
        $topRatedCafes = Cafe::query()
            ->where('status', 'approved')
            ->withAvg('reviews', 'rating')    // => reviews_avg_rating
            ->withCount('reviews')            // => reviews_count
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            // ดึงฟิลด์ที่ต้องใช้จริง (หรือจะไม่ใส่ select ก็ได้)
            ->get(['cafe_id', 'cafe_name', 'images']);

        // ⬅︎ ให้ชี้ไปที่ไฟล์ resources/views/cafes/top10.blade.php
        return view('Top10', compact('topRatedCafes'));
    }

    /**
     * คาเฟ่เปิดใหม่ (ล่าสุด 10)
     * View: resources/views/NewlyCafes.blade.php
     */
    public function showNewlyCafesPage()
    {
        $newCafes = Cafe::query()
            ->where('status', 'approved')
            ->orderByDesc('created_at')
            ->take(10)
            ->get(['id', 'cafe_name', 'image_path', 'created_at']);

        return view('NewlyCafes', compact('newCafes'));
    }

    /**
     * FAQ
     * View: resources/views/pages/faq.blade.php
     */
    public function showFAQPage()
    {
        return view('pages.faq');
    }
}
