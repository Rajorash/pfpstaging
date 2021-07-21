<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdvisorChangedCountOfLicenses extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    public $user;
    public $author;
    public $licensesCounter;
    public $assignedLicenses;
    public $availableLicenses;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        User $user,
        User $author,
        int $licensesCounter,
        int $assignedLicenses,
        int $availableLicenses
    ) {
        $this->user = $user;
        $this->author = $author;
        $this->licensesCounter = $licensesCounter;
        $this->assignedLicenses = $assignedLicenses;
        $this->availableLicenses = $availableLicenses;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.licenses.change-count-for-advisor');
    }
}
