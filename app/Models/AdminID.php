<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminID extends Authenticatable
{
    protected $table = 'admin_id';
    protected $primaryKey = 'AdminID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'UserName',
        'Email',
        'Password',
        'profile_image',
    ];
}
