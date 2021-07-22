<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendBusinessLicenseState extends Mailable
{
    use Queueable, SerializesModels;

    public $business;
    public $user;
    public $string;
    public $title;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        string $business,
        string $user,
        string $string,
        string $title
    ) {
        $this->business = $business;
        $this->user = $user;
        $this->string = $string;
        $this->title = $title;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.businesses.license-state')
            ->subject(str_replace('&quot;', '"', strip_tags($this->title)));
    }
}
