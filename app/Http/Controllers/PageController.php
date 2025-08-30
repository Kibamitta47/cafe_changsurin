<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cafe; // ✅ 1. เพิ่มการ 'use' Cafe Model

class PageController extends Controller
{
    /**
     * แสดงหน้าสำหรับเพิ่มเพื่อน LINE
     */
    public function showLinePage()
    {
        // 🎯 หมายเหตุ: ตรวจสอบให้แน่ใจว่ามีไฟล์ view ที่ resources/views/pages/line.blade.php
        $lineOfficialId = '@363tvzhr';
        $lineAddUrl = 'https://line.me/ti/p/' . $lineOfficialId;

        return view('pages.line', [
            'lineOfficialId' => $lineOfficialId,
            'lineAddUrl' => $lineAddUrl,
        ]);
    }

    /**
     * แสดงหน้ารายละเอียดและราคาแพ็กเกจโฆษณา
     */
    public function showAdvertisingPackages()
    {
        // 🎯 หมายเหตุ: ตรวจสอบให้แน่ใจว่ามีไฟล์ view ที่ resources/views/pages/advertising-packages.blade.php
        $contactEmail = 'nongchangsaren@gmail.com';

        return view('pages.advertising-packages', [
            'contactEmail' => $contactEmail
        ]);
    }

    /**
     * แสดงหน้าการรายงานปัญหา
     */
    public function showProblemInfoPage()
    {
        // 🎯 หมายเหตุ: ตรวจสอบให้แน่ใจว่ามีไฟล์ view ที่ resources/views/pages/report-problem-info.blade.php
        $problemEmail = 'snongchangsaren@gmail.com';

        $emailBodyTemplate = "สวัสดีทีมงานน้องช้างสะเร็น" . "%0A%0A" .
                             "ฉันต้องการแจ้งปัญหาการใช้งานดังนี้:" . "%0A%0A" .
                             "1. ชื่อผู้แจ้ง: [กรุณากรอกชื่อของคุณ]" . "%0A%0A" .
                             "2. URL ที่พบปัญหา: [กรุณาวางลิงก์ที่นี่ ถ้ามี]" . "%0A%0A" .
                             "3. รายละเอียดปัญหา: [กรุณาอธิบายปัญหาที่พบ]" . "%0A%0A" .
                             "(หากเป็นไปได้ กรุณาแนบภาพหน้าจอของปัญหามาด้วย)" . "%0A%0A" .
                             "ขอบคุณครับ/ค่ะ";

        return view('pages.report-problem-info', [
            'problemEmail' => $problemEmail,
            'emailBodyTemplate' => $emailBodyTemplate,
        ]);
    }

    /**
     * แสดงหน้าเกี่ยวกับเรา
     */
    public function showAboutPage()
    {
        // 🎯 หมายเหตุ: ตรวจสอบให้แน่ใจว่ามีไฟล์ view ที่ resources/views/pages/about-us.blade.php
        return view('pages.about-us');
    }

    /**
     * ✅ 2. รวมฟังก์ชัน showTop10Page เข้ามาอย่างถูกต้อง
     * แสดงหน้า Top 10 Cafes
     */
    public function showTop10Page()
    {
        // ดึงข้อมูลคาเฟ่ที่ได้รับการอนุมัติ (status = 'approved')
        // และเรียงตาม rating จากมากไปน้อย, เอามา 10 อันดับแรก
        $cafes = Cafe::where('status', 'approved')
                     ->withAvg('reviews', 'rating') // คำนวณ rating เฉลี่ย
                     ->orderByDesc('reviews_avg_rating')
                     ->take(10)
                     ->get();

        // ส่งข้อมูลไปยัง View
        return view('Top10', compact('cafes'));
    }

public function showNewlyCafesPage()
{
    // ดึงข้อมูลคาเฟ่ที่ได้รับการอนุมัติ (status = 'approved')
    // และเรียงตาม created_at จากใหม่ไปเก่า, เอามา 10 อันดับแรก
    $cafes = Cafe::where('status', 'approved')
                 ->orderByDesc('created_at')
                 ->take(10)
                 ->get();

    // ส่งข้อมูลไปยัง View NewlyCafes.blade.php
    return view('NewlyCafes', compact('cafes'));
}
    /**
     * แสดงหน้า FAQ
     */
    public function showFAQPage()
    {
        // 🎯 หมายเหตุ: ตรวจสอบให้แน่ใจว่ามีไฟล์ view ที่ resources/views/pages/faq.blade.php
        return view('pages.faq');
    }
}