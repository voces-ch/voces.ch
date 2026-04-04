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
            'submit_label' => $this->submit_label,
            'signature_goal' => $this->signature_goal,
            'signature_count' => $this->totalSignatures(),
            'success_type' => $this->success_type,
            'success_message' => $this->success_message,
            'success_url' => $this->success_url,

            'fields' => $this->campaignFields->map(function ($field) {
                return [
                    'name' => $field->name,
                    'label' => $field->label,
                    'type' => $field->type ?? 'text',
                    'is_required' => (bool) $field->is_required,
                    'default_value' => $field->default_value,
                    'order' => $field->order,
                ];
            })->sortBy('order')->values(),
        ];
    }
}
