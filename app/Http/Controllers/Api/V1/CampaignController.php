<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\SignatureResource;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{
    public function show(Campaign $campaign)
    {
        $campaign->load('campaignFields');
        return new CampaignResource($campaign);
    }

    public function sign(Request $request, Campaign $campaign)
    {
        $campaign->loadMissing(['campaignFields', 'campaignPartners']);

        $organizationId = $campaign->organization_id; // Default to Host
        $source = $request->input('source');

        if (!empty($source)) {
            $partner = $campaign->campaignPartners
                ->where('source_slug', $source)
                ->first();

            if ($partner) {
                $organizationId = $partner->organization_id;
            }
        }

        $rules = [
            'source' => ['nullable', 'string'],
            'payload' => ['required', 'array'],
            'payload.email' => [
                'required',
                'email',
                // Uniqueness now scoped to BOTH the campaign and the specific organization
                Rule::unique('signatures', 'email')->where(function ($query) use ($campaign, $organizationId) {
                    return $query->where('campaign_id', $campaign->id)
                                 ->where('organization_id', $organizationId);
                }),
            ],
        ];

        $customAttributes = [];

        foreach ($campaign->campaignFields as $field) {
            $fieldRules = $field->is_required ? ['required'] : ['nullable'];

            $rules["payload.{$field->name}"] = $fieldRules;
            $customAttributes["payload.{$field->name}"] = $field->label;
        }

        $validated = $request->validate($rules, [], $customAttributes);

        $email = $validated['payload']['email'];

        unset($validated['payload']['email']);

        $campaign->signatures()->create([
            'organization_id' => $organizationId,
            'email' => $email,
            'payload' => $validated['payload'],
            'is_verified' => false,
            'signed_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Signature captured successfully.',
            'requires_verification' => true,
            'signature' => new SignatureResource($campaign->signatures()->latest()->first()),
        ], 201);
    }
}
