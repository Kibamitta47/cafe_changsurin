<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cafe; // ✅ ใช้โมเดล Cafe

class PageController extends Controller
{
    /**
     * แสดงหน้าสำหรับเพิ่มเพื่อน LINE / ติดต่อผ่านไลน์
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
     * ✅ หน้าแพ็กเกจโฆษณา (ติดต่อผ่านไลน์เท่านั้น)
     */
    public function showAdvertisingPackages()
    {
        // ตั้งค่า LINE Official ที่ต้องการให้ติดต่อ
        $lineOfficialId = '@363tvzhr';
        $lineAddUrl     = 'https://line.me/R/ti/p/' . ltrim($lineOfficialId, '@');

        // ส่งไปให้ view ใช้
        return view('pages.advertising-packages', [
            'lineId'     => $lineOfficialId,
            'lineAddUrl' => $lineAddUrl,
        ]);
    }

    /**
     * แสดงหน้าการรายงานปัญหา (หากยังต้องใช้อีเมลสำหรับแจ้งปัญหา)
     * View: resources/views/pages/report-problem-info.blade.php
     */
    public function showProblemInfoPage()
    {
        $problemEmail = 'snongchangsaren@gmail.com';

        $emailBodyTemplate = "สวัสดีทีมงานน้องช้างสะเร็น" . "%0A%0A" .
                             "ฉันต้องการแจ้งปัญหาการใช้งานดังนี้:" . "%0A%0A" .
                             "1. ชื่อผู้แจ้ง: [กรุณากรอกชื่อของคุณ]" . "%0A%0A" .
                             "2. URL ที่พบปัญหา: [กรุณาวางลิงก์ที่นี่ ถ้ามี]" . "%0A%0A" .
                             "3. รายละเอียดปัญหา: [กรุณาอธิบายปัญหาที่พบ]" . "%0A%0A" .
                             "(หากเป็นไปได้ กรุณาแนบภาพหน้าจอของปัญหามาด้วย)" . "%0A%0A" .
                             "ขอบคุณครับ/ค่ะ";

        return view('pages.report-problem-info', [
            'problemEmail'       => $problemEmail,
            'emailBodyTemplate'  => $emailBodyTemplate,
        ]);
    }

    /**
     * แสดงหน้าเกี่ยวกับเรา
     * View: resources/views/pages/about-us.blade.php
     */
    public function showAboutPage()
    {
        return view('pages.about-us');
    }

    /**
     * แสดงหน้า Top 10 Cafes (เรียงตามเรตติ้งเฉลี่ย)
     * View: resources/views/Top10.blade.php
     */
    public function showTop10Page()
    {
        $cafes = Cafe::where('status', 'approved')
            ->withAvg('reviews', 'rating')          // จะได้ฟิลด์ reviews_avg_rating
            ->orderByDesc('reviews_avg_rating')
            ->take(10)
            ->get();

        return view('Top10', compact('cafes'));
    }

    /**
     * แสดงคาเฟ่ที่เพิ่มใหม่ (ล่าสุด 10 รายการ)
     * View: resources/views/NewlyCafes.blade.php
     */
    public function showNewlyCafesPage()
    {
        $cafes = Cafe::where('status', 'approved')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('NewlyCafes', compact('cafes'));
    }

    /**
     * แสดงหน้า FAQ
     * View: resources/views/pages/faq.blade.php
     */
    public function showFAQPage()
    {
        return view('pages.faq');
    }
}
