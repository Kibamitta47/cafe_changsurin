<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddnewsAdmin extends Model
{
    use HasFactory;

    // ตั้งชื่อตารางให้ตรงกับฐานข้อมูล
    // จาก migrate:status คุณใช้ 'addnews_admin' ซึ่งถูกต้อง
    protected $table = 'addnews_admin';

    protected $fillable = [
        'title',
        'content',
        'images',
        'start_datetime',
        'end_datetime',
        'is_visible', // <--- ต้องมีใน fillable
    ];

    protected $casts = [
        'images' => 'array',
        'is_visible' => 'boolean', // <--- ต้องมี cast เป็น boolean
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];
}
