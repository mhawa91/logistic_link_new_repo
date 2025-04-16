<?php

namespace Modules\Clients\Entities;

use Validator;
use Hash;
use Bouncer;
use DataTables;
use Modules\SystemCore\System\Services\ModelService;
use Illuminate\Support\Facades\Http;

class ClientService extends ModelService
{
    public function register() 
    {
        $this->modelNamespace = config('systemcore.models.User');
        $this->guard();
        if ($this->can('update')) {
            $this->registerDataTableAction('update', function($Row) {
                return [
                    'label'  => __('ui::strings.update'),
                    'action' => route('ClientsController@update', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('delete')) {
            $this->registerDataTableAction('delete', function($Row) {
                return $Row->trashed() ? null : [
                    'label'  => __('ui::strings.delete'),
                    'action' => route('ClientsController@postDelete', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('restore')) {
            $this->registerDataTableAction('restore', function($Row) {
                return ! $Row->trashed() ? null : [
                    'label'  => __('ui::strings.restore'),
                    'action' => route('ClientsController@postRestore', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('permaDelete')) {
            $this->registerDataTableAction('permaDelete', function($Row) {
                return [
                    'label'  => __('ui::strings.perma_delete'),
                    'action' => route('ClientsController@postPermaDelete', ['model' => $Row->id]),
                ];
            });
        }
        $this->registerAjaxSelectCallback(function($Row) {
            return [
                'id'   => ! $Row ? '0'            : $Row->id,
                'text' => ! $Row ? '-- Select --' : ($Row->name ? $Row->name : ($Row->first_name ? $Row->first_name . ' ' . $Row->last_name : 'N/A')) . ' | ' . ($Row->email ?? $Row->phone_number ?? $Row->username),
                'data' => ! $Row ? []             : $Row->toArray(),
            ];
        });
    }

    public function can(string $permission)
    {
        if (! $this->isGuarded()) {
            return true;
        }
        if (! auth()->check()) {
            return false;
        }
        if (auth()->user()->isAn('SYSTEM_ADMIN') && $permission != 'permaDelete') {
            return true;
        }
        return auth()->user()->isAn('ROOT') ?? auth()->user()->can($this->getPermissionName($permission));
    }

    public function validator($type = 'create', array $data = [])
    {
        if ($type == 'create') {
            return Validator::make($data, [
                'name'                  => 'required',
                // 'first_name'            => 'required',
                // 'last_name'             => 'required',
                'username'              => 'nullable|required_without_all:phone_number,email|unique:users,username',
                'phone_number'          => 'nullable|required_without_all:username,email|unique:users,phone_number',
                'email'                 => 'nullable|required_without_all:username,phone_number|email|unique:users,email',
                'password'              => 'required|confirmed',
                'locale'                => 'required',
                'branch_id'             => 'nullable|exists:branches,id',
                'bio'                   => 'nullable|max:400',
            ]);
        }
        if ($type == 'update') {
            return Validator::make($data, [
                'name'                  => 'required',
                // 'first_name'            => 'required',
                // 'last_name'             => 'required',
                'username'              => 'nullable|required_without_all:phone_number,email|unique:users,username,'       . $this->data->id,
                'phone_number'          => 'nullable|required_without_all:username,email|unique:users,phone_number,'       . $this->data->id,
                'email'                 => 'nullable|required_without_all:username,phone_number|email|unique:users,email,' . $this->data->id,
                'password'              => 'nullable|confirmed',
                'locale'                => 'required',
                'branch_id'             => 'nullable|exists:branches,id',
                'bio'                   => 'nullable|max:400',
            ]);
        }
        if ($type == 'updateProfile') {
            $validator = Validator::make($data, [
                'name'                  => 'required',
                // 'first_name'            => 'required',
                // 'last_name'             => 'required',
                'username'              => 'nullable|required_without_all:phone_number,email|unique:users,username,'       . $this->data->id,
                'phone_number'          => 'nullable|required_without_all:username,email|unique:users,phone_number,'       . $this->data->id,
                'email'                 => 'nullable|required_without_all:username,phone_number|email|unique:users,email,' . $this->data->id,
                'current_password'      => 'nullable|required_with:new_password',
                'new_password'          => 'nullable|confirmed',
                'locale'                => 'required',
                'bio'                   => 'nullable|max:400',
            ]);
            $validator->after(function($validator) use($data) {
                if (! empty($data['new_password']) ) {
                    if (! Hash::check($data['current_password'], $this->data->password)) {
                        $validator->errors()->add(
                            'type' , 'Please check your current account password'
                        );
                    }
                }
            });
            return $validator;
        }
    }

    public function findByAny(string $string)
    {
        $this->model()->where(function ($User) use($string) {
            $User->where('id', strtolower($string));
            $User->orWhere('phone_number', app(\Modules\SystemCore\System\Classes\Helpers::class)->parsePhoneNumber(strtolower($string)));
            $User->orWhere('username', strtolower($string));
            $User->orWhere('email', strtolower($string));
        });

        return $this->firstOrFail()->data();
    }

    public function findById(string $id)
    {
        return $this->findOrFail($id)->data();
    }

    protected function keywordFilter($query, string $keyword = null)
    {
        $query->where(function($Model) use($keyword) {
            $Model->where('name'           , 'like', "%{$keyword}%");
            // $Model->orWhereRaw('CONCAT_WS(" ", first_name, last_name) LIKE ?' ,"%{$keyword}%");
            $Model->orWhere('email'        , 'like', "%{$keyword}%");
            $Model->orWhere('username'     , 'like', "%{$keyword}%");
            $Model->orWhere('phone_number' , 'like', "%{$keyword}%");
            $Model->orWhereHas('roles' , function($Rel) use($keyword) {
                $Rel->whereHas('translations', function($Rel) use($keyword) {
                    $Rel->where('title', 'like', "%{$keyword}%");
                });
            });
        });
    }

    protected function advancedFilter($query, array $filter = [])
    {
        $query->AdvancedSearch($filter);

        $query->where(function($query) use($filter) {
            if (! empty($filter['country_id'])) {
                $query->whereHas('company.address', function($Rel) use($filter) {
                    if (is_array($filter['country_id'])) {
                        $query->whereIn('country_id', (array) $filter['country_id']);
                    } else {
                        $query->where('country_id', $filter['country_id']);
                    }
                });
            }
            if (! empty($filter['city_id'])) {
                $query->whereHas('company.address', function($Rel) use($filter) {
                    if (is_array($filter['city_id'])) {
                        $query->whereIn('city_id', (array) $filter['city_id']);
                    } else {
                        $query->where('city_id', $filter['city_id']);
                    }
                });
            }
        });
    }

    public function dataTable($Filter = null)
    {
        $this->model()->select([
            '*',
            // DB::raw('
            //     DATE_FORMAT(created_at, "%Y-%m-%d") AS new_created_at,
            //     DATE_FORMAT(updated_at, "%Y-%m-%d") AS new_updated_at
            // '),
            // DB::raw('(CASE 
            //     WHEN status = "ACTIVE"   THEN "'. __('admin::strings.active') .'"
            //     WHEN status = "DISABLED" THEN "'. __('admin::strings.disabled') .'"
            //     ELSE "-----"
            // END) AS new_status')
            // \DB::raw('
            //     CONVERT_TZ(created_at, "-03:00", "SYSTEM") AS ist_created_at,
            //     DATE_FORMAT(CONVERT_TZ(created_at, "-03:00", "SYSTEM"), "%Y-%m-%d") AS ist_created_at_date,
            //     DATE_FORMAT(CONVERT_TZ(created_at, "-03:00", "SYSTEM"), "%h:%i %p") AS ist_created_at_time_ampm
            // '),
            // \DB::raw('
            //     CONCAT_WS(" ", first_name, last_name) AS name
            // '),
            \DB::raw('
                DATE_FORMAT(created_at, "%Y-%m-%d %h:%i %p") AS ist_created_at,
                DATE_FORMAT(created_at, "%Y-%m-%d") AS ist_created_at_date,
                DATE_FORMAT(created_at, "%h:%i %p") AS ist_created_at_time_ampm
            '),
        ])
        ->with('Type.translations')
        ->with([
            'company'         => function($rel) {
                $rel->withTrashed();
                $rel->with([
                    'address' => function($rel) {
                        $rel->withTrashed();
                        $rel->with([
                            'country' => function($rel) {
                                $rel->withTrashed();
                            },
                            'city' => function($rel) {
                                $rel->withTrashed();
                            },
                        ]);
                    },
                ]);
            },
        ]);
        if (auth()->check() && ! auth()->user()->isAn('ROOT')) {
            $this->model()->where('type', '!=', 'ROOT');
        }
        if (config('admin.controll-panel-users-list-filter')) {
            $this->model()->whereNotIn('type', config('admin.controll-panel-users-list-groups'));
        }
        if (\Module::installedAndEnabled('Branches')) {
            $this->model()->with('Branch');
        }
        $table = parent::dataTable($Filter);

        $table->addColumn('avatar_src', function($Model) {
            return app('GraphManager')->src('400x400', 'user', $Model->avatar);
        });

        return $table;
    }

    public function create(array $data = [])
    {
        $data = array_merge([
            'name'               => null,
            // 'first_name'         => null,
            // 'last_name'          => null,
            'name'               => null,
            'username'           => null,
            'phone_number'       => null,
            'email'              => null,
            'avatar'             => null,
            'password'           => null,
            'locale'             => app()->getLocale(),
            'type'               => 'NORMAL_USER',
            'completed_at'       => null,
            'reviewd_at'         => null,
            'created_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'updated_by_user_id' => null,
            'sys_status'         => 'ACTIVE',
            'bio'                => null,
        ], $data);
        $data['name']         = strtolower($data['name']);
        // $data['first_name']   = strtolower($data['first_name']);
        // $data['last_name']    = strtolower($data['last_name']);
        $data['email']        = strtolower($data['email']);
        if (! empty($data['username'])) {
            $data['username'] = strtolower($data['username']);
        }
        if (! empty($data['phone_number'])) {
            $data['phone_number'] = strtolower($data['phone_number']);
            $data['phone_number'] = app(\Modules\SystemCore\System\Classes\Helpers::class)->parsePhoneNumber($data['phone_number']);
        }
        $data['password'] = empty($data['password']) ? null : Hash::make($data['password']);
        if (! empty($data['avatar']) && !is_string($data['avatar'])) {
            // $data['avatar'] = $data['avatar']->store('user', 'graph');
            $data['avatar'] = app('GraphManager')->process('user', $data['avatar']);
        }
        if (\Module::installedAndEnabled('Branches')) { 
            $data['branch_id'] = empty($data['branch_id']) ? null : ($data['branch_id'] == 0 ? null : $data['branch_id']);
        }
        $model = parent::create( $data )->data();
        Bouncer::assign($data['type'])->to($model);
        return $model;
    }

    public function update(array $data = [])
    {
        $data = array_merge([
            'name'               => $this->data->name,
            // 'first_name'         => $this->data->first_name,
            // 'last_name'          => $this->data->last_name,
            'name'               => $this->data->name,
            'username'           => $this->data->username,
            'phone_number'       => $this->data->phone_number,
            'email'              => $this->data->email,
            'avatar'             => $this->data->avatar,
            'password'           => $this->data->password,
            'locale'             => $this->data->locale ?? app()->getLocale(),
            'type'               => null,
            'completed_at'       => $this->data->completed_at,
            'reviewd_at'         => $this->data->reviewd_at,
            'updated_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'sys_status'         => $this->data->sys_status,
            'bio'                => $this->data->bio,
        ], $data);
        $data['name']         = strtolower($data['name']);
        // $data['first_name']   = strtolower($data['first_name']);
        // $data['last_name']    = strtolower($data['last_name']);
        $data['email']        = strtolower($data['email']);
        if (! empty($data['username'])) {
            $data['username'] = strtolower($data['username']);
        }
        if (! empty($data['phone_number'])) {
            $data['phone_number'] = app(\Modules\SystemCore\System\Classes\Helpers::class)->parsePhoneNumber(strtolower($data['phone_number']));
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        else {
            $data['password'] = Hash::make($data['password']);
        }
        if (! empty($data['avatar']) && !is_string($data['avatar'])) {
            if (! empty( $oldImage = $this->data->avatar )) {
                app()->make('GraphManager')->remove( $oldImage );
            }
            // $data['avatar'] = $data['avatar']->store('user', 'graph');
            $data['avatar'] = app('GraphManager')->process('user', $data['avatar']);
        }
        if (isset($data['remove_avatar'])) {
            if ($data['remove_avatar'] == 'Y') {
                if (! empty( $oldImage = $this->data->avatar )) {
                    app()->make('GraphManager')->remove( $oldImage );
                }
            }
        }
        if (\Module::installedAndEnabled('Branches')) { 
            $data['branch_id'] = empty($data['branch_id']) ? null : ($data['branch_id'] == 0 ? null : $data['branch_id']);
        }
        $model = parent::update( $data );
        if (!empty($data['type'])) {
            Bouncer::sync( $model->data() )->roles([ $data['type'] ]);
        }
        return $model->data();
    }

    public function updateProfile(array $data = [])
    {
        $this->unguard();
        $data = array_merge([
            'name'               => $this->data->name,
            // 'first_name'         => $this->data->first_name,
            // 'last_name'          => $this->data->last_name,
            'username'           => $this->data->username,
            'phone_number'       => $this->data->phone_number,
            'email'              => $this->data->email,
            'avatar'             => $this->data->avatar,
            'password'           => $this->data->password,
            'locale'             => $this->data->locale,
            'type'               => $this->data->type,
            'completed_at'       => $this->data->completed_at,
            'reviewd_at'         => $this->data->reviewd_at,
            'updated_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'sys_status'         => $this->data->sys_status,
        ], $data);
        $data['name']         = strtolower($data['name']);
        // $data['first_name']   = strtolower($data['first_name']);
        // $data['last_name']    = strtolower($data['last_name']);
        $data['email']        = strtolower($data['email']);
        $data['username']     = strtolower($data['username']);
        $data['phone_number'] = app(\Modules\SystemCore\System\Classes\Helpers::class)->parsePhoneNumber(strtolower($data['phone_number']));
        if (empty($data['new_password'])) {
            unset($data['password']);
        }
        else {
            $data['password'] = Hash::make($data['new_password']);
        }
        if (! empty($data['avatar']) && ! is_string($data['avatar'])) {
            if (! empty( $oldImage = $this->data->avatar )) {
                app()->make('GraphManager')->remove( $oldImage );
            }
            // $data['avatar'] = $data['avatar']->store('user', 'graph');
            $data['avatar'] = app('GraphManager')->process('user', $data['avatar']);
        }
        if (isset($data['remove_avatar'])) {
            if ($data['remove_avatar'] == 'Y') {
                if (! empty( $oldImage = $this->data->avatar )) {
                    app()->make('GraphManager')->remove( $oldImage );
                }
            }
        }
        $model = parent::update( $data );
        return $model->data();
    }

    public function ajaxSelect($filter = null, array $Options = [], int $page = 1, int $limit = 30)
    {
        if (! empty($Options['type'])) {
            if (is_array($Options['type'])) {
                $this->model()->whereIn('type', $Options['type']);
            }
            else {
                $this->model()->where('type', $Options['type']);
            }
        }
        if (! empty($Options['has_no_scheduals'])) {
            $this->model()->where(function($Model) use($Options) {
                $Model->doesnthave('Scheduals');
                if (! empty($Options['has_no_scheduals_exclude_ids'])) {
                    $Model->orWhereIn('id', $Options['has_no_scheduals_exclude_ids']);
                }
            });
        }
        // $this->model()->whereNull('id');
        return parent::ajaxSelect($filter, $Options, $page, $limit);
    }
}