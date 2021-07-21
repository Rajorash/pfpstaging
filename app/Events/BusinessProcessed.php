<?php

namespace App\Events;

use App\Models\Business;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusinessProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $business;
    public $type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $type, Business $business)
    {
        $this->type = $type;
        $this->business = $business;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
