<?php

namespace Modules\ContentTypes\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Modules\ContentTypes\Entities;
use DB;

class ShippmentSendToTransportar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $shippment          = null;

    public $matchedTransportar = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Entities\Shippment $shippment, Entities\ShippmentTransporter $matchedTransportar)
    {
        $this->shippment          = $shippment;

        $this->matchedTransportar = $matchedTransportar;

        $this->shippment->load([
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
            'ATTACHMENT_1' => function($query) {
                $query->withTrashed();
            },
            'ATTACHMENT_2' => function($query) {
                $query->withTrashed();
            },
            'ATTACHMENT_3' => function($query) {
                $query->withTrashed();
            },
            'ATTACHMENT_4' => function($query) {
                $query->withTrashed();
            },
            'fromCity' => function($query) {
                $query->withTrashed();
            },
            'toCity' => function($query) {
                $query->withTrashed();
            },
        ]);

        $this->matchedTransportar->load([
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
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function() {
            $this->start();
        });

        ShippmentCheckTransportaAcceptance::dispatch($this->shippment, $this->matchedTransportar)->delay(now()->addMinutes(15));
    }

    public function start()
    {
        $this->matchedTransportar->status       = 'PENDING_RESPONSE';
        $this->matchedTransportar->requested_at = now()->format('Y-m-d H:i:s');
        $this->matchedTransportar->responded_at = null;
        $this->matchedTransportar->save();
        // TO DO: Send email to transporter with shippment details

        $this->shippment->updated_at = now()->format('Y-m-d H:i:s');
        $this->shippment->save();
    }
}
