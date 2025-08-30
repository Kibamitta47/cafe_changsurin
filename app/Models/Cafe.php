<?php
// app/Models/Cafe.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cafe extends Model
{
    use HasFactory;

    protected $primaryKey = 'cafe_id';
    public $incrementing = true;       // <-- ใช้ auto-increment
    protected $keyType = 'int';

    public function getRouteKeyName()
    {
        return 'cafe_id';
    }

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
        'parking'         => 'boolean',
        'credit_card'     => 'boolean',
    ];

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
        return $this->belongsToMany(User::class, 'cafe_likes', 'cafe_id', 'user_id')
            ->withTimestamps()
            ->withPivot('cafe_like_id');
    }

    public function admin()
    {
        return $this->belongsTo(AdminID::class, 'admin_id', 'admin_id');
    }
}
