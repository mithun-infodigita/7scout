<?php

namespace App\Events\Import;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportCompletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $import;

    public function __construct($import)
    {
        $this->import = $import;
    }
}
