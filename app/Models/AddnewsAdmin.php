<?php

namespace App\Models;

// แก้ไข Syntax Error ตรงนี้
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddnewsAdmin extends Model
{
    use HasFactory;  

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'addnews_admins';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'addnews_admin_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'link_url',
        'start_datetime',
        'end_datetime',
        'images',
        'is_visible',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'images' => 'array',
        'is_visible' => 'boolean',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];
}