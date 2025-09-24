<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    protected $fillable = [
        'user_id',
        'query',
        'normalized_query',
        'has_results',
        'result_count',
        'source',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'has_results' => 'boolean',
        'result_count' => 'integer',
    ];
}
