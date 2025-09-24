<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SearchLogFactory extends Factory
{
    public function definition(): array
    {
        $q = $this->faker->words(rand(1,3), true);
        return [
            'user_id'          => null,
            'query'            => $q,
            'normalized_query' => mb_strtolower(trim($q)),
            'has_results'      => $this->faker->boolean(80),
            'result_count'     => $this->faker->numberBetween(0, 20),
            'source'           => $this->faker->randomElement(['web','line','api']),
            'ip'               => $this->faker->ipv4(),
            'user_agent'       => $this->faker->userAgent(),
            'created_at'       => now()->subDays(rand(0,30))->setTime(rand(0,23), rand(0,59)),
            'updated_at'       => now(),
        ];
    }
}
