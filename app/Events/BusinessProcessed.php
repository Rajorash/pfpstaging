<?php

namespace App\Events;

use App\Models\Business;
use App\Models\User;
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

    public $type;
    public $business;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $type, Business $business, User $user)
    {
        $this->type = $type;
        $this->business = $business;
        $this->user = $user;
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
