<?php

namespace Modules\Admin\Http\Controllers;

use DB;
use Exception;
use Validator;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\Admin\Entities\UserService;
use Modules\Admin\Entities\RoleService;

class UsersController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'admin::users';

    public function __construct(UserService $UserService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware($UserService->getPermissionMiddlewareName('HTTP', 'read'))->only(['index', 'postIndex', 'read', 'postRead']);
        $this->middleware($UserService->getPermissionMiddlewareName('HTTP', 'create'))->only(['create', 'postCreate']);
        $this->middleware($UserService->getPermissionMiddlewareName('HTTP', 'update'))->only(['update', 'postUpdate']);
        $this->middleware($UserService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['postStatus']);
        $this->middleware($UserService->getPermissionMiddlewareName('HTTP', 'delete'))->only(['postDelete']);
        $this->middleware($UserService->getPermissionMiddlewareName('HTTP', 'restore'))->only(['postRestore']);
        $this->middleware($UserService->getPermissionMiddlewareName('HTTP', 'permaDelete'))->only(['postPermaDelete']);
    }

    public function index(Request $Request, ActionResponse $Response, RoleService $RoleService, UserService $UserService)
    {
        $this->data['Service'] = $UserService;
        $this->data['Roles']   = $RoleService->get()->data();
        if (config('admin.controll-panel-users-list-filter')) {
            $this->data['Roles'] = $this->data['Roles']->whereIn('name', config('admin.controll-panel-users-list-groups'));
        }
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response, RoleService $RoleService)
    {
        $this->data['Roles'] = $RoleService->get()->data();
        if (config('admin.controll-panel-users-list-filter')) {
            $this->data['Roles'] = $this->data['Roles']->whereIn('name', config('admin.controll-panel-users-list-groups'));
        }
        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function update(Request $Request, ActionResponse $Response, RoleService $RoleService, UserService $UserService)
    {
        $this->data['model']  = $UserService->findOrFail($Request->model)->data();
        if (\Module::installedAndEnabled('Branches')) {
            $this->data['model']->load('Branch');
        }
        $this->data['Roles'] = $RoleService->get()->data();
        if (config('admin.controll-panel-users-list-filter')) {
            $this->data['Roles'] = $this->data['Roles']->whereIn('name', config('admin.controll-panel-users-list-groups'));
        }

        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response, UserService $UserService)
    {
        $this->data['model'] = $UserService->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function profile(Request $Request, ActionResponse $Response, UserService $UserService)
    {
        $this->data['model'] = auth()->user();
        return view($this->viewsFolderPath . '.profile', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, UserService $UserService)
    {
        return $UserService->when($Request->records_type == 'TRASHED', function($UserService) {
            $UserService->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }

    public function postCreate(Request $Request, ActionResponse $Response, UserService $UserService)
    {
        $this->data['Validator'] = $UserService->validator('create', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $UserService) {
            $UserService->create($Request->all());
        });

        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('UsersController@index')
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response, UserService $UserService)
    {
        $this->data['model']     = $UserService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['Validator'] = $UserService->validator('update', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $UserService) {
            $UserService->update($Request->all());
        });

        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }

    public function postRead(Request $Request, ActionResponse $Response, UserService $UserService)
    {
    }

    public function select(Request $Request, ActionResponse $Response, UserService $UserService)
    {
        $UserService->unguard();
        return $UserService->ajaxSelect($Request->search                             ?? null, [
            'nullable'                     => $Request->nullable                     ?? true,
            'type'                         => $Request->type                         ?? null,
            'has_no_scheduals'             => $Request->has_no_scheduals             ?? null,
            'has_no_scheduals_exclude_ids' => $Request->has_no_scheduals_exclude_ids ?? [],
        ]);
    }

    public function postProfile(Request $Request, ActionResponse $Response, UserService $UserService)
    {
        $this->data['model']     = $UserService->withTrashed()->findOrFail(auth()->user()->id)->data();
        $this->data['Validator'] = $UserService->validator('updateProfile', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $UserService) {
            $UserService->updateProfile($Request->all());
        });

        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }

    public function postStatus(Request $Request, ActionResponse $Response, UserService $UserService)
    {
        $this->data['model'] = $UserService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $UserService) {
            $UserService->statusUpdate($Request->status);
        });

        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.status_updated.title'),
                'description' => __('ui::strings.messages.status_updated.description'),
            ],
        ]);
    }

    public function postDelete(Request $Request, ActionResponse $Response, UserService $UserService)
    {
        $this->data['model'] = $UserService->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $UserService) {
            $UserService->delete();
        });
        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.deleted.title'),
                'description' => __('ui::strings.messages.deleted.description'),
            ],
        ]);
    }

    public function postRestore(Request $Request, ActionResponse $Response, UserService $UserService)
    {
        $this->data['model'] = $UserService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $UserService) {
            $UserService->restore();
        });
        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.restored.title'),
                'description' => __('ui::strings.messages.restored.description'),
            ],
        ]);
    }

    public function postPermaDelete(Request $Request, ActionResponse $Response, UserService $UserService)
    {
        $this->data['model'] = $UserService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $UserService) {
            $UserService->permaDelete();
        });
        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.permaDeleted.title'),
                'description' => __('ui::strings.messages.permaDeleted.description'),
            ],
        ]);
    }
}
