<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'query',
        'normalized_query',
        'filters',
        'results_count',
        'has_results',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'filters' => 'array',
        'has_results' => 'boolean',
    ];
}
