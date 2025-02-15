<?php

namespace Modules\SystemCore\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\SystemCore\Entities\MunicipalitiesService;

class MunicipalitiesController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'systemcore::municipalities';

    public function __construct(MunicipalitiesService $MunicipalitiesService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware($MunicipalitiesService->getPermissionMiddlewareName('HTTP', 'read'))->only(['index', 'postIndex', 'read', 'postRead']);
        $this->middleware($MunicipalitiesService->getPermissionMiddlewareName('HTTP', 'create'))->only(['create', 'postCreate']);
        $this->middleware($MunicipalitiesService->getPermissionMiddlewareName('HTTP', 'update'))->only(['update', 'postUpdate']);
        $this->middleware($MunicipalitiesService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['postStatus']);
        $this->middleware($MunicipalitiesService->getPermissionMiddlewareName('HTTP', 'delete'))->only(['postDelete']);
        $this->middleware($MunicipalitiesService->getPermissionMiddlewareName('HTTP', 'restore'))->only(['postRestore']);
        $this->middleware($MunicipalitiesService->getPermissionMiddlewareName('HTTP', 'permaDelete'))->only(['postPermaDelete']);
    }

    public function index(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
        $this->data['Service'] = $MunicipalitiesService;
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function update(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
        $this->data['model'] = $MunicipalitiesService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
        $this->data['model'] = $MunicipalitiesService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
        return $MunicipalitiesService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }

    public function postCreate(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
        $this->data['Validator'] = $MunicipalitiesService->validator('create', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $MunicipalitiesService) {
            $MunicipalitiesService->create($Request->all());
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('MunicipalitiesController@index')
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
        $this->data['model']     = $MunicipalitiesService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['Validator'] = $MunicipalitiesService->validator('update', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $MunicipalitiesService) {
            $MunicipalitiesService->update($Request->all());
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }

    public function postRead(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
    }

    public function select(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
        return $MunicipalitiesService->ajaxSelect($Request->search ?? null, [
            'nullable'        => true,
            'country_id'      => $Request->country_id      ?? null,
            'city_id'         => $Request->city_id         ?? null,
            'municipality_id' => $Request->municipality_id ?? null,
        ], $Request->page);
    }

    public function postStatus(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
        $this->data['model'] = $MunicipalitiesService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $MunicipalitiesService) {
            $MunicipalitiesService->statusUpdate($Request->status);
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.status_updated.title'),
                'description' => __('ui::strings.messages.status_updated.description'),
            ],
        ]);
    }

    public function postDelete(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
        $this->data['model'] = $MunicipalitiesService->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $MunicipalitiesService) {
            $MunicipalitiesService->delete();
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.deleted.title'),
                'description' => __('ui::strings.messages.deleted.description'),
            ],
        ]);
    }

    public function postRestore(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
        $this->data['model'] = $MunicipalitiesService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $MunicipalitiesService) {
            $MunicipalitiesService->restore();
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.restored.title'),
                'description' => __('ui::strings.messages.restored.description'),
            ],
        ]);
    }

    public function postPermaDelete(Request $Request, ActionResponse $Response, MunicipalitiesService $MunicipalitiesService)
    {
        $this->data['model'] = $MunicipalitiesService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $MunicipalitiesService) {
            $MunicipalitiesService->permaDelete();
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
