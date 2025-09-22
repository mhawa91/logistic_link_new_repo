<?php

namespace Modules\ContentTypes\Emails;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContractNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var $user
     */
    public $user;

    /**
     * The company name instance.
     *
     * @var $company
     */
    public $company;

    /**
     * The contract details instance.
     *
     * @var $contract
     */
    public $contract;

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
    public function __construct(User $user, string $company, $contract = null, string $hash = null)
    {
        $this->user = $user;
        $this->company = $company;
        $this->contract = $contract;
        $this->hash = $hash;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('contenttypes::emails.contract_notification')->with([
            'user' => $this->user,
            'company' => $this->company,
            'contract' => $this->contract,
            'hash' => $this->hash,
        ])
        ->subject('New Contract Added - ' . config('mail_theme.appName'));
    }
}
