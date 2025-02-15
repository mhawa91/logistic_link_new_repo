<?php

namespace Modules\ContentManager\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContentManager\Entities\Contracts\SectionInterface;

class SectionController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'contentmanager::sections';

    public $service          = null;

    public function __construct(SectionInterface $SectionService)
    {
        $this->service = $SectionService;
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
        $this->data['inputs'] = $this->service->get();
        $this->service->loadTranslated();
        $this->data['service'] = $this->service;
        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function postUpdate(Request $Request, ActionResponse $Response)
    {
        $this->data['Validator'] = $this->service->validator($Request->all(), 'update');
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request) {
            $this->service->loadTranslated();
            $this->service->save($Request->sections);
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
