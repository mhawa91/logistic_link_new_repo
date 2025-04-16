<?php

namespace Modules\Admin\Emails;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfirmEmailAddress extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var $data
     */
    public $data;

    public $hash;

    // $data = [
    //     'full_name' => $Request->full_name,
    //     'email'     => $Request->email,
    //     'password'  => $Request->password,
    //     'type'      => $Request->type,
    //     'code'      => config('app.env', 'production') == 'production' ? rand(1000, 9999) : 1111,
    // ];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $hash)
    {
        $this->data = $data;

        $this->hash = $hash;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('admin::emails_v2.auth.email_verification')->with([
            'data' => $this->data,
            'hash' => $this->hash,
        ])->subject('Confirm your email address');
    }
}
