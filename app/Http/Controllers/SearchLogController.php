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
        // รองรับทั้ง 2 payload:
        // (A) จากหน้าเว็บ: { keyword, results_count, filters, source }
        // (B) ตรงกับ migration: { query, normalized_query, has_results, result_count, source }

        $query = trim((string)($request->input('query', $request->input('keyword', ''))));
        $resultCount = (int)($request->input('result_count', $request->input('results_count', 0)));
        $hasResults  = filter_var(
            $request->input('has_results', $resultCount > 0),
            FILTER_VALIDATE_BOOLEAN
        );
        $source = $request->input('source', 'web');

        if ($query === '') {
            return response()->json(['status' => 'ok', 'skipped' => true, 'reason' => 'empty query']);
        }

        /**
         * ✅ สำคัญ: เก็บวรรณยุกต์/สระภาษาไทยไว้ (รวมถึงสระที่ลอยบน/ล่าง)
         * เดิมใช้ [^\p{L}\p{N}\s] ทำให้ \p{M} (combining marks) ถูกลบทิ้ง
         * แก้เป็น [^\p{L}\p{M}\p{N}\s] เพื่อ "อนุญาต" \p{M}
         */
        $normalized = mb_strtolower(
            preg_replace(
                '/[^\p{L}\p{M}\p{N}\s]/u', // ← อนุญาต Marks
                '',
                trim(preg_replace('/\s+/u', ' ', $query))
            )
        );

        // กันสแปม 8 วิ/เนื้อหาเดียวกัน ต่อผู้ใช้/ไอพี
        $identity = Auth::id() ?: $request->ip();
        $sig = md5(json_encode(['q' => $normalized, 's' => (string)$source]));
        $lockKey = "searchlog:cooldown:{$identity}:{$sig}";
        if (Cache::has($lockKey)) {
            return response()->json(['status' => 'ok', 'skipped' => true]);
        }
        Cache::put($lockKey, 1, now()->addSeconds(8));

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
