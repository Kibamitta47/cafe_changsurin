<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SearchLog;
use Illuminate\Support\Facades\Auth;

class SearchLogController extends Controller
{
    public function store(Request $request)
    {
        // รองรับทั้งกรณีส่ง query อย่างเดียว หรือส่ง filters มาด้วย
        $data = $request->validate([
            'query'         => 'nullable|string|max:255',
            'result_count'  => 'required|integer|min:0',
            'source'        => 'nullable|string|max:50', // เช่น web-filter | web-search
            'filters'       => 'nullable|array',         // รายละเอียดตัวกรองทั้งหมด (optional)
            'filters.rating'           => 'nullable|integer|min:0|max:5',
            'filters.isNewOpening'     => 'nullable|boolean',
            'filters.time'             => 'nullable|string|max:10',
            'filters.days'             => 'nullable|array',
            'filters.days.*'           => 'string|max:20',
            'filters.priceRanges'      => 'nullable|array',
            'filters.priceRanges.*'    => 'string|max:10', // '฿', '฿฿', ...
            'filters.styles'           => 'nullable|array',
            'filters.styles.*'         => 'string|max:255',
            'filters.facilities'       => 'nullable|array',
            'filters.facilities.*'     => 'string|max:255',
            'filters.paymentMethods'   => 'nullable|array',
            'filters.paymentMethods.*' => 'string|max:255',
            'filters.otherServices'    => 'nullable|array',
            'filters.otherServices.*'  => 'string|max:255',
        ]);

        $query = trim((string)($data['query'] ?? ''));
        $normalized = mb_strtolower($query);

        // ต้นทาง (fallback เป็น web-filter)
        $source = $data['source'] ?? 'web-filter';

        // เก็บรายละเอียดฟิลเตอร์ไว้ใน metadata (ถ้าส่งมา)
        $metadata = [];
        if (isset($data['filters']) && is_array($data['filters'])) {
            $metadata['filters'] = $data['filters'];
        }

        SearchLog::create([
            'user_id'          => Auth::id(),
            'query'            => $query,
            'normalized_query' => $query === '' ? null : $normalized,
            'has_results'      => (int)$data['result_count'] > 0,
            'result_count'     => (int)$data['result_count'],
            'source'           => $source,
            'ip'               => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'metadata'         => empty($metadata) ? null : $metadata,
        ]);

        return response()->json(['status' => 'ok']);
    }
}
