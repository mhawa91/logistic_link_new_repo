<?php

namespace Modules\ContentTypes\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Modules\ContentTypes\Entities;
use DB;

class ShippmentMatchTransportar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $shippment = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Entities\Shippment $shippment)
    {
        $this->shippment = $shippment;

        $this->shippment->load([
            'offloadingLocations' => function($query) {
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

        $transportarMatched = $this->shippment->matchedTransportars()
        ->orderBy('id', 'ASC')
        ->orderBy('price', 'ASC')
        ->first();

        if ($transportarMatched) {
            ShippmentSendToTransportar::dispatch($this->shippment, $transportarMatched);
        }
    }

    public function start()
    {
        $suitableContracts = $this->suitableContracts();

        if ($suitableContracts->isEmpty()) {
            $this->shippment->sys_status = 'NO_MATCHING_CONTRACTS';
            $this->shippment->save();
            return true;
        }

        $this->shippment->matchedTransportars()->delete();

        $matchedTransportars = $suitableContracts->map(function($contract) {
            return [
                'shippment_id'                  => $this->shippment->id,
                'company_user_id'               => $this->shippment->company_user_id,
                'transporter_user_id'           => $contract->transporter_user_id,
                'contract_id'                   => $contract->contract_id,
                'contract_instance_id'          => $contract->contract_instance_id,
                'price'                         => $contract->price,
                'pre_additional_location_price' => $contract->pre_additional_location_price,
                'total_price'                   => ($this->shippment->number_of_trucks * $contract->price) + (($this->shippment->offloadingLocations->count() - 1) * $contract->pre_additional_location_price),
                'status'                        => 'PENDING',
                'requested_at'                  => now()->format('Y-m-d H:i:s'),
                'responded_at'                  => null,
            ];
        });

        $this->shippment->matchedTransportars()->insert($matchedTransportars->toArray());

        $this->shippment->sys_status = 'PENDING_TRANSPORTER_APPROVAL';
        $this->shippment->save();
    }

    public function suitableContracts()
    {
        $type              = $this->shippment->round_trip == 'YES' ? 'CITY_TO_CITY_ROUND_TRIP' : $this->shippment->type;
        $suitableContracts = Entities\TransporterContractInstancePrice::where('company_user_id', $this->shippment->company_user_id)
        ->has('client')
        ->has('transporter')
        ->whereHas('contractInstance', function($query) {
            $query->select('lgl_transporters_contracts_instances.*');
            $query->onlyActiveByStartDate();
            $query->where('is_current', 'YES');
            $query->where('sys_status', 'ACTIVE');
        })
        ->with([
            'contractInstance' => function($query) {
                $query->select('lgl_transporters_contracts_instances.*');
                $query->onlyActiveByStartDate();
                $query->where('is_current', 'YES');
                $query->where('sys_status', 'ACTIVE');
            },
        ])
        ->where('type', $type)
        ->where('truck_type_id', $this->shippment->truck_type_id)
        ->where('from_country_id', $this->shippment->from_country_id)
        ->where('from_city_id', $this->shippment->from_city_id)
        ->where('to_country_id', $this->shippment->to_country_id)
        ->where('to_city_id', $this->shippment->to_city_id)
        ->orderBy('id', 'ASC')
        ->orderBy('price', 'ASC')
        ->orderBy('pre_additional_location_price', 'ASC')
        ->get();

        return $suitableContracts;
    }
}
