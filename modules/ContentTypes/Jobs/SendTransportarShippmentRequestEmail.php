<?php

namespace Modules\ContentTypes\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;
use Modules\ContentTypes\Emails\TransporarShippmentRequestEmail;
use Modules\ContentTypes\Entities;
use App\Models\User;

class SendTransportarShippmentRequestEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transportar;

    public $shippments;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $transportar, array $shippments = [])
    {
        $this->transportar = $transportar;

        $this->shippments  = $shippments;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Send email to user
        Mail::to($this->transportar->email)->send(
            new TransporarShippmentRequestEmail($this->transportar, $this->shippments)
        );
    }
}
