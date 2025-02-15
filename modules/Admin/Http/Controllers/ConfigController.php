<?php

namespace Modules\Admin\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\SystemCore\Entities\Contracts\SystemConfigInterface;

class ConfigController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'admin::config';

    public $service          = null;

    public function __construct(SystemConfigInterface $SystemConfigService)
    {
        $this->service = $SystemConfigService;
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('read'))->only(['index', 'postIndex', 'read', 'postRead']);
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('create'))->only(['create', 'postCreate']);
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('update'))->only(['update', 'postUpdate']);
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('statusUpdate'))->only(['postStatus']);
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('delete'))->only(['postDelete']);
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('restore'))->only(['postRestore']);
        $this->middleware(config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') .':'. $this->service->permissionNamespace('permaDelete'))->only(['postPermaDelete']);
    }

    public function update(Request $Request, ActionResponse $Response)
    {
        $this->data['inputs'] = $this->service->getConfigs();
        $this->service->load();
        $this->data['service'] = $this->service;
        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function postUpdate(Request $Request, ActionResponse $Response)
    {
        $this->data['Validator'] = $this->service->validator($Request->all(), 'update');
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request) {
            $this->service->save($Request->config);
        });
        return $Response->module('Products')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }
}
