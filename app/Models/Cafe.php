<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cafe extends Model
{
    use HasFactory;

    protected $table = 'cafes';
    protected $primaryKey = 'cafe_id';   // << สำคัญที่สุด
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
        // ถ้าใน DB เป็น TIME ให้ใช้เป็น 'string' หรือเก็บเป็น datetime จริง ๆ
        'open_time'    => 'datetime:H:i',
        'close_time'   => 'datetime:H:i',
        'parking'         => 'boolean',
        'credit_card'     => 'boolean',
    ];

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
}
