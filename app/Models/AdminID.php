<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminID extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard      = 'admin';
    protected $table      = 'admin_id';
    protected $primaryKey = 'admin_id';
    public $timestamps    = true; // ถ้าตารางไม่มี created_at/updated_at ให้เปลี่ยนเป็น false

    protected $fillable = [
        'UserName',
        'Email',
        'password',
        'profile_image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ให้ Laravel ใช้ชื่อคีย์ตาม $primaryKey เสมอ
    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }
}
