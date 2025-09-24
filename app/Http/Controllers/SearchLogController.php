<?php

namespace App\Http\Controllers;

use App\Models\SearchLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SearchLogController extends Controller
{
    public function store(Request $request)
    {
        // รองรับได้ทั้ง 2 payload:
        // (A) แบบใหม่จากหน้าเว็บ: { keyword, results_count, filters, source }
        // (B) แบบตรงกับ migration:   { query, normalized_query, has_results, result_count, source }

        // ดึงค่าที่มาก่อน: ถ้ามี 'query' ใช้เลย; ถ้าไม่มีให้ใช้ 'keyword'
        $query = trim((string)($request->input('query', $request->input('keyword', ''))));
        $resultCount = (int)($request->input('result_count', $request->input('results_count', 0)));
        $hasResults  = filter_var(
            $request->input('has_results', $resultCount > 0),
            FILTER_VALIDATE_BOOLEAN
        );
        $source      = $request->input('source', 'web');

        // ถ้า query ว่างจริง ๆ ก็ไม่ต้องบันทึก
        if ($query === '') {
            return response()->json(['status' => 'ok', 'skipped' => true, 'reason' => 'empty query']);
        }

        // normalize ฝั่งเซิร์ฟเวอร์ (กันพลาดจาก client)
        $normalized = mb_strtolower(
            preg_replace('/[^\p{L}\p{N}\s]/u', '', trim(preg_replace('/\s+/u', ' ', $query)))
        );

        // กันสแปมระยะสั้น (8 วิ) จากผู้ใช้/ไอพีเดียวกัน ด้วยลายเซ็นเนื้อหา
        $identity = Auth::id() ?: $request->ip();
        $sig = md5(json_encode(['q' => $normalized, 's' => (string)$source]));
        $lockKey = "searchlog:cooldown:{$identity}:{$sig}";
        if (Cache::has($lockKey)) {
            return response()->json(['status' => 'ok', 'skipped' => true]);
        }
        Cache::put($lockKey, 1, now()->addSeconds(8));

        // บันทึกตาม schema เดิม
        $log = SearchLog::create([
            'user_id'          => Auth::id(),
            'query'            => $query,
            'normalized_query' => $normalized ?: null,
            'has_results'      => (bool)$hasResults,
            'result_count'     => $resultCount,
            'source'           => $source,
            'ip'               => $request->ip(),
            'user_agent'       => $request->userAgent(),
        ]);

        return response()->json(['status' => 'success', 'id' => $log->id]);
    }
}
