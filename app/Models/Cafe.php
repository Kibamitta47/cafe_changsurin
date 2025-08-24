<?php

namespace App\Models;

use Illuminate from\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// ตรวจสอบให้แน่ใจว่าได้ use Model ที่จำเป็นครบถ้วน
use App\Models\User;
use App\Models\AdminID;
use App\Models\Review;

class Cafe extends Model
{
    use HasFactory;

    // ลบบรรทัดนี้ทิ้ง หรือคอมเมนต์ออกไป เพราะ PK ของคุณคือ 'id' ซึ่งเป็นค่าเริ่มต้นอยู่แล้ว
    // protected $primaryKey = 'cafe_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id', 'admin_id', 'cafe_name', 'place_name', 'address', 'lat', 'lng', 
        'price_range', 'phone', 'email', 'website', 'facebook_page', 'instagram_page', 
        'line_id', 'open_day', 'close_day', 'open_time', 'close_time', 'is_new_opening', 
        'payment_methods', 'facilities', 'other_services', 'cafe_styles', 'other_style', 
        'images', 'parking', 'credit_card', 'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_new_opening'    => 'boolean',
        'payment_methods'   => 'array',
        'facilities'        => 'array',
        'other_services'    => 'array',
        'cafe_styles'       => 'array',
        'images'            => 'array',
        'open_time'         => 'datetime:H:i',
        'close_time'        => 'datetime:H:i',
        'parking'           => 'boolean',
        'credit_card'       => 'boolean',
    ];

    /**
     * Get the user that owns the cafe.
     */
    public function user()
    {
        // Foreign Key 'user_id' ของ cafes เชื่อมกับ PK 'id' ของ users
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the reviews for the cafe.
     * นี่คือส่วนที่แก้ไขให้ถูกต้อง 100%
     */
    public function reviews()
    {
        // Cafe หนึ่งมีหลาย Review
        // โดยใช้ Foreign Key 'cafe_id' ในตาราง reviews
        // มาเชื่อมกับ Local Key (PK) 'id' ของตาราง cafes (ตารางปัจจุบัน)
        return $this->hasMany(Review::class, 'cafe_id', 'id');
    }

    /**
     * The users that have liked this cafe.
     * นี่คือส่วนที่แก้ไขให้ถูกต้อง 100%
     */
    public function likers()
    {
        // ตารางกลางคือ 'cafe_likes'
        // Foreign Key ในตารางกลางที่เชื่อมกับ Cafe (ตัวนี้) คือ 'cafe_id'
        // Foreign Key ในตารางกลางที่เชื่อมกับ User (ตัวอื่น) คือ 'user_id'
        // แต่ Local Key (PK) ของ Cafe (ตัวนี้) คือ 'id'
        return $this->belongsToMany(User::class, 'cafe_likes', 'cafe_id', 'user_id')->withTimestamps();
    }
    
    // ... ฟังก์ชันที่เหลือของคุณถูกต้องแล้ว ...
    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'cafe_likes', 'cafe_id', 'user_id');
    }
    
    public function isLikedBy(User $user): bool
    {
        return $this->likers()->where('user_id', $user->getKey())->exists();
    }
    
    // ... ฟังก์ชัน admin() ...
    public function admin()
    {
        // สมมติว่า PK ของตาราง admin_id คือ 'AdminID'
        return $this->belongsTo(AdminID::class, 'admin_id', 'AdminID');
    }
}