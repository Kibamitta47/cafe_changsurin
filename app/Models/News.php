<?php

namespace App\Models; // <-- ตรวจสอบให้แน่ใจว่าตรงนี้ถูกต้อง

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model // <-- ตรวจสอบให้แน่ใจว่าตรงนี้ถูกต้อง
{
    use HasFactory;

    // คุณอาจจะต้องเพิ่ม $fillable หรือ $guarded เพื่อให้สามารถบันทึกข้อมูลได้
    protected $fillable = [
        'title',
        'content',
        'images', // ถ้าเก็บเป็น JSON
        'status',
        'start_datetime',
        'end_datetime',
    ];

    // หากคุณใช้ casts สำหรับ field 'images' ที่เป็น array/json
    protected $casts = [
        'images' => 'array',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    // คุณอาจจะต้องกำหนดชื่อตารางถ้าไม่ได้ใช้ convention 'news'
    // protected $table = 'your_news_table_name';
}