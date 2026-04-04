<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'total_signatures' => $this->totalSignatures(),
            // 'goal' => $this->signature_goal, // TODO: Allow organizations to set a signature goal for each campaign and return it here

            'fields' => $this->campaignFields->map(function ($field) {
                return [
                    'name' => $field->name,
                    'label' => $field->label,
                    'type' => $field->type ?? 'text',
                    'is_required' => (bool) $field->is_required,
                    'order' => $field->order,
                ];
            })->sortBy('order')->values(), // Ensure they are sorted properly!
        ];
    }
}
