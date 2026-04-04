<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'slug' => fake()->slug(),
            'uuid' => fake()->uuid(),
            'description' => fake()->text(),
            'is_active' => fake()->boolean(),
            'organization_id' => Organization::factory(),
        ];
    }
}
