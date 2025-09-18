<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cafe extends Model
{
    use HasFactory;

    protected $table = 'cafes';
    protected $primaryKey = 'cafe_id';   // PK จริงในตาราง
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
        'images'          => 'array',
        // ถ้าใน DB เป็น TIME แนะนำเก็บเป็น string จะปลอดภัยกว่า
        'open_time'       => 'string',
        'close_time'      => 'string',
        'parking'         => 'boolean',
        'credit_card'     => 'boolean',
    ];

    /* ---------------- Relations ---------------- */

    public function user()
    {
        // FK ใน cafes = user_id, PK ใน users = user_id
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function reviews()
    {
        // FK ใน reviews = cafe_id, PK ใน cafes = cafe_id
        return $this->hasMany(Review::class, 'cafe_id', 'cafe_id');
    }

    public function likers()
    {
        // pivot: cafe_likes(cafe_id, user_id)
        return $this->belongsToMany(
            User::class,
            'cafe_likes',
            'cafe_id',   // FK ของฝั่ง Cafe ใน pivot
            'user_id',   // FK ของฝั่ง User ใน pivot
            'cafe_id',   // local key ของตาราง cafes
            'user_id'    // local key ของตาราง users
        )->withTimestamps()->withPivot('cafe_like_id');
    }

    public function admin()
    {
        // ถ้าในตาราง admin ใช้ PK = admin_id
        return $this->belongsTo(AdminID::class, 'admin_id', 'admin_id');
    }

    /* ---------------- Accessors ---------------- */

    /**
     * คืน URL รูปภาพแรกของคาเฟ่แบบทนทาน:
     * - ถ้า images เป็น array: ใช้สมาชิกตัวแรก
     * - รองรับทั้ง URL เต็ม, public/images, public/storage, uploads
     * - ถ้าไม่พบให้ใช้ placeholder
     */
    public function getImageUrlAttribute(): string
    {
        $p = null;

        // 1) ดึง path แรกจาก images (array) ถ้ามี
        if (is_array($this->images) && !empty($this->images)) {
            $first = reset($this->images);
            if (is_string($first) && $first !== '') {
                $p = $first;
            }
        }

        // 2) fallback: ถ้ามีคอลัมน์ image_path แยก (เผื่อมีในบางสคีมา)
        if (!$p && isset($this->attributes['image_path']) && $this->attributes['image_path'] !== '') {
            $p = $this->attributes['image_path'];
        }

        // 3) ไม่มีข้อมูลภาพเลย → placeholder
        if (!$p) {
            return asset('images/placeholder-cafe.jpg');
        }

        // 4) ถ้าเป็น URL เต็ม
        if (Str::startsWith($p, ['http://', 'https://', '//'])) {
            return $p;
        }

        // 5) ถ้าเป็น path ที่ขึ้นต้นด้วยโฟลเดอร์สาธารณะทั่วไป
        if (Str::startsWith($p, ['images/', 'storage/', 'uploads/'])) {
            return asset($p);
        }

        // 6) ลอง public/storage/<path>
        if (file_exists(public_path('storage/'.$p))) {
            return asset('storage/'.$p);
        }

        // 7) ลอง public/images/<path>
        if (file_exists(public_path('images/'.$p))) {
            return asset('images/'.$p);
        }

        // 8) สุดท้ายใช้ placeholder
        return asset('images/placeholder-cafe.jpg');
    }
}
