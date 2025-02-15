<?php

namespace Modules\SystemCore\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\SystemCore\Entities\NeighbourhoodsService;

class NeighbourhoodsController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'systemcore::neighbourhoods';

    public function __construct(NeighbourhoodsService $NeighbourhoodsService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware($NeighbourhoodsService->getPermissionMiddlewareName('HTTP', 'read'))->only(['index', 'postIndex', 'read', 'postRead']);
        $this->middleware($NeighbourhoodsService->getPermissionMiddlewareName('HTTP', 'create'))->only(['create', 'postCreate']);
        $this->middleware($NeighbourhoodsService->getPermissionMiddlewareName('HTTP', 'update'))->only(['update', 'postUpdate']);
        $this->middleware($NeighbourhoodsService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['postStatus']);
        $this->middleware($NeighbourhoodsService->getPermissionMiddlewareName('HTTP', 'delete'))->only(['postDelete']);
        $this->middleware($NeighbourhoodsService->getPermissionMiddlewareName('HTTP', 'restore'))->only(['postRestore']);
        $this->middleware($NeighbourhoodsService->getPermissionMiddlewareName('HTTP', 'permaDelete'))->only(['postPermaDelete']);
    }

    public function index(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
        $this->data['Service'] = $NeighbourhoodsService;
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function update(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
        $this->data['model'] = $NeighbourhoodsService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
        $this->data['model'] = $NeighbourhoodsService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
        return $NeighbourhoodsService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }

    public function postCreate(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
        $this->data['Validator'] = $NeighbourhoodsService->validator('create', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $NeighbourhoodsService) {
            $NeighbourhoodsService->create($Request->all());
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('NeighbourhoodsController@index')
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
        $this->data['model']     = $NeighbourhoodsService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['Validator'] = $NeighbourhoodsService->validator('update', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $NeighbourhoodsService) {
            $NeighbourhoodsService->update($Request->all());
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }

    public function postRead(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
    }

    public function select(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
        $NeighbourhoodsService->unguard();
        return $NeighbourhoodsService->ajaxSelect($Request->search ?? null, [
            'nullable'        => true,
            'country_id'      => $Request->country_id      ?? null,
            'city_id'         => $Request->city_id         ?? null,
            'municipality_id' => $Request->municipality_id ?? null,
        ], $Request->page);
    }

    public function postStatus(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
        $this->data['model'] = $NeighbourhoodsService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $NeighbourhoodsService) {
            $NeighbourhoodsService->statusUpdate($Request->status);
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.status_updated.title'),
                'description' => __('ui::strings.messages.status_updated.description'),
            ],
        ]);
    }

    public function postDelete(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
        $this->data['model'] = $NeighbourhoodsService->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $NeighbourhoodsService) {
            $NeighbourhoodsService->delete();
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.deleted.title'),
                'description' => __('ui::strings.messages.deleted.description'),
            ],
        ]);
    }

    public function postRestore(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
        $this->data['model'] = $NeighbourhoodsService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $NeighbourhoodsService) {
            $NeighbourhoodsService->restore();
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.restored.title'),
                'description' => __('ui::strings.messages.restored.description'),
            ],
        ]);
    }

    public function postPermaDelete(Request $Request, ActionResponse $Response, NeighbourhoodsService $NeighbourhoodsService)
    {
        $this->data['model'] = $NeighbourhoodsService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $NeighbourhoodsService) {
            $NeighbourhoodsService->permaDelete();
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.permaDeleted.title'),
                'description' => __('ui::strings.messages.permaDeleted.description'),
            ],
        ]);
    }
}
