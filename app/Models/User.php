<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Cafe;
use App\Models\Review;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id'; // <--- เพิ่มบรรทัดนี้

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'line_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    

    public function likedCafes()
{
    return $this->belongsToMany(Cafe::class, 'cafe_likes', 'user_id', 'cafe_id')
                ->withTimestamps(); // ✅✅✅ เพิ่มบรรทัดนี้เข้าไป
}

    /**
     * ความสัมพันธ์เพื่อดึงคาเฟ่ที่ User คนนี้เป็นเจ้าของ
     */
    public function cafes()
    {
        return $this->hasMany(Cafe::class, 'user_id');
    }
    /**
     * ความสัมพันธ์: User หนึ่งคนสามารถเขียนได้หลาย Review
     */
    public function reviews()
    {
        //              (Model ลูก,  Foreign Key ในตาราง reviews,  Local Key (PK) ในตาราง users)
        return $this->hasMany(Review::class, 'user_id',               'user_id');
    }
}
