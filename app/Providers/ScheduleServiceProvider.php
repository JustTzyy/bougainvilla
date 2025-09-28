<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Schedule guest cleanup to run daily at 2:00 AM
        Schedule::command('guests:cleanup')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground();
    }
}
