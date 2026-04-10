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
        $email = fake()->unique()->safeEmail();
        return [
            'uuid' => fake()->uuid(),
            'payload' => [
                "first_name" => fake()->firstName(),
                "last_name" => fake()->lastName(),
                "email" => $email,
                "postal_code" => fake()->postcode(),
                "pledged_amount" => fake()->randomFloat(2, 0, 500), // Example of a numeric field for goal tracking
            ],
            'unique_identifier' => $email,
            'verified_at' => fake()->boolean(75) ? now() : null,
            'signed_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'campaign_id' => Campaign::where("uuid", "c7439b13-cabe-4daf-8f21-1f1b2980edd6")->first()->id,
            'organization_id' => Organization::whereIn("id", array_merge([$campaign->organization_id], $partners))->inRandomOrder()->first()->id,
        ];
    }
}
