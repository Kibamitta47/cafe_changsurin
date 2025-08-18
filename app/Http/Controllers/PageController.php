<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * แสดงหน้าสำหรับเพิ่มเพื่อน LINE
     */
    public function showLinePage()
    {
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
        return view('pages.about-us');
    }
}
