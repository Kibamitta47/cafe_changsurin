<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminID extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * คุณสมบัติพื้นฐานของ Model
     */
    protected $guard = 'admin';
    protected $table = 'admin_id';
    protected $primaryKey = 'admin_id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'UserName',
        'Email',
        'password',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // =========================================================================
    // [จุดที่แก้ไขและปรับปรุงใหม่ทั้งหมด]
    // โค้ดชุดนี้จะทำให้ Laravel จัดการกับ Session ของ Admin ได้อย่างถูกต้อง 100%
    // =========================================================================

    /**
     * Override parent method to get the name of the unique identifier for the user.
     * ฟังก์ชันนี้ต้องคืนค่า "ชื่อของคอลัมน์ Primary Key" เสมอ
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->getKeyName(); // ใช้วิธีนี้จะปลอดภัยที่สุด เพราะจะดึงค่าจาก $primaryKey มาโดยตรง
    }

    /**
     * Override parent method to get the unique identifier for the user.
     * ฟังก์ชันนี้ต้องคืนค่า "ค่าของ Primary Key" ของแถวข้อมูลนั้นๆ
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }
}