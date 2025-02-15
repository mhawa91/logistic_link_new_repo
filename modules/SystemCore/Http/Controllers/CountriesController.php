<?php

namespace Modules\SystemCore\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\SystemCore\Entities\CountriesService;

class CountriesController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'systemcore::countries';

    public function __construct(CountriesService $CountriesService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware($CountriesService->getPermissionMiddlewareName('HTTP', 'read'))->only(['index', 'postIndex', 'read', 'postRead']);
        $this->middleware($CountriesService->getPermissionMiddlewareName('HTTP', 'create'))->only(['create', 'postCreate']);
        $this->middleware($CountriesService->getPermissionMiddlewareName('HTTP', 'update'))->only(['update', 'postUpdate']);
        $this->middleware($CountriesService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['postStatus']);
        $this->middleware($CountriesService->getPermissionMiddlewareName('HTTP', 'delete'))->only(['postDelete']);
        $this->middleware($CountriesService->getPermissionMiddlewareName('HTTP', 'restore'))->only(['postRestore']);
        $this->middleware($CountriesService->getPermissionMiddlewareName('HTTP', 'permaDelete'))->only(['postPermaDelete']);
    }

    public function index(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
        $this->data['Service'] = $CountriesService;
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function update(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
        $this->data['model'] = $CountriesService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
        $this->data['model'] = $CountriesService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
        return $CountriesService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }

    public function postCreate(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
        $this->data['Validator'] = $CountriesService->validator('create', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $CountriesService) {
            $CountriesService->create($Request->all());
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('CountriesController@index')
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
        $this->data['model']     = $CountriesService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['Validator'] = $CountriesService->validator('update', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $CountriesService) {
            $CountriesService->update($Request->all());
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }

    public function postRead(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
    }

    public function select(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
        return $CountriesService->ajaxSelect($Request->search ?? null, [
            'nullable'   => $Request->nullable   ?? true,
            'country_id' => $Request->country_id ?? null,
        ], $Request->page);
    }

    public function postStatus(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
        $this->data['model'] = $CountriesService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $CountriesService) {
            $CountriesService->statusUpdate($Request->status);
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.status_updated.title'),
                'description' => __('ui::strings.messages.status_updated.description'),
            ],
        ]);
    }

    public function postDelete(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
        $this->data['model'] = $CountriesService->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $CountriesService) {
            $CountriesService->delete();
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.deleted.title'),
                'description' => __('ui::strings.messages.deleted.description'),
            ],
        ]);
    }

    public function postRestore(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
        $this->data['model'] = $CountriesService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $CountriesService) {
            $CountriesService->restore();
        });
        return $Response->module('SystemCore')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.restored.title'),
                'description' => __('ui::strings.messages.restored.description'),
            ],
        ]);
    }

    public function postPermaDelete(Request $Request, ActionResponse $Response, CountriesService $CountriesService)
    {
        $this->data['model'] = $CountriesService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $CountriesService) {
            $CountriesService->permaDelete();
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
