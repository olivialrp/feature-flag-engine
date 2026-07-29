<?php

namespace App\Listeners;

use App\Events\FlagsEvaluated;
use App\Models\UsageLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class MeterUsage implements ShouldQueue
{
    public function handle(FlagsEvaluated $event): void
    {
        UsageLog::upsert(
            [
                [
                    'tenant_id' => $event->environment->project->tenant_id,
                    'environment_id' => $event->environment->id,
                    'date' => now()->toDateString(),
                    'request_count' => 1,
                ]
            ],
            ['environment_id', 'date'],
            ['request_count' => DB::raw('request_count + 1')]
        );
    }
}
