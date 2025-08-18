<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\AdminID;
use App\Models\Review;

class Cafe extends Model
{
    use HasFactory;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
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

    /**
     * ตรวจสอบว่าผู้ใช้กดไลก์คาเฟ่นี้หรือไม่
     *
     * @param User $user
     * @return bool
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likers()->where('user_id', $user->getKey())->exists();
    }
    
}
