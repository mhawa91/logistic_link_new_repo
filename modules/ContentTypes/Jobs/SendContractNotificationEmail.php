<?php

namespace Modules\ContentTypes\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;
use Modules\ContentTypes\Emails\ContractNotificationEmail;
use App\Models\User;

class SendContractNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $company;

    public $contract;

    public $hash;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $company, $contract = null, $hash = null)
    {
        $this->user = $user;

        $this->company = $company;

        $this->contract = $contract;

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
            new ContractNotificationEmail($this->user, $this->company, $this->contract, $this->hash)
        );
    }
}
