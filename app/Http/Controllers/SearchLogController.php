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
        // รับเฉพาะฟิลด์ตาม migration เท่านั้น
        $data = $request->validate([
            'query'            => 'required|string|max:2000',
            'normalized_query' => 'nullable|string|max:2000',
            'has_results'      => 'required|boolean',
            'result_count'     => 'required|integer|min:0',
            'source'           => 'nullable|string|max:50', // web/line/api
        ]);

        // กันสแปมระยะสั้น (8 วิ) ตาม query+filter เดิม ๆ จาก same user/ip
        $identity = Auth::id() ?: $request->ip();
        $sig = md5(json_encode([
            'q' => (string)$data['query'],
            'n' => (string)($data['normalized_query'] ?? ''),
            's' => (string)($data['source'] ?? 'web'),
        ]));
        $lockKey = "searchlog:cooldown:{$identity}:{$sig}";
        if (Cache::has($lockKey)) {
            return response()->json(['status' => 'ok', 'skipped' => true]);
        }
        Cache::put($lockKey, 1, now()->addSeconds(8));

        // บันทึก
        $log = SearchLog::create([
            'user_id'         => Auth::id(),
            'query'           => $data['query'],
            'normalized_query'=> $data['normalized_query'] ?? null,
            'has_results'     => (bool)$data['has_results'],
            'result_count'    => (int)$data['result_count'],
            'source'          => $data['source'] ?? 'web',
            'ip'              => $request->ip(),
            'user_agent'      => $request->userAgent(),
        ]);

        return response()->json(['status' => 'success', 'id' => $log->id]);
    }
}
