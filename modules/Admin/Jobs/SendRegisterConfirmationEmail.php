<?php

namespace Modules\Admin\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;
use Modules\Admin\Emails\ConfirmEmailAddress;

class SendRegisterConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $hash)
    {
        $this->data = $data;

        $this->hash = $hash;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Send email to user
        Mail::to($this->data['email'])
        ->send(
            new ConfirmEmailAddress($this->data, $this->hash)
        );
    }
}
