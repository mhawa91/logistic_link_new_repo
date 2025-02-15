<?php

namespace Modules\ContactUs\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContactUs\Entities\ContactUsService;

class ContactUsController extends SystemCoreHttpController
{
    public $viewsFolderPath  = 'contactus::contact_us';

    public function __construct(ContactUsService $ContactUsService)
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
        $this->middleware($ContactUsService->getPermissionMiddlewareName('HTTP', 'read'))->only(['index', 'read']);
        $this->middleware($ContactUsService->getPermissionMiddlewareName('HTTP', 'create'))->only(['create']);
        $this->middleware($ContactUsService->getPermissionMiddlewareName('HTTP', 'update'))->only(['update']);
        // $this->middleware($ContactUsService->getPermissionMiddlewareName('HTTP', 'statusUpdate'))->only(['postStatus']);
        // $this->middleware($ContactUsService->getPermissionMiddlewareName('HTTP', 'delete'))->only(['postDelete']);
        // $this->middleware($ContactUsService->getPermissionMiddlewareName('HTTP', 'restore'))->only(['postRestore']);
        // $this->middleware($ContactUsService->getPermissionMiddlewareName('HTTP', 'permaDelete'))->only(['postPermaDelete']);
    }

    public function index(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $this->data['Service'] = $ContactUsService;
        return view($this->viewsFolderPath . '.index', $this->data);
    }

    public function create(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        return view($this->viewsFolderPath . '.create', $this->data);
    }

    public function update(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $this->data['model'] = $ContactUsService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.update', $this->data);
    }

    public function read(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $this->data['model'] = $ContactUsService->withTrashed()->findOrFail($Request->model)->data();
        return view($this->viewsFolderPath . '.read', $this->data);
    }

    public function postIndex(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $ContactUsService->unguard();
        return $ContactUsService->when($Request->records_type == 'TRASHED', function($service) {
            $service->onlyTrashed();
        })->dataTable($Request->search['value'] ?? $Request->advanced_search ?? null)->toJson();
    }

    public function postCreate(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $ContactUsService->unguard();
        $this->data['Validator'] = $ContactUsService->validator('create', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $ContactUsService) {
            $ContactUsService->create($Request->all());
        });
        return $Response->module('ContactUs')->code('200')->success()->withOnlyToAjaxWeb([
            'redirect' => route('ContactUsController@index')
        ]);
    }

    public function postUpdate(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $ContactUsService->unguard();
        $this->data['model']     = $ContactUsService->withTrashed()->findOrFail($Request->model)->data();
        $this->data['Validator'] = $ContactUsService->validator('update', $Request->all());
        $this->data['Validator']->validate();
        DB::transaction(function() use($Request, $ContactUsService) {
            $ContactUsService->update($Request->all());
        });
        return $Response->module('ContactUs')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
        ]);
    }

    public function postRead(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $ContactUsService->unguard();
        $ContactRequest = $ContactUsService->withTrashed()->findOrFail($Request->model)->data();
        return [
            'id'                 => $ContactRequest->id,
            'created_by_user_id' => $ContactRequest->created_by_user_id,
            'created_at'         => Carbon::parse($ContactRequest->created_at)->format('d/m/Y g:i A'),
            'name'               => e(strip_tags($ContactRequest->name)),
            'email'              => e(strip_tags($ContactRequest->email)),
            'phone_number'       => e(strip_tags($ContactRequest->phone_number)),
            'subject'            => e(strip_tags($ContactRequest->subject)),
            'message'            => strip_tags(nl2br($ContactRequest->message), '<br>'),
            'sys_status'         => $ContactRequest->sys_status,
            'status'             => [
                'label' => $ContactRequest->sys_status == 'PENDING' ? __('contactus::inputs.status.options.PENDING') : __('contactus::inputs.status.options.REVIEWED'),
                'color' => $ContactRequest->sys_status == 'PENDING' ? 'warning'                                      : 'success',
            ],
        ];
    }

    public function select(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $ContactUsService->unguard();
        return $ContactUsService->ajaxSelect($Request->search ?? null, [
            'nullable' => true,
        ], $Request->page);
    }

    public function postStatus(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $ContactUsService->unguard();
        $this->data['model'] = $ContactUsService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ContactUsService) {
            $ContactUsService->statusUpdate($Request->status);
        });
        return $Response->module('ContactUs')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.status_updated.title'),
                'description' => __('ui::strings.messages.status_updated.description'),
            ],
        ]);
    }

    public function postDelete(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $ContactUsService->unguard();
        $this->data['model'] = $ContactUsService->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ContactUsService) {
            $ContactUsService->delete();
        });
        return $Response->module('ContactUs')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.deleted.title'),
                'description' => __('ui::strings.messages.deleted.description'),
            ],
        ]);
    }

    public function postRestore(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $ContactUsService->unguard();
        $this->data['model'] = $ContactUsService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ContactUsService) {
            $ContactUsService->restore();
        });
        return $Response->module('ContactUs')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.restored.title'),
                'description' => __('ui::strings.messages.restored.description'),
            ],
        ]);
    }

    public function postPermaDelete(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $ContactUsService->unguard();
        $this->data['model'] = $ContactUsService->withTrashed()->findOrFail($Request->model)->data();
        DB::transaction(function() use($Request, $ContactUsService) {
            $ContactUsService->permaDelete();
        });
        return $Response->module('ContactUs')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.permaDeleted.title'),
                'description' => __('ui::strings.messages.permaDeleted.description'),
            ],
        ]);
    }
}
