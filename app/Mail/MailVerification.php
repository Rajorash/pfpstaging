<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class MailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $author, $generatedPassword;
    public $verifyUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, User $author, string $generatedPassword)
    {
        $this->user = $user;
        $this->author = $author;
        $this->generatedPassword = $generatedPassword;
        $this->verifyUrl = URL::temporarySignedRoute(
            'verification.verify', Carbon::now()->addMinutes(60), [
                'id' => $this->user->getKey(),
                'hash' => sha1($this->user->getEmailForVerification())
            ]
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.users.verify-email');
    }
}
