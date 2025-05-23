<?php

namespace Modules\ContentTypes\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;
use Modules\ContentTypes\Emails\TransportarInvitationEmail;
use App\Models\User;

class SendTransportarInvitationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $hash;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $hash)
    {
        $this->user = $user;

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
        Mail::to($this->user->email)->send(
            new TransportarInvitationEmail($this->user, $this->hash)
        );
    }
}
