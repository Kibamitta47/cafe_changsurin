<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddnewsAdmin extends Model
{
    use HasFactory;

    protected $table = 'Addnews_admin';

    protected $fillable = [
        'title',
        'content',
        'images',
    ];
}
