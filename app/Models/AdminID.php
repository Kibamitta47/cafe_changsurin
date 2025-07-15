<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminID extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin_id';
    protected $primaryKey = 'AdminID';
    public $timestamps = true;
    public $incrementing = true;

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

    public function getAuthPassword()
    {
        return $this->password;
    }
}
