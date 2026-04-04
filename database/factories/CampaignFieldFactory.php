<?php

namespace Database\Factories;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'label' => fake()->word(),
            'type' => fake()->word(),
            'is_required' => fake()->boolean(),
            'order' => fake()->numberBetween(-10000, 10000),
            'campaign_id' => Campaign::factory(),
        ];
    }
}
