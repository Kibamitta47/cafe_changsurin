<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = ['name','email','password','profile_image','line_id'];

    protected $hidden = ['password','remember_token'];

    protected $casts = ['email_verified_at' => 'datetime','password' => 'hashed'];

    
   public function likedCafes()
{
    return $this->belongsToMany(
        Cafe::class,   // โมเดลที่เกี่ยวข้อง
        'cafe_likes',  // ชื่อ pivot table
        'user_id',     // FK ของฝั่ง User ใน pivot
        'cafe_id',     // FK ของฝั่ง Cafe ใน pivot
        'user_id',     // PK ของตาราง users
        'cafe_id'      // PK ของตาราง cafes
    )->withTimestamps()
     ->withPivot('cafe_like_id');
}

    public function cafes()
    {
        return $this->hasMany(Cafe::class, 'user_id', 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'user_id');
    }
}
