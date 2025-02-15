<?php

namespace Modules\ContentManager\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContentManager\Entities\Contracts\ContentInterface;
use Modules\ContentManager\Entities\Contracts\CategoryInterface;
use Modules\ContentManager\Entities\Tag;

class ContentController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'contentmanager::contents';

    public $service          = null;

    public function __construct(ContentInterface $ContentService)
    {
        $this->service = $ContentService;
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
        $this->data['type']       = $Request->type;
        $this->data['Service']    = $this->service;
        $this->data['LoadedType'] = $this->service->getType($this->data['type']);
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response)
    {
        $this->data['type']       = $Request->type;
        $this->data['LoadedType'] = $this->service->getType($this->data['type']);
        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function update(Request $Request, ActionResponse $Response, CategoryInterface $CategoryService)
    {
        $this->data['model']      = $this->service->withTrashed()->find($Request->model);
        $this->data['model']->load('Tags.translations', 'Categories.translations');
        $this->data['type']       = $this->data['model']->type;
        $this->data['LoadedType'] = $this->service->getType($this->data['type']);
        $this->data['categories'] = $this->data['model']->Categories->map(function($Category) use($CategoryService) {
            return $CategoryService->toSelect2Option($Category);
        })->toArray();
        $this->data['tags'] = $this->data['model']->Tags->map(function($Tag) {
            return $this->service->toSelect2Option($Tag);
        })->toArray();
        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response)
    {
        $this->data['model']      = $this->service->withTrashed()->find($Request->model);
        $this->data['type']       = $this->data['model']->type;
        $this->data['LoadedType'] = $this->service->getType($this->data['type']);
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response)
    {
        return $this->service
        ->oftype($Request->type)
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
            'redirect' => route('ContentController@index', ['type' => $Request->type])
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
        $Data = $this->service->ofType($Request->type)->search($Request->search ?? null)->paginate(50);
        return $this->service->toSelect2($Data, [
            'nullable' => true,
        ]);
    }

    public function tagSelect(Request $Request, ActionResponse $Response)
    {
        $Data = Tag::with('translations')->search($Request->search ?? null)->paginate(50);
        return $this->service->toSelect2($Data, [
            'nullable' => 'false',
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
