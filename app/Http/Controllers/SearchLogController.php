<?php

namespace App\Http\Controllers;

use App\Models\SearchLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

// ใช้ได้เมื่อมี ext-intl (แนะนำให้เปิด) เพื่อ normalize อักษรผสมให้เป็นมาตรฐาน
use Normalizer;

class SearchLogController extends Controller
{
    public function store(Request $request)
    {
        /**
         * รองรับได้ทั้ง 2 payload:
         * (A) จากหน้าเว็บ: { keyword, results_count, filters, source }
         * (B) ตรงกับ migration: { query, normalized_query, has_results, result_count, source }
         */
        $query = trim((string) ($request->input('query', $request->input('keyword', ''))));
        $resultCount = (int) ($request->input('result_count', $request->input('results_count', 0)));
        $hasResults  = filter_var(
            $request->input('has_results', $resultCount > 0),
            FILTER_VALIDATE_BOOLEAN
        );
        $source = (string) $request->input('source', 'web');

        // ถ้า query ว่าง ไม่ต้องบันทึก
        if ($query === '') {
            return response()->json(['status' => 'ok', 'skipped' => true, 'reason' => 'empty query']);
        }

        // ==============================
        // ✅ Normalize แบบ "ไม่ตัดสระ/วรรณยุกต์ไทย"
        // ==============================
        // 1) รวมสระ/วรรณยุกต์ให้เป็นรูปแบบมาตรฐาน (NFC) ถ้ามี ext-intl
        $normalized = $query;
        if (class_exists(\Normalizer::class)) {
            $normalized = \Normalizer::normalize($normalized, \Normalizer::FORM_C);
        }
        // 2) แปลงเป็นพิมพ์เล็ก + บีบช่องว่าง
        $normalized = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $normalized)));
        // 3) (ถ้าต้องการตัดสัญลักษณ์พิเศษ ให้ "เก็บ" \p{M} ไว้เสมอ)
        // หมายเหตุ: \p{M} = combining marks (สระ/วรรณยุกต์)
        $normalized = preg_replace('/[^\p{L}\p{M}\p{N}\s]/u', '', $normalized);

        // ==============================
        // กันสแปม 8 วิ/คอนเทนต์เดียวกันต่อ user/ip
        // ==============================
        $identity = Auth::id() ?: $request->ip();
        $sig = md5(json_encode(['q' => $normalized, 's' => (string) $source]));
        $lockKey = "searchlog:cooldown:{$identity}:{$sig}";
        if (Cache::has($lockKey)) {
            return response()->json(['status' => 'ok', 'skipped' => true]);
        }
        Cache::put($lockKey, 1, now()->addSeconds(8));

        // บันทึกตาม schema
        $log = SearchLog::create([
            'user_id'          => Auth::id(),
            'query'            => $query,
            'normalized_query' => $normalized ?: null,
            'has_results'      => (bool) $hasResults,
            'result_count'     => $resultCount,
            'source'           => $source,
            'ip'               => $request->ip(),
            'user_agent'       => $request->userAgent(),
        ]);

        return response()->json(['status' => 'success', 'id' => $log->id]);
    }
}
