<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignPageResource;
use App\Models\CampaignPage;
use Illuminate\Http\Request;

class CampaignPageController extends Controller
{
    public function show(CampaignPage $campaignPage)
    {
        if (! $campaignPage->is_published) {
            abort(404);
        }
        app()->setLocale($campaignPage->locale);
        $campaignPage->load('campaign');
        $campaignPage->campaign->load('organization');
        return new CampaignPageResource($campaignPage);
    }
}
