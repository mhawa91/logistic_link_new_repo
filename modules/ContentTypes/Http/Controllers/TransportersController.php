<?php

namespace Modules\ContentTypes\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContentTypes\Entities\TransporterService;

class TransportersController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'contenttypes::transporters';

    public function __construct(TransporterService $TransporterService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware($TransporterService->getPermissionMiddlewareName('HTTP', 'read'))->only(['index', 'postIndex', 'read', 'postRead']);
        $this->middleware($TransporterService->getPermissionMiddlewareName('HTTP', 'create'))->only(['create', 'postCreate']);
        $this->middleware($TransporterService->getPermissionMiddlewareName('HTTP', 'update'))->only(['update', 'postUpdate']);
        $this->middleware($TransporterService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['postStatus']);
        $this->middleware($TransporterService->getPermissionMiddlewareName('HTTP', 'delete'))->only(['postDelete']);
        $this->middleware($TransporterService->getPermissionMiddlewareName('HTTP', 'restore'))->only(['postRestore']);
        $this->middleware($TransporterService->getPermissionMiddlewareName('HTTP', 'permaDelete'))->only(['postPermaDelete']);
    }

    public function index(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        $this->data['Service'] = $TransporterService;
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function update(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        $this->data['model'] = $TransporterService->withTrashed()->findOrFail($Request->model)->data();

        $this->data['truckTypes']  = collect([]);
        $this->data['commodities'] = collect([]);
        $this->data['items']       = collect([]);

        $this->data['commodities'] = $this->data['model']->currentInstance?->commodities->map(function($commodity) {
            return[
                'id'    => $commodity->content->id,
                'text'  => $commodity->content->__('title'),
                'data'  => $commodity->content->toArray(),
            ];
        });

        $this->data['model']->currentInstance?->pricing->each(function($pricing) {
            $this->data['truckTypes']->push([
                'id'    => $pricing->truckType->id,
                'text'  => $pricing->truckType->__('title'),
                'data'  => $pricing->truckType->toArray(),
            ]);

            $this->data['items']->push([
                'id'                            => $pricing->id,
                'truck_type'                    => $pricing->truckType->toArray(),
                'loading_type'                  => $pricing->type,
                'from_city'                     => $pricing->fromCity->toArray(),
                'to_city'                       => $pricing->toCity->toArray(),
                'price'                         => $pricing->price,
                'pre_additional_location_price' => $pricing->pre_additional_location_price,
            ]);
        });

        $this->data['truckTypes'] = $this->data['truckTypes']->unique('id');

        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        $this->data['model'] = $TransporterService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        return $TransporterService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }

    public function postCreate(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        // dd($Request->all());
        $this->data['Validator'] = $TransporterService->validator('create', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $TransporterService) {
            $TransporterService->create($Request->all());
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('TransportersController@index')
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        $this->data['model']     = $TransporterService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['Validator'] = $TransporterService->validator('update', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $TransporterService) {
            $TransporterService->update($Request->all());
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }

    public function postRead(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
    }

    public function select(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        return $TransporterService->ajaxSelect($Request->search ?? null, [
            'nullable' => $Request->nullable ?? true,
        ], $Request->page);
    }

    public function postStatus(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        $this->data['model'] = $TransporterService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $TransporterService) {
            $TransporterService->statusUpdate($Request->status);
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.status_updated.title'),
                'description' => __('ui::strings.messages.status_updated.description'),
            ],
        ]);
    }

    public function postDelete(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        $this->data['model'] = $TransporterService->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $TransporterService) {
            $TransporterService->delete();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.deleted.title'),
                'description' => __('ui::strings.messages.deleted.description'),
            ],
        ]);
    }

    public function postRestore(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        $this->data['model'] = $TransporterService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $TransporterService) {
            $TransporterService->restore();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.restored.title'),
                'description' => __('ui::strings.messages.restored.description'),
            ],
        ]);
    }

    public function postPermaDelete(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        $this->data['model'] = $TransporterService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $TransporterService) {
            $TransporterService->permaDelete();
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.permaDeleted.title'),
                'description' => __('ui::strings.messages.permaDeleted.description'),
            ],
        ]);
    }

    public function postExport(Request $Request, ActionResponse $Response, TransporterService $TransporterService)
    {
        return $TransporterService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->export($Request->search['value'] ?? $Request->advanced_search ?? null)->toCsv();
    }
}
