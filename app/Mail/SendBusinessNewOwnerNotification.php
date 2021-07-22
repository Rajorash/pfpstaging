<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendBusinessNewOwnerNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $ownerName;
    public $businessName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        string $ownerName,
        string $businessName
    ) {
        $this->ownerName = $ownerName;
        $this->businessName = $businessName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.businesses.new-owner')
            ->subject('New Business for you');
    }
}
