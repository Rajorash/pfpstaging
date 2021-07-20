<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LicenseForAdvisorChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user, $author, $licensesCounter;
    public $assignedLicenses;
    public $availableLicenses;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, User $author, int $licensesCounter)
    {
        $this->user = $user;
        $this->author = $author;
        $this->licensesCounter = $licensesCounter;
        $this->assignedLicenses = count($user->licenses);
        $this->availableLicenses = $this->licensesCounter - $this->assignedLicenses;
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
