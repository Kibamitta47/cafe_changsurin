<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'user_id',
        'user_name', // เพิ่ม user_name เข้ามา
        'title',     // เพิ่ม title เข้ามา
        'content',   // เพิ่ม content เข้ามา
        'rating',
        'images',
    ];

    protected $casts = [
        'images' => 'array',  // แปลง JSON <-> array อัตโนมัติ
    ];

    // ความสัมพันธ์กับ Cafe
    public function cafe()
    {
        return $this->belongsTo(Cafe::class);
    }

    // ความสัมพันธ์กับ User (ถ้ามี)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}