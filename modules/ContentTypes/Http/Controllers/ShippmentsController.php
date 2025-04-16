<?php

namespace Modules\ContentTypes\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContentTypes\Entities\ShippmentService;
use Modules\ContentManager\Entities\Content;

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
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model'] = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
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
        DB::transaction(function() use($Request, $ShippmentService) {
            $ShippmentService->create($Request->all());
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('ShippmentsController@index')
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response, ShippmentService $ShippmentService)
    {
        $this->data['model']     = $ShippmentService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['Validator'] = $ShippmentService->validator('update', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $ShippmentService) {
            $ShippmentService->update($Request->all());
        });
        return $Response->module('ContentTypes')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
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
}
