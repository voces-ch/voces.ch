<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Integration;
use App\Models\Signature;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use MailchimpMarketing\ApiClient;

class ProcessSignatureIntegrations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Signature $signature;
    public Campaign $campaign;
    /**
     * Create a new job instance.
     */
    public function __construct(Signature $signature, Campaign $campaign)
    {
        $this->signature = $signature;
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $integrations = Integration::where('campaign_id', $this->campaign->id)
            ->where('organization_id', $this->signature->organization_id)
            ->where('is_active', true)
            ->get();

        /** @var \App\Models\Integration $integration */
        foreach ($integrations as $integration) {
            // For now, we only have webhook integrations, but this is where we'd branch based on type
            if ($integration->type === 'webhook') {
                $this->processWebhookIntegration($integration);
            } else if ($integration->type === 'mailchimp') {
                $this->processMailchimpIntegration($integration);
            } else {
                Log::warning("Integration type {$integration->type} is not supported. Skipping.");
            }
        }
    }

    protected function processWebhookIntegration(Integration $integration): void
    {
        $url = $integration->settings['endpoint_url'] ?? null;
        $token = $integration->settings['secret_token'] ?? null;

        if (!$url) {
            Log::warning("Integration {$integration->id} is missing an endpoint URL. Skipping.");
            return;
        }

        $request = Http::timeout(10);

        if ($token) {
            $request->withHeaders([
                'X-Voces-Signature' => $token,
            ]);
        }

        $response = $request->post($url, [
            'event' => 'signature.created',
            'signature_uuid' => $this->signature->uuid,
            'campaign_id' => $this->signature->campaign_id,
            'signed_at' => $this->signature->signed_at,
            'origin' => $this->signature->origin ?? '',
            'organization' => $this->signature->organization?->name,
            'fields' => $this->signature->payload,
        ]);
        if ($response->successful()) {
            Log::info("Successfully sent signature {$this->signature->id} to integration {$integration->id}.");
        } else {
            Log::error("Failed to send signature {$this->signature->id} to integration {$integration->id}. Response: {$response->status()} - {$response->body()}");
        }
    }

    protected function processMailchimpIntegration(Integration $integration): void
    {
        // Note: Make sure the keys here match exactly what you named them in Filament!
        $apiKey = $integration->settings['api_key'] ?? null;
        $listId = $integration->settings['list_id'] ?? null;
        $mergeFieldMappings = $integration->settings['field_mappings'] ?? [];
        $optInFieldKey = $integration->settings['opt_in_field'] ?? null;

        if (!$apiKey || !$listId) {
            Log::warning("Mailchimp integration {$integration->id} is missing API key or List ID. Skipping.");
            return;
        }

        if ($optInFieldKey) {
            $optInValue = $this->signature->payload[$optInFieldKey] ?? false;
            $hasOptedIn = filter_var($optInValue, FILTER_VALIDATE_BOOLEAN);

            if (!$hasOptedIn) {
                Log::info("Mailchimp integration {$integration->id} skipped: User did not opt-in.", [
                    'signature_id' => $this->signature->id
                ]);
                return;
            }
        }

        $server = substr($apiKey, strpos($apiKey, '-') + 1);
        if (!$server) {
            Log::warning("Mailchimp integration {$integration->id} has an invalid API key format. Skipping.");
            return;
        }

        $mailchimp = new ApiClient();
        $mailchimp->setConfig([
            'apiKey' => $apiKey,
            'server' => $server,
        ]);

        $emailAddress = null;
        $mailchimpMergeFields = [];

        foreach ($mergeFieldMappings as $vocesKey => $mailchimpTag) {
            $fieldValue = $this->signature->payload[$vocesKey] ?? '';
            Log::debug("Mapping field '{$vocesKey}' to Mailchimp tag '{$mailchimpTag}' with value '{$fieldValue}'");
            if ($mailchimpTag === 'EMAIL') {
                $emailAddress = strtolower(trim($fieldValue));
            } else {
                $mailchimpMergeFields[$mailchimpTag] = $fieldValue;
            }
        }

        if (empty($emailAddress)) {
            Log::warning("Mailchimp integration {$integration->id} failed: No EMAIL address found in payload.");
            Log::debug('Payload data', $this->signature->payload);
            return;
        }

        $subscriberHash = md5($emailAddress);

        try {
            $mailchimp->lists->setListMember($listId, $subscriberHash, [
                'email_address' => $emailAddress,
                'status_if_new' => 'subscribed',
                'merge_fields'  => (object) $mailchimpMergeFields,
            ]);

            Log::info("Mailchimp integration {$integration->id} successful for signature {$this->signature->id}");

        } catch (\Exception $e) {
            Log::error("Mailchimp integration {$integration->id} failed.", [
                'error' => $e->getMessage(),
                'signature_id' => $this->signature->id
            ]);
        }
    }
}
