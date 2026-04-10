<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\SignatureResource;
use App\Jobs\ProcessSignatureIntegrations;
use App\Mail\VerifySignature;
use App\Models\Campaign;
use GrantHolle\Altcha\Rules\ValidAltcha;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Mail;

class CampaignController extends Controller
{
    public function show(Request $request, Campaign $campaign)
    {
        $locale = $request->query('locale', 'de');
        app()->setLocale($locale);

        $source = $request->query('source');
        $campaignPartner = $campaign->campaignPartners()->where('source_slug', $source)->first();

        $isHost = is_null($campaignPartner);
        $partnerOrgId = $campaignPartner ? $campaignPartner->id : null;
        $campaign->load(['campaignFields' => function ($query) use ($partnerOrgId, $isHost) {
            $query->where(function ($q) use ($partnerOrgId, $isHost) {
                $q->whereNull('target_organization_ids')
                    ->orWhereJsonLength('target_organization_ids', 0)
                    ->orWhereJsonContains('target_organization_ids', $isHost ? "host" : $partnerOrgId);
            });
        }]);

        return new CampaignResource($campaign);
    }

    public function sign(Request $request, Campaign $campaign)
    {
        $request->validate([
            'payload.altcha' => ['required', new ValidAltcha],
        ], [
            'payload.altcha.required' => __("Please complete the anti-spam verification."),
            'payload.altcha.' . ValidAltcha::class => __('The anti-spam verification failed or expired. Please reload the page.'),
        ]);
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

        $signature = $campaign->signatures()->create([
            'organization_id' => $organizationId,
            'unique_identifier' => $identifierValue,
            'payload' => $validated['payload'],
            'origin' => $validated['origin'] ?? null,
            'is_verified' => false,
            'signed_at' => now(),
        ]);
        if ($campaign->is_email_verification_enabled && $campaign->email_verification_field && isset($validated['payload'][$campaign->email_verification_field])) {
            $signature->verification_token = bin2hex(random_bytes(32));
            $signature->token_expiration = now()->addHours(48);
            $signature->save();
            $to = $validated['payload']['email'] ?? null;
            Mail::to($to)
                ->locale($locale)
                ->send(new VerifySignature($signature));
        }

        ProcessSignatureIntegrations::dispatch($signature);

        return response()->json([
            'status' => 'success',
            'message' => 'Signature captured successfully.',
            'requires_verification' => $campaign->is_email_verification_enabled,
            'signature' => new SignatureResource($signature),
        ], 201);
    }
}
