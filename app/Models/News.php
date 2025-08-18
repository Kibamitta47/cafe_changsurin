<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ชื่อคลาสต้องตรงกับชื่อไฟล์ คือ News
class News extends Model

{
    use HasFactory;

    /**
     * กำหนดชื่อตารางในฐานข้อมูลให้ตรงกับ Model นี้
     * (ถ้าชื่อตารางไม่ใช่ 'addnews_admins' ให้แก้ตรงนี้)
     */
    protected $table = 'addnews_admins';

    /**
     * กำหนดคอลัมน์ที่อนุญาตให้บันทึกข้อมูลได้ผ่าน Mass Assignment
     */
    protected $fillable = [
        'title',
        'content',
        'link_url',
        'start_datetime',
        'end_datetime',
        'images',
        'is_visible',
    ];

    /**
     * กำหนดการแปลงประเภทข้อมูล (Casting)
     * - 'images' จะถูกแปลงเป็น array โดยอัตโนมัติ
     * - 'is_visible' จะถูกแปลงเป็น boolean (true/false)
     * - วันที่จะถูกแปลงเป็น Carbon object เพื่อให้จัดการง่ายขึ้น
     */
    protected $casts = [
        'images' => 'array',
        'is_visible' => 'boolean',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];
}