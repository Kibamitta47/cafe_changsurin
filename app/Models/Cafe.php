<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cafe extends Model
{
    use HasFactory;

    protected $table = 'cafes';
    protected $primaryKey = 'cafe_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id','admin_id','cafe_name','place_name','address','lat','lng',
        'price_range','phone','email','website','facebook_page','instagram_page',
        'line_id','open_day','close_day','open_time','close_time','is_new_opening',
        'payment_methods','facilities','other_services','cafe_styles','other_style',
        'images','parking','credit_card','status',
    ];

    protected $casts = [
        'is_new_opening'  => 'boolean',
        'payment_methods' => 'array',
        'facilities'      => 'array',
        'other_services'  => 'array',
        'cafe_styles'     => 'array',
        'images'          => 'array',   // JSON → array
        'open_time'       => 'string',
        'close_time'      => 'string',
        'parking'         => 'boolean',
        'credit_card'     => 'boolean',
    ];

    /* ---------------- Relations ---------------- */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'cafe_id', 'cafe_id');
    }

    public function likers()
    {
        return $this->belongsToMany(
            User::class, 'cafe_likes',
            'cafe_id', 'user_id',
            'cafe_id', 'user_id'
        )->withTimestamps()->withPivot('cafe_like_id');
    }

    public function admin()
    {
        return $this->belongsTo(AdminID::class, 'admin_id', 'admin_id');
    }

    /* ---------------- Accessors ---------------- */

    /**
     * คืน URL รูปแรกของคาเฟ่แบบทนทาน
     */
    public function getImageUrlAttribute(): string
    {
        // ⚠ อย่าใช้ reset($this->images) เพราะต้องอ้างอิง (by reference)
        // ใช้ตัวแปรชั่วคราวแทน
        $images = $this->images ?? [];

        // เผื่อ DB เก็บเป็นสตริง JSON
        if (is_string($images)) {
            $decoded = json_decode($images, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $images = $decoded;
            } elseif ($images !== '') {
                $images = [$images];
            } else {
                $images = [];
            }
        }

        // ดึงสมาชิกตัวแรกถ้ามี
        $p = null;
        if (is_array($images) && isset($images[0]) && is_string($images[0]) && $images[0] !== '') {
            $p = $images[0];
        }

        // fallback จากคอลัมน์ image_path (ถ้ามีในสคีมา)
        if (!$p && !empty($this->attributes['image_path'])) {
            $p = $this->attributes['image_path'];
        }

        // ไม่มีข้อมูล → placeholder
        if (!$p) {
            return asset('images/placeholder-cafe.jpg');
        }

        // เป็น URL ตรง
        if (Str::startsWith($p, ['http://', 'https://', '//'])) {
            return $p;
        }

        // เป็น path ใต้ public/
        if (Str::startsWith($p, ['images/', 'storage/', 'uploads/'])) {
            return asset($p);
        }

        // ลอง public/storage/<path>
        if (file_exists(public_path('storage/'.$p))) {
            return asset('storage/'.$p);
        }

        // ลอง public/images/<path>
        if (file_exists(public_path('images/'.$p))) {
            return asset('images/'.$p);
        }

        // สุดท้าย → placeholder
        return asset('images/placeholder-cafe.jpg');
    }
}
