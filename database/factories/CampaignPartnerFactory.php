<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignPartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'source_slug' => fake()->word(),
            'campaign_id' => Campaign::factory(),
            'organization_id' => Organization::factory(),
        ];
    }
}
