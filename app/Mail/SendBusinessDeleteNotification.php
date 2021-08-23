<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendBusinessDeleteNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $ownerName;
    public $businessName;
    public $author;
    public $string;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        string $ownerName,
        string $businessName,
        string $author,
        string $string
    ) {
        $this->ownerName = $ownerName;
        $this->businessName = $businessName;
        $this->author = $author;
        $this->string = $string;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.businesses.delete')
            ->subject('Business was deleted');
    }
}
