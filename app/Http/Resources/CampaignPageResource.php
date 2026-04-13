<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'locale' => $this->locale,
            'is_published' => $this->is_published,
            'theme' => $this->theme,
            'heroine_image_url' => $this->getFirstMediaUrl('heroine_image'),
            'content' => $this->content,
            'campaign' => [
                'uuid' => $this->campaign->uuid,
                'title' => $this->campaign->title,
                'description' => $this->campaign->description,
            ],
            'organization' => [
                'uuid' => $this->campaign->organization->uuid,
                'name' => $this->campaign->organization->name,
                'logo_url' => $this->campaign->organization->getFirstMediaUrl('logo'),
            ],
        ];
    }
}
