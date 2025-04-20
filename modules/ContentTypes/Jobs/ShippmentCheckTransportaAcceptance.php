<?php

namespace Modules\ContentTypes\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Modules\ContentTypes\Entities;
use DB;

class ShippmentCheckTransportaAcceptance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $shippment          = null;

    public $matchedTransportar = null;

    public $nextMatchedTransportar = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Entities\Shippment $shippment, Entities\ShippmentTransporter $matchedTransportar)
    {
        $this->shippment          = $shippment;

        $this->matchedTransportar = $matchedTransportar;
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

        if ($this->nextMatchedTransportar) {
            ShippmentSendToTransportar::dispatch($this->shippment, $this->nextMatchedTransportar);
        }
    }

    public function start()
    {
        if ($this->matchedTransportar->status == 'PENDING_RESPONSE') {
            $this->matchedTransportar->status = 'RESPONSE_DELAYED';
            $this->matchedTransportar->save();

            $this->shippment->updated_at = now()->format('Y-m-d H:i:s');
            $this->shippment->save();

            $nextTransportar = $this->shippment->matchedTransportars()
            ->where('id', '>', $this->matchedTransportar->id)
            ->orderBy('id', 'ASC')
            ->orderBy('price', 'ASC')
            ->first();

            if ($nextTransportar) {
                $nextTransportar->status = 'PENDING_RESPONSE';
                $nextTransportar->save();
                $this->nextMatchedTransportar = $nextTransportar;
            }
            else {
                $this->shippment->sys_status = 'CANCELLED';
                $this->shippment->save();
            }
        }
    }
}
