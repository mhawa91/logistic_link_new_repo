<?php

namespace Modules\ContentTypes\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContentTypes\Entities\CompanyService;
use Modules\ContentTypes\Entities\Company;
use Modules\ContentTypes\Entities\Shippment;
use Modules\ContentTypes\Entities\UploadedFile;
use Modules\ContentManager\Entities\Content;
use Validator;

use Modules\ContentTypes\Jobs;

class PublicTransportarsController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'contenttypes::public_transportars';

    public function __construct(CompanyService $CompanyService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
    }

    public function index(Request $Request, ActionResponse $Response, CompanyService $CompanyService)
    {
        $this->data['Service'] = $CompanyService;
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, CompanyService $CompanyService)
    {
        $advanced_search = $Request->advanced_search;
        if ($advanced_search && $advanced_search['suitable_for_shippment']) {
            $advanced_search['suitable_for_shippment'] = Shippment::where('id', $advanced_search['suitable_for_shippment'])->firstOrFail();
        }

        return $CompanyService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $advanced_search ?? null)->toJson();
    }

    public function read(Request $Request, ActionResponse $Response, CompanyService $CompanyService)
    {
        $this->data['Service'] = $CompanyService;
        $this->data['model']   = $CompanyService->customModel()->findOrFail($Request->model);

        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function postRfq(Request $Request, ActionResponse $Response, CompanyService $CompanyService)
    {
        $this->data['shippment'] = Shippment::where('id', $Request->shippment_id)
        ->where('company_user_id', auth()->id())
        ->first();

        if (! $this->data['shippment']) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.shippment_not_available.title'),
                    'description' => __('contenttypes::strings.messages.shippment_not_available.description'),
                ],
            ]);
        }

        $this->data['ids'] = collect($Request->transportar);

        if ($this->data['ids']->isEmpty()) {
            return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
                'toastr' => [
                    'type'        => 'error',
                    'title'       => __('contenttypes::strings.messages.select_one_transportar_at_least.title'),
                    'description' => __('contenttypes::strings.messages.select_one_transportar_at_least.description'),
                ],
            ]);
        }

        DB::transaction(function() use($Request) {
            $this->data['shippment']->matchedTransportars()->delete();
            $this->data['shippment']->rfqs()->delete();
            $this->data['shippment']->allAddressess()->update([
                'delivered_at' => null,
            ]);
            $this->data['ids']->each(function($id) use($Request) {
                $TransportarUser = Company::findOrFail($id);
                $this->data['shippment']->rfqs()->create([
                    'shippment_id'        => $this->data['shippment']->id,
                    'company_user_id'     => $this->data['shippment']->company_user_id,
                    'transporter_user_id' => $TransportarUser->user_id,
                    'status'              => 'PENDING_RESPONSE',
                    'requested_at'        => now()->format('Y-m-d H:i:s'),
                ]);
                // TO DO: send email to the transporter
            });
            $this->data['shippment']->sys_status                    = 'RFQ_PENDING_TRANSPORTER_OFFER';
            $this->data['shippment']->is_rfq                        = 'YES';
            $this->data['shippment']->transporter_user_id           = null;
            $this->data['shippment']->driver_id                     = null;
            $this->data['shippment']->car_plate                     = null;
            $this->data['shippment']->contract_id                   = null;
            $this->data['shippment']->contract_instance_id          = null;
            $this->data['shippment']->price                         = 0;
            $this->data['shippment']->pre_additional_location_price = 0;
            $this->data['shippment']->total_price                   = 0;
            $this->data['shippment']->save();
        });

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.rfqs_sent.title'),
                'description' => __('contenttypes::strings.messages.rfqs_sent.description'),
            ],
            'redirect' => route('RfqController@index', ['shippment_id' => $this->data['shippment']->id]),
        ]);
    }
}