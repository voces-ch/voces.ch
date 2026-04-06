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
    public function show(Request $request, Campaign $campaign)
    {
        $locale = $request->query('locale', 'de');
        app()->setLocale($locale);
        $campaign->load('campaignFields');
        return new CampaignResource($campaign);
    }

    public function sign(Request $request, Campaign $campaign)
    {
        $locale = $request->query('locale', 'de');
        app()->setLocale($locale);
        if (!$campaign->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => __('validation.campaign_inactive'),
            ], 400);
        }
        $campaign->loadMissing(['campaignFields', 'campaignPartners']);

        $organizationId = $campaign->organization_id;
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
            'origin' => ['nullable', 'string', 'max:255'],
            'payload' => ['required', 'array'],
        ];
        $customAttributes = [];

        $uniqueFieldKey = null;

        foreach ($campaign->campaignFields as $field) {
            $fieldRules = $field->is_required ? ['required'] : ['nullable'];

            if ($field->is_unique) {
                $uniqueFieldKey = $field->name;
                $fieldRules[] = Rule::unique('signatures', 'unique_identifier')
                    ->where(function ($query) use ($campaign, $organizationId) {
                        return $query->where('campaign_id', $campaign->id)
                                     ->where('organization_id', $organizationId);
                    });
            }

            if ($field->type === 'email') {
                $fieldRules[] = 'email';
            }

            $rules["payload.{$field->name}"] = $fieldRules;
            $customAttributes["payload.{$field->name}"] = $field->label;
        }

        $validated = $request->validate($rules, [], $customAttributes);
        $validated['payload']['language'] = $locale;
        $identifierValue = $validated['payload'][$uniqueFieldKey] ?? null;

        $campaign->signatures()->create([
            'organization_id' => $organizationId,
            'unique_identifier' => $identifierValue,
            'payload' => $validated['payload'],
            'origin' => $validated['origin'] ?? null,
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
