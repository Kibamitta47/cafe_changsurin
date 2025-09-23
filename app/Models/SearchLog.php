<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;

    protected $table = 'search_logs';

    protected $fillable = [
        'query',            // คำค้นที่ผู้ใช้พิมพ์
        'normalized_query', // คำค้น normalize (lower/trim) ใช้ group ได้แม่น
        'has_results',      // true/false พบผลลัพธ์ไหม
        'results_count',    // จำนวนผลลัพธ์ที่พบ
        'user_id',          // ผู้ใช้ (nullable)
        'meta',             // JSON เพิ่มเติม (เช่น filters, source)
    ];

    protected $casts = [
        'has_results'  => 'boolean',
        'results_count'=> 'integer',
        'meta'         => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
