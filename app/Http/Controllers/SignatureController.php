<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Signature;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    public function verify(Request $request, Signature $signature, $token)
    {
        app()->setLocale($signature->payload["language"] ?? 'de');
        if ($signature->verification_token !== $token) {
            return response()->json([
                'status' => 'error',
                'message' => __('Invalid verification token.'),
            ], 400);
        }
        if ($signature->token_expiration && now()->greaterThan($signature->token_expiration)) {
            return response()->json([
                'status' => 'error',
                'message' => __('Verification token has expired.'),
            ], 400);
        }

        $signature->verified_at = now();
        $signature->save();

        // Dispatch any post-verification actions here (e.g., send thank you email, trigger integrations)

        $redirectUrl = $signature->campaign->verification_success_url ?? "/thank-you/{$signature->campaign->id}/{$signature->uuid}";
        return redirect()->to($redirectUrl);
    }

    public function thankYou(Request $request, Campaign $campaign, Signature $signature)
    {
        if ($signature->campaign_id != $campaign->id) {
            abort(404);
        }
        app()->setLocale($signature->payload["language"] ?? 'de');
        return view('thank-you', [
            'signature' => $signature,
            'campaign' => $campaign,
        ]);
    }
}
