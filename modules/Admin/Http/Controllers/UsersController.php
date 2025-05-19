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
use Modules\SystemCore\Entities\Role;

use Modules\ContentTypes\Entities\Company;
use Modules\ContentTypes\Entities\Address;

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
        if ($this->data['model']->type == 'DEFAULT' || $this->data['model']->type == 'TRANSPORTER') {
            $this->data['model']->load(['company' => function($rel) {
                $rel->withTrashed();
                $rel->with(['address' => function($rel) {
                    $rel->withTrashed();
                    $rel->with(['city' => function($rel) {
                        $rel->withTrashed();
                        $rel->with(['country' => function($rel) {
                            $rel->withTrashed();
                        }]);
                    }]);
                }]);
            }]);
            $this->data['registerationTypes'] = \Modules\ContentManager\Entities\Content::where('type', 'registration-type')->with('translations')->get();
            return view($this->viewsFolderPath . '.company_profile', $this->data);
        }
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

    public function postCompany(Request $Request, ActionResponse $Response, UserService $UserService)
    {        
        $this->data['UserData']  = [
            'name'               => $Request->contact_name,
            'username'           => null,
            'phone_number'       => null,
            'email'              => $Request->company_email,
            'avatar'             => $Request->company_logo,
            'locale'             => app()->getLocale(),
            'type'               => auth()->user()->type,
            'completed_at'       => now()->format('Y-m-d H:i:s'),
            'reviewd_at'         => null,
            'created_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'updated_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'sys_status'         => 'ACTIVE',
            'bio'                => null,
        ];
        $UserService->unguard();
        $this->data['model']     = $UserService->withTrashed()->findOrFail(auth()->user()->id)->data();
        $this->data['Validator'] = $UserService->validator('updateProfile', $this->data['UserData']);
        $this->data['Validator']->validate();

        $Company = Company::where('user_id', auth()->user()->id)->first() ?? new Company;
        $this->data['ValidatorCompany'] = Validator::make($Request->all(), [
            'registration_type_category_id' => 'required|exists:contents,id',
            'company_logo'                  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'company_name'                  => 'required|string|max:255',
            'contact_name'                  => 'required|string|max:255',
            'company_email'                 => 'required|email|max:255',
            'company_phone_number'          => 'required|string|max:255',
            'registration_number'           => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',
            'tax_identification_number'     => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',
            'country_id'                    => 'required|exists:countries,id',
            'city_id'                       => 'required|exists:cities,id',
            'street'                        => 'required|string|max:255',
            'lat_lng'                       => 'required|string|max:255',
            'full_address'                  => 'required|string|max:255',
        ]);
        $this->data['ValidatorCompany']->validate();

        DB::transaction(function() use($Request, $UserService, $Company) {
            $phone = strtolower($Request->company_phone_number);
            $phone = app(\Modules\SystemCore\System\Classes\Helpers::class)->phoneNumberParts($phone);
            $UserService->updateProfile($this->data['UserData']);
            
            $Role    = Role::where('name', auth()->user()->type)->first();
            
            if ($Company->company_logo && $Request->company_logo && !is_string($Request->company_logo)) {
                app()->make('GraphManager')->remove( $Company->company_logo );
            }
            if ($Request->company_logo && !is_string($Request->company_logo)) {
                $Company->company_logo               = app('GraphManager')->process('company', $Request->company_logo);
            }
            if ($Company->registration_number && $Request->registration_number && !is_string($Request->registration_number)) {
                app()->make('GraphManager')->remove( $Company->registration_number );
            }
            if ($Request->registration_number && !is_string($Request->registration_number)) {
                $Company->registration_number               = app('GraphManager')->process('files', $Request->registration_number);
            }
            if ($Company->tax_identification_number && $Request->tax_identification_number && !is_string($Request->tax_identification_number)) {
                app()->make('GraphManager')->remove( $Company->tax_identification_number );
            }
            if ($Request->tax_identification_number && !is_string($Request->tax_identification_number)) {
                $Company->tax_identification_number               = app('GraphManager')->process('files', $Request->tax_identification_number);
            }
            $Company->registration_number_string       = $Request->registration_number_string       ?? null;
            $Company->tax_identification_number_string = $Request->tax_identification_number_string ?? null;
            $Company->user_id                          = auth()->id();
            $Company->profile_type_category_id         = $Role->id;
            $Company->registration_type_category_id    = $Request->registration_type_category_id;
            $Company->company_name                     = $Request->company_name;
            $Company->contact_name                     = $Request->contact_name;
            $Company->company_email                    = $Request->company_email;
            $Company->company_phone_number_ext         = $phone ? $phone->ext    : null;
            $Company->company_phone_number             = $phone ? $phone->number : null;
            $Company->sys_status                       = 'ACTIVE';
            $Company->created_by_user_id               = auth()->id() ?? null;
            $Company->updated_by_user_id               = auth()->id() ?? null;
            $Company->save();

            $latlng  = explode(',', $Request->lat_lng);
            $Address = Address::where('related_id', $Company->id)->where('related_type', 'PRIMARY_COMPANY_ADDRESS')->first() ?? new Address;
            $Address->related_id                       = $Company->id;
            $Address->related_type                     = 'PRIMARY_COMPANY_ADDRESS';
            $Address->address_tag                      = 'PRIMARY_COMPANY_ADDRESS';
            $Address->primary_contact_name             = $Request->contact_name;
            $Address->primary_contact_email            = $Request->company_email;
            $Address->primary_contact_phone_number_ext = $Company->company_phone_number_ext;
            $Address->primary_contact_phone_number     = $Company->company_phone_number;
            $Address->country_id                       = $Request->country_id;
            $Address->city_id                          = $Request->city_id;
            $Address->street                           = $Request->street;
            $Address->lat                              = $latlng[0] ?? null;
            $Address->lng                              = $latlng[1] ?? null;
            $Address->full_address                     = $Request->full_address;
            $Address->sys_status                       = 'ACTIVE';
            $Address->created_by_user_id               = auth()->check() ? auth()->user()->id : null;
            $Address->updated_by_user_id               = auth()->check() ? auth()->user()->id : null;
            $Address->save();
        });

        return $Response->module('Admin')->code('200')->success()->withOnlyToAjaxWeb([
            'toastr' => [
                'type'        => 'success',
                'title'       => __('ui::strings.messages.saved.title'),
                'description' => __('ui::strings.messages.saved.description'),
            ],
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
