<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cafe;
use App\Models\User;

class Review extends Model
{
    use HasFactory;

    protected $primaryKey = 'review_id';

    protected $fillable = [
        'cafe_id',
        'user_id',
        'user_name',
        'title',
        'content',
        'rating',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

  /**
     * ความสัมพันธ์: Review หนึ่งอันเป็นของ Cafe หนึ่งร้าน
     */
    public function cafe()
    {
        return $this->belongsTo(Cafe::class, 'cafe_id', 'cafe_id');
    }

    public function user()
    {
        //              (Model แม่,  Foreign Key ในตาราง reviews,  Owner Key (PK) ในตาราง users)
        return $this->belongsTo(User::class, 'user_id',               'user_id');
    }

    
}
