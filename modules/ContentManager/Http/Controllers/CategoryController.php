<?php

namespace Modules\ContentManager\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContentManager\Entities\Contracts\CategoryInterface;
use Modules\ContentManager\Entities\Services\CategoryService;

class CategoryController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'contentmanager::categories';

    public $service          = null;

    public function __construct(CategoryInterface $CategoryService)
    {
        $this->service = $CategoryService;
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('read'))->only(['index', 'postIndex', 'read', 'postRead']);
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('create'))->only(['create', 'postCreate']);
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('update'))->only(['update', 'postUpdate']);
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('statusUpdate'))->only(['postStatus']);
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('delete'))->only(['postDelete']);
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('restore'))->only(['postRestore']);
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('permaDelete'))->only(['postPermaDelete']);
    }

    public function index(Request $Request, ActionResponse $Response)
    {
        $this->data['type']    = $Request->type;
        $this->data['Service'] = $this->service;
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response)
    {
        $this->data['type'] = $Request->type;
        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function update(Request $Request, ActionResponse $Response)
    {
        $this->data['model']   = $this->service->withTrashed()->find($Request->model);
        $this->data['type']    = $this->data['model']->type;
        $this->data['Parent']  = $this->service->toSelect2Option($this->data['model']->Parent);
        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response)
    {
        $this->data['model'] = $this->service->withTrashed()->find($Request->model);
        $this->data['type']  = $this->data['model']->type;
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response)
    {
        return $this->service
        ->ofType($Request->type)
        ->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })
        ->autoSearch($Request->search['value'] ?? $Request->advanced_search ?? null)
        ->dataTable()
        ->toJson();
    }

    public function postCreate(Request $Request, ActionResponse $Response)
    {
        $this->data['Validator'] = $this->service->validator($Request->all(), 'create');
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request) {
            $this->service->create($Request->all());
        });
        return $Response->module('Products')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('CategoryController@index', ['type' => $Request->type])
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response)
    {
        $this->data['Validator'] = $this->service->validator($Request->all(), 'update');
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request) {
            $this->service->update($Request->model, $Request->all());
        });
        return $Response->module('Products')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }

    public function postRead(Request $Request, ActionResponse $Response)
    {
    }

    public function select(Request $Request, ActionResponse $Response)
    {
        $Data = $this->service->ofType($Request->type)
        ->when(! empty($Request->exclude_ids), function($service) use($Request) {
            $service->excludeIds($Request->exclude_ids);
        })
        ->search($Request->search ?? null)
        ->paginate(50);
        return $this->service->toSelect2($Data, [
            'nullable' => $Request->nullable == 'N' ? false : true,
        ]);
    }

    public function postStatus(Request $Request, ActionResponse $Response)
    {
        DB::transaction(function() use($Request) {
            $this->service->toggleStatus($Request->model, $Request->status);
        });
        return $Response->module('Products')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.status_updated.title'),
                'description' => __('ui::strings.messages.status_updated.description'),
            ],
        ]);
    }

    public function postDelete(Request $Request, ActionResponse $Response)
    {
        DB::transaction(function() use($Request) {
            $this->service->softDelete($Request->model);
        });
        return $Response->module('Products')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.deleted.title'),
                'description' => __('ui::strings.messages.deleted.description'),
            ],
        ]);
    }

    public function postRestore(Request $Request, ActionResponse $Response)
    {
        DB::transaction(function() use($Request) {
            $this->service->restore($Request->model);
        });
        return $Response->module('Products')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.restored.title'),
                'description' => __('ui::strings.messages.restored.description'),
            ],
        ]);
    }

    public function postPermaDelete(Request $Request, ActionResponse $Response)
    {
        DB::transaction(function() use($Request) {
            $this->service->permaDelete($Request->model);
        });
        return $Response->module('Products')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.permaDeleted.title'),
                'description' => __('ui::strings.messages.permaDeleted.description'),
            ],
        ]);
    }
}
