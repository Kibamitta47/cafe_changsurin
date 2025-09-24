<?php

namespace Database\Factories;

use App\Models\SearchLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class SearchLogFactory extends Factory
{
    protected $model = SearchLog::class;

    public function definition(): array
    {
        $q   = fake()->randomElement(['กาแฟ', 'ชาเขียว', 'ลาเต้', 'วิวเขา', 'นั่งทำงาน', 'เงียบ']);
        $cnt = fake()->numberBetween(0, 20);
        return [
            'user_id'          => null, // หรือสุ่ม user ถ้ามี
            'query'            => $q,
            'normalized_query' => mb_strtolower(trim($q)),
            'filters'          => ['rating' => fake()->numberBetween(0,5)],
            'results_count'    => $cnt,
            'has_results'      => $cnt > 0,
            'ip'               => fake()->ipv4(),
            'user_agent'       => fake()->userAgent(),
            'created_at'       => now()->subDays(fake()->numberBetween(0, 30))->setTime(fake()->numberBetween(8,22), 0),
            'updated_at'       => now(),
        ];
    }
}
