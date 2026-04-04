<?php
use App\Http\Controllers\Api\V1\CampaignController;
use Illuminate\Support\Facades\Route;

Route::get('/campaigns/{campaign:uuid}', [CampaignController::class, 'show']);

Route::post('/campaigns/{campaign:uuid}/signatures', [CampaignController::class, 'sign']);
