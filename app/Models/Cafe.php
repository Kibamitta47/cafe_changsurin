<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\AdminID;
use App\Models\Review;

class Cafe extends Model
{
    use HasFactory;

    protected $primaryKey = 'cafe_id';
    
    // ... ส่วนของ $fillable และ $casts ไม่ต้องเปลี่ยนแปลง ...
    protected $fillable = [
        'user_id',
        'admin_id',
        'cafe_name',
        'place_name',
        'address',
        'lat',
        'lng',
        'price_range',
        'phone',
        'email',
        'website',
        'facebook_page',
        'instagram_page',
        'line_id',
        'open_day',
        'close_day',
        'open_time',
        'close_time',
        'is_new_opening',
        'payment_methods',
        'facilities',
        'other_services',
        'cafe_styles',
        'other_style',
        'images',
        'parking',
        'credit_card',
        'status',
    ];

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

    public function user()
    {
        //              (Model แม่,  Foreign Key ในตาราง cafes,  Owner Key (PK) ในตาราง users)
        return $this->belongsTo(User::class, 'user_id',             'user_id');
    }

    public function admin()
    {
        // <-- แก้ไขตรงนี้: ระบุ Owner Key (PK ของตารางแม่) ให้ถูกต้อง
        return $this->belongsTo(AdminID::class, 'admin_id', 'admin_id_pk');
    }

    public function reviews()
    {
        // <-- แก้ไขตรงนี้: ระบุ Foreign Key และ Local Key ให้ชัดเจน
        return $this->hasMany(Review::class, 'cafe_id', 'cafe_id');
    }

    public function likers()
    {
        return $this->belongsToMany(User::class, 'cafe_likes', 'cafe_id', 'user_id')->withTimestamps();
    }

    /**
     * ฟังก์ชันเสริมสำหรับตรวจสอบว่าผู้ใช้ที่ล็อกอินอยู่ได้ไลค์คาเฟ่นี้หรือยัง
     * (มีประโยชน์สำหรับหน้าอื่นๆ)
     */
    public function isLikedByCurrentUser(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        return $this->likers()->where('user_id', auth()->id())->exists();
    }
}
