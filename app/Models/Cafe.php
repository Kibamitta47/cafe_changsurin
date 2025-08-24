<?php

namespace App\Models;

// แก้ไข Syntax Error ตรงนี้
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// ตรวจสอบให้แน่ใจว่าได้ use Model ที่จำเป็นครบถ้วน
use App\Models\User;
use App\Models\AdminID;
use App\Models\Review;

class Cafe extends Model
{
    use HasFactory;

    // ไม่ต้องกำหนด $primaryKey เพราะค่าเริ่มต้น 'id' ถูกต้องอยู่แล้วตามฐานข้อมูล
    
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
     */
    public function reviews()
    {
        // Foreign Key 'cafe_id' ในตาราง reviews เชื่อมกับ PK 'id' ของตาราง cafes
        return $this->hasMany(Review::class, 'cafe_id', 'id');
    }

    /**
     * The users that have liked this cafe.
     */
    public function likers()
    {
        // FK ของ Cafe ในตารางกลางคือ 'cafe_id', PK ของ Cafe (ตัวนี้) คือ 'id'
        // FK ของ User ในตารางกลางคือ 'user_id', PK ของ User (ตัวอื่น) คือ 'id'
        return $this->belongsToMany(User::class, 'cafe_likes', 'cafe_id', 'user_id');
    }
    
    /**
     * Check if the cafe is liked by a specific user.
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likers()->where('user_id', $user->id)->exists();
    }
    
    /**
     * Get the admin that owns the cafe.
     */
    public function admin()
    {
        // สมมติว่า PK ของตาราง admin_id คือ 'AdminID'
        return $this->belongsTo(AdminID::class, 'admin_id', 'AdminID');
    }
}