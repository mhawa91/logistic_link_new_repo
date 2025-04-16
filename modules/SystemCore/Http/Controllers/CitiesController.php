<?php

namespace Modules\SystemCore\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\SystemCore\Entities\CitiesService;

class CitiesController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'systemcore::cities';

    public function __construct(CitiesService $CitiesService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware($CitiesService->getPermissionMiddlewareName('HTTP', 'read'))->only(['index', 'postIndex', 'read', 'postRead']);
        $this->middleware($CitiesService->getPermissionMiddlewareName('HTTP', 'create'))->only(['create', 'postCreate']);
        $this->middleware($CitiesService->getPermissionMiddlewareName('HTTP', 'update'))->only(['update', 'postUpdate']);
        $this->middleware($CitiesService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['postStatus']);
        $this->middleware($CitiesService->getPermissionMiddlewareName('HTTP', 'delete'))->only(['postDelete']);
        $this->middleware($CitiesService->getPermissionMiddlewareName('HTTP', 'restore'))->only(['postRestore']);
        $this->middleware($CitiesService->getPermissionMiddlewareName('HTTP', 'permaDelete'))->only(['postPermaDelete']);
    }

    public function index(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
        $this->data['Service'] = $CitiesService;
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function update(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
        $this->data['model'] = $CitiesService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
        $this->data['model'] = $CitiesService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
        return $CitiesService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }

    public function postCreate(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
        $this->data['Validator'] = $CitiesService->validator('create', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $CitiesService) {
            $CitiesService->create($Request->all());
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('CitiesController@index')
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
        $this->data['model']     = $CitiesService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['Validator'] = $CitiesService->validator('update', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $CitiesService) {
            $CitiesService->update($Request->all());
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }

    public function postRead(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
    }

    public function select(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
        $CitiesService->unguard();
        return $CitiesService->ajaxSelect($Request->search ?? null, [
            'nullable'   => $Request->nullable   ?? true,
            'country_id' => $Request->country_id ?? null,
            'city_id'    => $Request->city_id    ?? null,
        ], $Request->page);
    }

    public function postStatus(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
        $this->data['model'] = $CitiesService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $CitiesService) {
            $CitiesService->statusUpdate($Request->status);
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.status_updated.title'),
                'description' => __('ui::strings.messages.status_updated.description'),
            ],
        ]);
    }

    public function postDelete(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
        $this->data['model'] = $CitiesService->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $CitiesService) {
            $CitiesService->delete();
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.deleted.title'),
                'description' => __('ui::strings.messages.deleted.description'),
            ],
        ]);
    }

    public function postRestore(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
        $this->data['model'] = $CitiesService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $CitiesService) {
            $CitiesService->restore();
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.restored.title'),
                'description' => __('ui::strings.messages.restored.description'),
            ],
        ]);
    }

    public function postPermaDelete(Request $Request, ActionResponse $Response, CitiesService $CitiesService)
    {
        $this->data['model'] = $CitiesService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $CitiesService) {
            $CitiesService->permaDelete();
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
