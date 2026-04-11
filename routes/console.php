<?php
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Schedule::call(function () {
    Log::info('Beep boop! The Docker cron scheduler is alive.');
})->everyMinute();
