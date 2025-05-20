<?php

namespace Modules\ContentTypes\Emails;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransportarInvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var $user
     */
    public $user;

    /**
     * The hash instance.
     *
     * @var $hash
     */
    public $hash;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $hash)
    {
        $this->user = $user;

        $this->hash = $hash;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('contenttypes::emails.transportar_invitation')->with([
            'user' => $this->user,
            'hash' => $this->hash,
        ])
        ->subject('You have been invited to join ' . config('mail_theme.appName'));
    }
}
