<?php

namespace Modules\SystemCore\Entities;

use Validator;
use DB;
use Modules\SystemCore\Entities\Role;
use Modules\SystemCore\Entities\AbilityGroup;
use Modules\SystemCore\Entities\Ability;
use Modules\SystemCore\System\Services\ModelService;

class MunicipalitiesService extends ModelService
{
    public function register()
    {
        $this->modelNamespace = Municipality::class;
        $this->guard();
        if ($this->can('update')) {
            $this->registerDataTableAction('update', function ($Row) {
                return [
                    'label'  => __('ui::strings.update'),
                    'action' => route('MunicipalitiesController@update', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('delete')) {
            $this->registerDataTableAction('delete', function ($Row) {
                return $Row->trashed() ? null : [
                    'label'  => __('ui::strings.delete'),
                    'action' => route('MunicipalitiesController@postDelete', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('restore')) {
            $this->registerDataTableAction('restore', function ($Row) {
                return ! $Row->trashed() ? null : [
                    'label'  => __('ui::strings.restore'),
                    'action' => route('MunicipalitiesController@postRestore', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('permaDelete')) {
            $this->registerDataTableAction('permaDelete', function ($Row) {
                return [
                    'label'  => __('ui::strings.perma_delete'),
                    'action' => route('MunicipalitiesController@postPermaDelete', ['model' => $Row->id]),
                ];
            });
        }
        $this->registerAjaxSelectCallback(function ($Row) {
            return [
                'id'   => ! $Row ? '0'            : $Row->id,
                'text' => ! $Row ? '-- Select --' : 
                $Row->{'name_' . (in_array(app()->getLocale(), ['ar', 'en']) ? app()->getLocale() : 'en')} 
                . ' - [' 
                . $Row->Country->{'name_' . (in_array(app()->getLocale(), ['ar', 'en']) ? app()->getLocale() : 'en')} . ' - ' 
                . $Row->City->{'name_' . (in_array(app()->getLocale(), ['ar', 'en']) ? app()->getLocale() : 'en')} 
                .']',
                'data' => ! $Row ? []             : $Row->toArray(),
            ];
        });
    }

    public function validator($type = 'create', array $data = [])
    {
        $Rules = [
            'country_id' => 'required|exists:countries,id',
            'city_id'    => 'required|exists:cities,id,country_id,'. ($data['country_id'] ?? '0'),
            'sys_status' => 'nullable|in:DISABLED,ACTIVE',
            'name_ar'    => 'required|max:191',
            'name_en'    => 'required|max:191',
        ];
        if ($type == 'create') {
            return Validator::make($data, $Rules);
        }
        if ($type == 'update') {
            return Validator::make($data, $Rules);
        }
    }

    protected function keywordFilter($query, string $keyword = null)
    {
        $query->where(function($Model) use($keyword) {
            $Model->where('id', $keyword);
            $Model->orWhere('name_ar', 'like', "%{$keyword}%");
            $Model->orWhere('name_en', 'like', "%{$keyword}%");
        });
    }

    protected function advancedFilter($query, array $filter = [])
    {
        if (! empty($filter['country_id'])) {
            $Model->where('country_id', $filter['country_id']);
        }
        if (! empty($filter['city_id'])) {
            $Model->where('city_id', $filter['city_id']);
        }
        if (! empty($filter['name_ar'])) {
            $Model->where('name_ar', 'like', "%{$filter['name_ar']}%");
        }
        if (! empty($filter['name_en'])) {
            $Model->where('name_en', 'like', "%{$filter['name_en']}%");
        }
        if (! empty($filter['sys_status'])) {
            $Model->where('sys_status', $filter['sys_status']);
        }
    }

    public function dataTable($Filter = null)
    {
        $this->model()->select([
            '*',
            DB::raw('
                DATE_FORMAT(created_at, "%Y-%m-%d") AS new_created_at,
                DATE_FORMAT(updated_at, "%Y-%m-%d") AS new_updated_at
            '),
            // DB::raw('(CASE 
            //     WHEN status = "ACTIVE"   THEN "'. __('admin::strings.active') .'"
            //     WHEN status = "DISABLED" THEN "'. __('admin::strings.disabled') .'"
            //     ELSE "-----"
            // END) AS new_status')
        ])
        ->with([
            'Country' => fn($Rel) => $Rel->withTrashed(),
            'City'    => fn($Rel) => $Rel->withTrashed(),
        ]);

        return parent::dataTable($Filter);
    }

    public function create(array $data = [])
    {
        $data = array_merge([
            'created_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'updated_by_user_id' => null,
            'sys_status'         => 'ACTIVE',
        ], $data);
        return parent::create( $data )->data();
    }

    public function update(array $data = [])
    {
        $data = array_merge([
            'updated_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'sys_status'         => 'ACTIVE',
        ], $data);
        return parent::update( $data )->data();
    }

    public function ajaxSelect($filter = null, array $Options = [], int $page = 1, int $limit = 30)
    {
        $this->model()->with([
            'Country' => fn($Rel) => $Rel,
            'City'    => fn($Rel) => $Rel,
        ]);
        $this->model()->where('sys_status', 'ACTIVE');
        if ($Options['country_id']) {
            $this->model()->where('country_id', $Options['country_id']);
        }
        if ($Options['city_id']) {
            $this->model()->where('city_id', $Options['city_id']);
        }
        return parent::ajaxSelect($filter, $Options, $page, $limit);
    }

    public function install() : bool
    {
        $Group = AbilityGroup::firstOrNew([
            'code' => $this->getPermissionGroupName()
        ]);
        $Group->icon               = null;
        $Group->{'default:ar'}      = 'Y';
        $Group->{'title:ar'}       = 'Municipality';
        $Group->{'description:ar'} = null;
        $Group->save();

        $read = Ability::firstOrNew([
            'name' => $this->getPermissionName('read')
        ]);
        $read->group_id           = $Group->id;
        $read->{'default:ar'}      = 'Y';
        $read->{'title:ar'}       = 'read';
        $read->{'description:ar'} = null;
        $read->save();

        $create = Ability::firstOrNew([
            'name' => $this->getPermissionName('create')
        ]);
        $create->group_id           = $Group->id;
        $create->{'default:ar'}      = 'Y';
        $create->{'title:ar'}       = 'create';
        $create->{'description:ar'} = null;
        $create->save();

        $update = Ability::firstOrNew([
            'name' => $this->getPermissionName('update')
        ]);
        $update->group_id           = $Group->id;
        $update->{'default:ar'}      = 'Y';
        $update->{'title:ar'}       = 'update';
        $update->{'description:ar'} = null;
        $update->save();

        $delete = Ability::firstOrNew([
            'name' => $this->getPermissionName('delete')
        ]);
        $delete->group_id           = $Group->id;
        $delete->{'default:ar'}      = 'Y';
        $delete->{'title:ar'}       = 'delete';
        $delete->{'description:ar'} = null;
        $delete->save();

        $restore = Ability::firstOrNew([
            'name' => $this->getPermissionName('restore')
        ]);
        $restore->group_id           = $Group->id;
        $restore->{'default:ar'}      = 'Y';
        $restore->{'title:ar'}       = 'restore';
        $restore->{'description:ar'} = null;
        $restore->save();

        $permaDelete = Ability::firstOrNew([
            'name' => $this->getPermissionName('permaDelete')
        ]);
        $permaDelete->group_id           = $Group->id;
        $permaDelete->{'default:ar'}      = 'Y';
        $permaDelete->{'title:ar'}       = 'permaDelete';
        $permaDelete->{'description:ar'} = null;
        $permaDelete->save();

        $statusUpdate = Ability::firstOrNew([
            'name' => $this->getPermissionName('statusUpdate')
        ]);
        $statusUpdate->group_id           = $Group->id;
        $statusUpdate->{'default:ar'}      = 'Y';
        $statusUpdate->{'title:ar'}       = 'statusUpdate';
        $statusUpdate->{'description:ar'} = null;
        $statusUpdate->save();

        $ADMIN_Role = Role::where('name', 'SYSTEM_ADMIN')->first();
        if ($ADMIN_Role) {
            $ADMIN_Role->abilities()->syncWithoutDetaching([
                $read->id,
                $create->id,
                $update->id,
                $delete->id,
                // $permaDelete->id,
                $statusUpdate->id,
            ]);
        }

        return true;
    }
}
