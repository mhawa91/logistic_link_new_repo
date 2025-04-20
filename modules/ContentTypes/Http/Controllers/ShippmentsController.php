<?php

namespace Modules\ContentTypes\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContentTypes\Entities\ShippmentService;
use Modules\ContentManager\Entities\Content;

use Modules\ContentTypes\Jobs;

class ShippmentsController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'contenttypes::shippments';

    public function __construct(ShippmentService $ShippmentService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'read'))->only(['index', 'postIndex', 'read', 'postRead']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'create'))->only(['create', 'postCreate']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'update'))->only(['update', 'postUpdate']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['postStatus']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'delete'))->only(['postDelete']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'restore'))->only(['postRestore']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'permaDelete'))->only(['postPermaDelete']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['transportarApprove']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['transportarApproveWithPriceChange']);
        $this->middleware($ShippmentService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['transportarReject']);
    }

    public function index(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['Service'] = $ShippmentService;
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['loadSizes'] = Content::where('base_type', 'CATEGORY')
        ->with('translations')
        ->where('type', 'load-size')
        ->where('sys_status', 'ACTIVE')
        ->get();

        $this->data['loadTypes'] = Content::where('base_type', 'CATEGORY')
        ->with('translations')
        ->where('type', 'load-type')
        ->where('sys_status', 'ACTIVE')
        ->get();

        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function update(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['loadSizes'] = Content::where('base_type', 'CATEGORY')
        ->with('translations')
        ->where('type', 'load-size')
        ->where('sys_status', 'ACTIVE')
        ->get();

        $this->data['loadTypes'] = Content::where('base_type', 'CATEGORY')
        ->with('translations')
        ->where('type', 'load-type')
        ->where('sys_status', 'ACTIVE')
        ->get();

        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();

        if(auth()->check() && auth()->user()->type == 'DEFAULT' && $this->data['model']->company_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }

        if(auth()->check() && auth()->user()->type == 'TRANSPORTER') {
            return redirect()->route('ShippmentsController@read', ['model' => $this->data['model']->id]);
        }

        // if($this->data['model']->sys_status == 'MATCHING') {
        //     return redirect()->route('ShippmentsController@read', ['model' => $this->data['model']->id]);
        // }

        $this->data['model']->load([
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
            'ADDITIONAL_DATA' => function($query) {
                $query->withTrashed();
            },
            'fromCity' => function($query) {
                $query->withTrashed();
            },
            'toCity' => function($query) {
                $query->withTrashed();
            },
        ]);

        $this->data['addresses'] = $this->data['model']->offloadingLocations->map(function($location, $index) {
            return [
                'el'                           => 'geo_map_' . $index,
                'id'                           => $location->id,
                'primary_contact_company_name' => $location->primary_contact_company_name,
                'primary_contact_name'         => $location->primary_contact_name,
                'primary_contact_email'        => $location->primary_contact_email,
                'primary_contact_phone_number' => $location->primary_contact_phone_number,
                'lat'                          => $location->lat,
                'lng'                          => $location->lng,
                'latlng'                       => $location->lat . ',' . $location->lng,
                'round_trip'                   => $location->round_trip,
                'full_address'                 => $location->full_address,
                'public_notes'                 => $location->public_notes,
                'private_notes'                => $location->private_notes,
                'removed'                      => 'N',
                'map'                          => null,
            ];
        });

        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'matchedTransportars' => function($query) {
                $query->where('transporter_user_id', auth()->id());
            },
        ]);

        $this->data['matchedTransportar'] = $this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->first();

        if(auth()->check() && auth()->user()->type == 'DEFAULT' && $this->data['model']->company_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }

        if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $this->data['matchedTransportar'] && $this->data['matchedTransportar']->transporter_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }

        if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $this->data['matchedTransportar'] && ! in_array($this->data['matchedTransportar']->status, ['PENDING_RESPONSE', 'ACCEPTED'])) {
            return redirect()->route('ShippmentsController@index');
        }

        $this->data['model']->load([
            'loadingLocation' => function($query) {
                $query->withTrashed();
                $query->with([
                    'city' => function($query) {
                        $query->withTrashed();
                    },
                ]);
            },
            'offloadingLocations' => function($query) {
                $query->withTrashed();
                $query->with([
                    'city' => function($query) {
                        $query->withTrashed();
                    },
                ]);
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
            'ADDITIONAL_DATA' => function($query) {
                $query->withTrashed();
            },
            'fromCity' => function($query) {
                $query->withTrashed();
            },
            'toCity' => function($query) {
                $query->withTrashed();
            },
        ]);

        $this->data['helper'] = app(\Modules\SystemCore\System\Classes\Helpers::class);
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        return $ShippmentService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }

    public function postCreate(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['Validator'] = $ShippmentService->validator('create', $Request->all());
        $this->data['Validator']->validate();
        $shippment = DB::transaction(function() use($Request, $ShippmentService) {
            return $ShippmentService->create($Request->all());
        });

        if($shippment->sys_status == 'MATCHING') {
            Jobs\ShippmentMatchTransportar::dispatch($shippment);
        }

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('ShippmentsController@index')
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model']     = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        if(auth()->check() && auth()->user()->type == 'DEFAULT' && $this->data['model']->company_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }
        if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $this->data['model']->transporter_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }
        $this->data['Validator'] = $ShippmentService->validator('update', $Request->all());
        $this->data['Validator']->validate();
        $shippment = DB::transaction(function() use($Request, $ShippmentService) {
            return $ShippmentService->update($Request->all());
        });

        if($shippment->sys_status == 'MATCHING') {
            Jobs\ShippmentMatchTransportar::dispatch($shippment);
        }

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
            'redirect' => route('ShippmentsController@read', ['model' => $this->data['model']->id]),
        ]);
    }

    public function postRead(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
    }

    public function select(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        return $ShippmentService->ajaxSelect($Request->search ?? null, [
            'nullable' => $Request->nullable ?? true,
        ], $Request->page);
    }

    public function postStatus(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();

        if(auth()->check() && auth()->user()->type == 'DEFAULT' && $this->data['model']->company_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }
        if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $this->data['model']->transporter_user_id != auth()->id()) {
            return redirect()->route('ShippmentsController@index');
        }

        DB::transaction(function() use($Request, $ShippmentService) {
            $ShippmentService->statusUpdate($Request->status);
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.status_updated.title'),
                'description' => __('ui::strings.messages.status_updated.description'),
            ],
        ]);
    }

    public function postDelete(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ShippmentService) {
            $ShippmentService->delete();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.deleted.title'),
                'description' => __('ui::strings.messages.deleted.description'),
            ],
        ]);
    }

    public function postRestore(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ShippmentService) {
            $ShippmentService->restore();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.restored.title'),
                'description' => __('ui::strings.messages.restored.description'),
            ],
        ]);
    }

    public function postPermaDelete(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ShippmentService) {
            $ShippmentService->permaDelete();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.permaDeleted.title'),
                'description' => __('ui::strings.messages.permaDeleted.description'),
            ],
        ]);
    }

    public function postExport(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        return $ShippmentService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->export($Request->search['value'] ?? $Request->advanced_search ?? null)->toCsv();
    }

    public function transportarApprove(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'matchedTransportars' => function($query) {
                $query->where('transporter_user_id', auth()->id());
            },
        ]);
        
        if(auth()->check() && auth()->user()->type != 'TRANSPORTER') {
            return redirect()->route('ShippmentsController@index');
        }
        if($this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->isEmpty()) {
            return redirect()->route('ShippmentsController@index');
        }
        if($this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->where('status', 'PENDING_RESPONSE')->isEmpty()) {
            return redirect()->route('ShippmentsController@index');
        }

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->first()->update([
                'status' => 'ACCEPTED',
            ]);
            $this->data['model']->sys_status = 'PENDING_PICKUP';
            $this->data['model']->save();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.transportarApprove.title'),
                'description' => __('contenttypes::strings.messages.transportarApprove.description'),
            ],
            'redirect' => route('ShippmentsController@read', ['model' => $this->data['model']->id]),
        ]);
    }

    public function transportarApproveWithPriceChange(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'matchedTransportars' => function($query) {
                $query->where('transporter_user_id', auth()->id());
            },
        ]);

        if(auth()->check() && auth()->user()->type != 'TRANSPORTER') {
            return redirect()->route('ShippmentsController@index');
        }
        if($this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->isEmpty()) {
            return redirect()->route('ShippmentsController@index');
        }
        if($this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->where('status', 'PENDING_RESPONSE')->isEmpty()) {
            return redirect()->route('ShippmentsController@index');
        }

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->first()->update([
                'status' => 'ACCEPTED',
            ]);
            $this->data['model']->sys_status = 'TRANSPORTER_APPROVED_WITH_PRICE_CHANGE';
            $this->data['model']->save();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.transportarApproveWithPriceChange.title'),
                'description' => __('contenttypes::strings.messages.transportarApproveWithPriceChange.description'),
            ],
            'redirect' => route('ShippmentsController@read', ['model' => $this->data['model']->id]),
        ]);
    }

    public function transportarReject(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'matchedTransportars' => function($query) {
                $query->where('transporter_user_id', auth()->id());
            },
        ]);

        $this->data['matchedTransportar'] = $this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->first();

        if(auth()->check() && auth()->user()->type != 'TRANSPORTER') {
            return redirect()->route('ShippmentsController@index');
        }
        if($this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->isEmpty()) {
            return redirect()->route('ShippmentsController@index');
        }
        if($this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->where('status', 'PENDING_RESPONSE')->isEmpty()) {
            return redirect()->route('ShippmentsController@index');
        }

        DB::transaction(function() use($Request, $ShippmentService) {
            $this->data['model']->matchedTransportars->where('transporter_user_id', auth()->id())->first()->update([
                'status' => 'REJECTED',
            ]);
            $this->data['model']->sys_status = 'PENDING_TRANSPORTER_APPROVAL';
            $this->data['model']->save();
        });

        $nextTransportar = $this->data['model']->matchedTransportars()
        ->where('id', '>', $this->data['matchedTransportar']->id)
        ->orderBy('id', 'ASC')
        ->orderBy('price', 'ASC')
        ->first();

        if ($nextTransportar) {
            $nextTransportar->status = 'PENDING_RESPONSE';
            $nextTransportar->save();
            Jobs\ShippmentSendToTransportar::dispatch($this->data['model'], $nextTransportar);
        }
        else {
            $this->data['model']->sys_status = 'CANCELLED';
            $this->data['model']->save();
        }

        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('contenttypes::strings.messages.transportarReject.title'),
                'description' => __('contenttypes::strings.messages.transportarReject.description'),
            ],
            'redirect' => route('ShippmentsController@index'),
        ]);
    }
}
