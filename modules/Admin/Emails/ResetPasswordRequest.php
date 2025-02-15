<?php

namespace Modules\Admin\Emails;

use App\Models\User;
use Modules\SystemCore\Entities\PasswordResetCode;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordRequest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The reset instance.
     *
     * @var \Modules\SystemCore\Entities\PasswordResetCode
     */
    public $reset;

     /**
     * The url string.
     *
     * @var string
     */
    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, PasswordResetCode $reset, ?string $url = null)
    {
        $this->user  = $user;
        
        $this->reset = $reset;

        $this->url   = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('admin::emails.reset_password_request')->with([
            'user'  => $this->user,
            'reset' => $this->reset,
            'url'   => $this->url,
        ]);
    }
}
