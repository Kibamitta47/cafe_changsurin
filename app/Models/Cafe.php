<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cafe extends Model
{
    use HasFactory;

    /**
     * ✅ 1. กำหนด Primary Key (ถูกต้องแล้ว)
     */
    protected $primaryKey = 'cafe_id';

    /**
     * ✅ 2. (สำคัญมาก) บอก Laravel ว่า Primary Key ของเราไม่ใช่ Auto-incrementing
     * เพราะ 'cafe_id' อาจจะไม่ได้เรียงลำดับเหมือน 'id' ปกติ
     * การตั้งค่านี้จะป้องกันไม่ให้ Laravel พยายามแปลงค่าเป็น integer และหาค่าไม่เจอ
     */
    public $incrementing = false;

    /**
     * ✅ 3. (สำคัญมาก) บอก Laravel ว่าชนิดข้อมูลของ Primary Key คืออะไร
     * เพื่อให้การเปรียบเทียบและการค้นหาค่าทำได้ถูกต้อง
     */
    protected $keyType = 'int'; // หรือ 'string' หาก cafe_id ของคุณเป็นข้อความ

    /**
     * ✅ 4. (สำคัญมาก) บอก Laravel ว่าเมื่อมีการทำ Route Model Binding (เช่นใน Controller ที่รับ Cafe $cafe)
     * ให้ใช้คอลัมน์ 'cafe_id' ในการค้นหาข้อมูลจาก URL โดยอัตโนมัติ
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'cafe_id';
    }

    // ส่วนของ Fillable และ Casts ของคุณถูกต้องดีอยู่แล้ว
    protected $fillable = [
        'user_id', 'admin_id', 'cafe_name', 'place_name', 'address', 'lat', 'lng', 
        'price_range', 'phone', 'email', 'website', 'facebook_page', 'instagram_page', 
        'line_id', 'open_day', 'close_day', 'open_time', 'close_time', 'is_new_opening', 
        'payment_methods', 'facilities', 'other_services', 'cafe_styles', 'other_style', 
        'images', 'parking', 'credit_card', 'status',
    ];

    protected $casts = [
        'is_new_opening'    => 'boolean',
        'payment_methods'   => 'array',
        'facilities'        => 'array',
        'other_services'    => 'array',
        'cafe_styles'       => 'array',
        'images'            => 'array',
        // 'open_time'         => 'datetime:H:i', // แนะนำให้ใช้ 'string' หรือไม่ cast เลยถ้าเก็บเป็น TIME
        // 'close_time'        => 'datetime:H:i',
        'parking'           => 'boolean',
        'credit_card'       => 'boolean',
        
    ];

    // --- Relationships ---

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviews()
    {
        // เมื่อกำหนด primaryKey ถูกต้องแล้ว Laravel จะรู้เองว่าต้องใช้ 'cafe_id'
        return $this->hasMany(Review::class, 'cafe_id', 'cafe_id');
    }

    /**
     * ความสัมพันธ์ Many-to-Many กับ User (สำหรับคนที่กดไลค์)
     */
    public function likers()
{
    return $this->belongsToMany(User::class, 'cafe_likes', 'cafe_id', 'user_id')
                ->withTimestamps(); // ✅✅✅ เพิ่มบรรทัดนี้เข้าไป
}

    public function admin()
    {
         return $this->belongsTo(AdminID::class, 'admin_id', 'admin_id');
    }
}