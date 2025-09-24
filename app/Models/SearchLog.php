<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;

    protected $table = 'search_logs';

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
        'has_results'   => 'boolean',
        'result_count'  => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
