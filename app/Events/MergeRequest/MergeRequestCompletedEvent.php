<?php

namespace App\Events\MergeRequest;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MergeRequestCompletedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mergeRequest;

    public function __construct($mergeRequest)
    {
        $this->mergeRequest = $mergeRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['mergeRequests'];
    }

    public function broadcastAs()
    {
        return 'mergeRequest:completed';
    }

    public function broadcastWith()
    {
        return ['mergeRequestName' => $this->mergeRequest->name];
    }
}
