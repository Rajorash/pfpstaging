<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendBusinessCollaborateRevokeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $collaboratorName;
    public $businessName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        string $collaboratorName,
        string $businessName
    ) {
        $this->collaboratorName = $collaboratorName;
        $this->businessName = $businessName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.businesses.collaborator-revoke')
            ->subject('Collaboration revoked');
    }
}
