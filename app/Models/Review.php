<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cafe;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * เพิ่ม 'images' กลับเข้ามา
     */
    protected $fillable = [
        'cafe_id',
        'user_id',
        'user_name',
        'title',
        'content',
        'rating',
        'images', // <-- เพิ่มกลับเข้ามา
    ];

    /**
     * The attributes that should be cast.
     * บอกให้ Laravel แปลงคอลัมน์ 'images' เป็น array โดยอัตโนมัติ
     */
    protected $casts = [
         'images' => 'array',
         'created_at' => 'datetime', // เพิ่มบรรทัดนี้
        'updated_at' => 'datetime', // และบรรทัดนี้
    ];

    /**
     * ความสัมพันธ์กับ Cafe (ถูกต้องแล้ว)
     */
    public function cafe()
{
    return $this->belongsTo(Cafe::class);
}


    /**
     * ความสัมพันธ์กับ User (ถูกต้องแล้ว)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // **ลบฟังก์ชัน images() ที่เป็น relationship ทิ้งไป** เพราะเราไม่ได้ใช้แล้ว
    // public function images()
    // {
    //     return $this->hasMany(Image::class);
    // }
}