<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class SignatureFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'payload' => '{}',
            'is_verified' => fake()->boolean(),
            'signed_at' => fake()->dateTime(),
            'campaign_id' => Campaign::factory(),
            'organization_id' => Organization::factory(),
        ];
    }
}
