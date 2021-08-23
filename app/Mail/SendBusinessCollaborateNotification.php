<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendBusinessCollaborateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $collaboratorName;
    public $businessName;
    public $title;
    public $text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        string $collaboratorName,
        string $businessName,
        string $title,
        string $text
    ) {
        $this->collaboratorName = $collaboratorName;
        $this->businessName = $businessName;
        $this->title = $title;
        $this->text = $text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.businesses.collaborator')
            ->subject($this->title);
    }
}
