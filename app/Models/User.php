<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Cafe;
use App\Models\Review;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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

    public function cafes()
    {
        return $this->hasMany(Cafe::class);
    }

    public function likedCafes()
    {
        return $this->belongsToMany(Cafe::class, 'cafe_likes', 'user_id', 'cafe_id')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
