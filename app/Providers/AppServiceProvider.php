<?php

namespace App\Providers;

use App\Events\FlagsEvaluated;
use App\Listeners\MeterUsage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Event::listen(
            FlagsEvaluated::class,
            MeterUsage::class,
        );
    }
}
