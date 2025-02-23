<?php

namespace Modules\Clients\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\Clients\Entities\ClientService;
use Modules\Admin\Entities\RoleService;
use Carbon\Carbon;

class ClientsController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'clients::clients';

    public function __construct(ClientService $ClientService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware($ClientService->getPermissionMiddlewareName('HTTP', 'read'))->only(['index', 'postIndex', 'read', 'postRead']);
        $this->middleware($ClientService->getPermissionMiddlewareName('HTTP', 'create'))->only(['create', 'postCreate']);
        $this->middleware($ClientService->getPermissionMiddlewareName('HTTP', 'update'))->only(['update', 'postUpdate']);
        $this->middleware($ClientService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['postStatus']);
        $this->middleware($ClientService->getPermissionMiddlewareName('HTTP', 'delete'))->only(['postDelete']);
        $this->middleware($ClientService->getPermissionMiddlewareName('HTTP', 'restore'))->only(['postRestore']);
        $this->middleware($ClientService->getPermissionMiddlewareName('HTTP', 'permaDelete'))->only(['postPermaDelete']);
    }

    public function index(Request $Request, ActionResponse $Response, RoleService $RoleService, ClientService $ClientService)
    {
        $this->data['Service'] = $ClientService;
        $this->data['Roles']   = $RoleService->get()->data();
        if (config('admin.controll-panel-users-list-filter')) {
            $this->data['Roles'] = $this->data['Roles']->whereNotIn('name', config('admin.controll-panel-users-list-groups'));
        }
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response, RoleService $RoleService)
    {
        $this->data['Roles'] = $RoleService->get()->data();
        if (config('admin.controll-panel-users-list-filter')) {
            $this->data['Roles'] = $this->data['Roles']->whereNotIn('name', config('admin.controll-panel-users-list-groups'));
        }
        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function update(Request $Request, ActionResponse $Response, RoleService $RoleService, ClientService $ClientService)
    {
        $this->data['model']  = $ClientService->findOrFail($Request->model)->data();
        $this->data['model']->load([
            'company' => fn($rel) => $rel->withTrashed(),
        ]);
        if (\Module::installedAndEnabled('Branches')) {
            $this->data['model']->load('Branch');
        }
        $this->data['Roles'] = $RoleService->get()->data();
        if (config('admin.controll-panel-users-list-filter')) {
            $this->data['Roles'] = $this->data['Roles']->whereNotIn('name', config('admin.controll-panel-users-list-groups'));
        }
        
        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response, ClientService $ClientService)
    {
        $this->data['model'] = $ClientService->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function profile(Request $Request, ActionResponse $Response, ClientService $ClientService)
    {
        $this->data['model'] = auth()->user();
        return view($this->viewsFolderPath . '.profile', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, ClientService $ClientService)
    {
        return $ClientService->when($Request->records_type == 'TRASHED', function($ClientService) {
            $ClientService->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }

    public function postCreate(Request $Request, ActionResponse $Response, ClientService $ClientService)
    {
        $this->data['Validator'] = $ClientService->validator('create', $Request->all());
        $this->data['Validator']->validate();
        $model = DB::transaction(function() use($Request, $ClientService) {
            return $ClientService->create($Request->all());
        });
        $ClientService->apiSendCreatePasswordEmail($model->id);

        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('ClientsController@index')
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response, ClientService $ClientService)
    {
        $this->data['model']     = $ClientService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['Validator'] = $ClientService->validator('update', $Request->all());
        $this->data['Validator']->validate();
        $model = DB::transaction(function() use($Request, $ClientService) {
            return $ClientService->update($Request->all());
        });
        // if ($Request->email != $model->email) {
            $ClientService->apiSendCreatePasswordEmail($model->id);
        // }

        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }

    public function postRead(Request $Request, ActionResponse $Response, ClientService $ClientService)
    {
    }

    public function select(Request $Request, ActionResponse $Response, ClientService $ClientService)
    {
        $ClientService->unguard();
        return $ClientService->ajaxSelect($Request->search                             ?? null, [
            'nullable'                     => $Request->nullable                     ?? true,
            'type'                         => $Request->type                         ?? null,
            'has_no_scheduals'             => $Request->has_no_scheduals             ?? null,
            'has_no_scheduals_exclude_ids' => $Request->has_no_scheduals_exclude_ids ?? [],
        ]);
    }

    public function postProfile(Request $Request, ActionResponse $Response, ClientService $ClientService)
    {
        $this->data['model']     = $ClientService->withTrashed()->findOrFail(auth()->user()->id)->data();
        $this->data['Validator'] = $ClientService->validator('updateProfile', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $ClientService) {
            $ClientService->updateProfile($Request->all());
        });

        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }

    public function postStatus(Request $Request, ActionResponse $Response, ClientService $ClientService)
    {
        $this->data['model'] = $ClientService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ClientService) {
            $ClientService->statusUpdate($Request->status);
        });

        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.status_updated.title'),
                'description' => __('ui::strings.messages.status_updated.description'),
            ],
        ]);
    }

    public function postDelete(Request $Request, ActionResponse $Response, ClientService $ClientService)
    {
        $this->data['model'] = $ClientService->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ClientService) {
            $ClientService->delete();
        });
        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.deleted.title'),
                'description' => __('ui::strings.messages.deleted.description'),
            ],
        ]);
    }

    public function postRestore(Request $Request, ActionResponse $Response, ClientService $ClientService)
    {
        $this->data['model'] = $ClientService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ClientService) {
            $ClientService->restore();
        });
        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.restored.title'),
                'description' => __('ui::strings.messages.restored.description'),
            ],
        ]);
    }

    public function postPermaDelete(Request $Request, ActionResponse $Response, ClientService $ClientService)
    {
        $this->data['model'] = $ClientService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ClientService) {
            $ClientService->permaDelete();
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
