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
        $campaign = Campaign::where("uuid", "c7439b13-cabe-4daf-8f21-1f1b2980edd6")->first();
        $organization = $campaign->organization;
        $partners = $campaign->campaignPartners()->pluck("organization_id")->toArray();
        return [
            'uuid' => fake()->uuid(),
            'payload' => [
                "first_name" => fake()->firstName(),
                "last_name" => fake()->lastName(),
                "email" => fake()->unique()->safeEmail(),
                "postal_code" => fake()->postcode(),
            ],
            'is_verified' => fake()->boolean(),
            'signed_at' => fake()->dateTime(),
            // Get the dev campaign and organization from the database, or create them if they don't exist
            'campaign_id' => Campaign::where("uuid", "c7439b13-cabe-4daf-8f21-1f1b2980edd6")->first()->id,
            'organization_id' => Organization::whereIn("id", array_merge([$campaign->organization_id], $partners))->inRandomOrder()->first()->id,
        ];
    }
}
