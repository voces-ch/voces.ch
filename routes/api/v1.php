<?php
use App\Http\Controllers\Api\V1\CampaignController;
use App\Http\Controllers\Api\V1\CampaignPageController;
use Illuminate\Support\Facades\Route;

Route::prefix('campaigns')->group(function () {
    Route::get('{campaign:uuid}', [CampaignController::class, 'show']);
    Route::post('{campaign:uuid}/signatures', [CampaignController::class, 'sign']);
});

Route::prefix('campaign-pages')->group(function () {
    Route::get('{campaignPage:slug}', [CampaignPageController::class, 'show']);
});
