<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddnewsAdmin extends Model
{
    use HasFactory;

    protected $table = 'addnews_admins';
    protected $primaryKey = 'addnews_admin_id';

    protected $fillable = [
        'title',
        'content',
        'link_url',
        'start_datetime',
        'end_datetime',
        'images',
        'is_visible',
    ];

    protected $casts = [
        'images' => 'array',
        'is_visible' => 'boolean',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];
}
