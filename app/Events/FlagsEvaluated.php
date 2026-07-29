<?php

namespace App\Events;

use App\Models\Environment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FlagsEvaluated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Environment $environment)
    {
    }
}
