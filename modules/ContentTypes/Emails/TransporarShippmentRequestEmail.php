<?php

namespace Modules\ContentTypes\Emails;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\ContentTypes\Entities;

class TransporarShippmentRequestEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var $transportarUser
     */
    public $transportarUser;

    /**
     * The shippments instance.
     *
     * @var $shippments
     */
    public $shippments;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $transportar, array $shippments)
    {
        $this->transportarUser = $transportar;

        $this->shippments      = Entities\Shippment::whereIn('id', $shippments)
        ->whereHas('matchedTransportars', function($query) {
            $query->where('transporter_user_id', $this->transportarUser->id);
        })
        ->whereHas('companyUser', function($query) {
            $query->withTrashed();
            $query ->whereHas('company', function($query) {
                $query->withTrashed();
            });
        })
        ->with([
            'loadingLocation' => function($query) {
                $query->withTrashed();
            },
            'offloadingLocations' => function($query) {
                $query->withTrashed();
            },
            'commodities' => function($query) {
                $query->with([
                    'commodity' => function($query) {
                        $query->withTrashed()->with('translations');
                    },
                ]);
            },
            'loadSize' => function($query) {
                $query->withTrashed()->with('translations');
            },
            'loadType' => function($query) {
                $query->withTrashed()->with('translations');
            },
            'truckType' => function($query) {
                $query->withTrashed()->with('translations');
            },
            'companyUser' => function($query) {
                $query->withTrashed();
            },
            'companyUser.company' => function($query) {
                $query->withTrashed();
            },
            'transporterUser' => function($query) {
                $query->withTrashed();
            },
            'transporterUser.company' => function($query) {
                $query->withTrashed();
            },
            'fromCountry' => function($query) {
                $query->withTrashed();
            },
            'toCountry' => function($query) {
                $query->withTrashed();
            },
            'fromCity' => function($query) {
                $query->withTrashed();
            },
            'toCity' => function($query) {
                $query->withTrashed();
            },
            'matchedTransportar' => function($query) {
                $query->where('transporter_user_id', $this->transportarUser->id);
            },
        ])
        ->get();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('contenttypes::emails.shippment_request')->with([
            'transportarUser' => $this->transportarUser,
            'shippments'      => $this->shippments,
            'helper'          => app(\Modules\SystemCore\System\Classes\Helpers::class)
        ])
        ->subject(config('mail_theme.appName') . ' | You have shippments requests awaiting your approval');
    }
}
