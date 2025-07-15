<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cafe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id', // ต้องมี admin_id อยู่ใน fillable
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

    /**
     * ความสัมพันธ์กับ User Model (สำหรับผู้ใช้ทั่วไป)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * ความสัมพันธ์กับ AdminID Model (สำหรับผู้ดูแลระบบ)
     */
    public function admin(): BelongsTo
    {
        // ชี้ไปที่ AdminID Model และระบุ foreign key (admin_id ในตาราง cafes)
        // และ local key (AdminID ในตาราง admin_id)
        return $this->belongsTo(AdminID::class, 'admin_id', 'AdminID'); 
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function likers()
    {
        return $this->belongsToMany(User::class, 'cafe_likes', 'cafe_id', 'user_id')->withTimestamps();
    }

    public function isLikedBy(\App\Models\User $user)
    {
        return $this->likers()->where('user_id', $user->id)->exists();
    }
}