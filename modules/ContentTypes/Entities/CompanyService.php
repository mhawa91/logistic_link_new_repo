<?php

namespace Modules\ContentTypes\Entities;

use Validator;
use DB;
use Modules\SystemCore\Entities\Role;
use Modules\SystemCore\Entities\AbilityGroup;
use Modules\SystemCore\Entities\Ability;
use Modules\SystemCore\System\Services\ModelService;
use Modules\SystemCore\System\Services\ExportService;

class CompanyService extends ModelService
{
    public function register() 
    {
        $this->modelNamespace = Company::class;
        $this->guard();
        if ($this->can('update')) {
            $this->registerDataTableAction('update', function($Row) {
                return [
                    'label'  => __('ui::strings.update'),
                    'action' => 'javascript:;',
                ];
            });
        }
        if ($this->can('delete')) {
            $this->registerDataTableAction('delete', function($Row) {
                return $Row->trashed() ? null : [
                    'label'  => __('ui::strings.delete'),
                    'action' => 'javascript:;',
                ];
            });
        }
        if ($this->can('restore')) {
            $this->registerDataTableAction('restore', function($Row) {
                return ! $Row->trashed() ? null : [
                    'label'  => __('ui::strings.restore'),
                    'action' => 'javascript:;',
                ];
            });
        }
        if ($this->can('permaDelete')) {
            $this->registerDataTableAction('permaDelete', function($Row) {
                return [
                    'label'  => __('ui::strings.perma_delete'),
                    'action' => 'javascript:;',
                ];
            });
        }
        $this->registerAjaxSelectCallback(function($Row) {
            return [
                'id'   => ! $Row ? '0'            : $Row->id,
                'text' => ! $Row ? '-- Select --' : $Row->code . ' - [' . $Row->symbol . ']',
                'data' => ! $Row ? []             : $Row->toArray(),
            ];
        });
    }

    public function validator($type = 'create', array $data = [])
    {
        if ($type == 'create') {
            return Validator::make($data, []);
        }
        if ($type == 'update') {
            return Validator::make($data, []);
        }
    }

    protected function keywordFilter($query, string $keyword = null)
    {
        $query->where(function($Model) use($keyword) {
            
        });
    }

    protected function advancedFilter($query, array $filter = [])
    {
        if (! empty($filter['suitable_for_shippment'])) {
            $query->OnlySuitableForShippment($filter['suitable_for_shippment']);
        }
    }

    public function customModel($model = null) 
    {
        if (! is_null($model)) {
            $this->modelQuery = $model->query();
        }
        if (is_null($this->modelQuery)) {
            $this->modelQuery = app($this->modelNamespace)->query();
        }
        return $this->modelQuery
        ->select('lgl_company_profile.*')
        ->WithBaseSelect()
        ->WithReviewsStates()
        ->whereHas('user', function($query) {
            $query->where('users.type', 'TRANSPORTER');
        })
        ->with([
            'user'    => function($query) {},
            'address' => function($query) {
                $query->select('lgl_addressess.*')
                ->WithCountryAndCitySelect()
                ->groupBy('lgl_addressess.id');
            },
            'registrationTypeCategory' => function($query) {
                $query->withTrashed();
            },
        ]);
    }

    public function dataTable($Filter = null)
    {
        $this->model()
        ->select('lgl_company_profile.*')
        ->WithBaseSelect()
        ->WithReviewsStates()
        ->where('lgl_company_profile.sys_status', '=', 'ACTIVE')
        ->whereHas('user', function($query) {
            $query->where('users.type', 'TRANSPORTER');
        })
        ->with([
            'user'    => function($query) {},
            'address' => function($query) {
                $query->select('lgl_addressess.*')
                ->WithCountryAndCitySelect()
                ->groupBy('lgl_addressess.id');
            },
        ]);

        return parent::dataTable($Filter);
    }

    public function export($filter = null)
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }

        $query    = $this->model();
        $this->filter($query, $filter);

        $exporter = app(ExportService::class)->of($query)->name('Data.csv');

        $exporter->addColumn('id', function($row) {
            return $row->id;
        });

        return $exporter;
    }

    public function create(array $data = [])
    {
        return null;
    }

    public function update(array $data = [])
    {
        return null;
    }

    public function ajaxSelect($filter = null, array $Options = [], int $page = 1, int $limit = 30)
    {
        // $this->model()->whereNull('id');
        // $this->model()->with([
        //    'translations' => fn($rel) => $rel,
        // ]);
        return parent::ajaxSelect($filter, $Options, $page, $limit);
    }

    public function install() : bool
    {
        $Group = AbilityGroup::firstOrNew([
            'code' => $this->getPermissionGroupName()
        ]);
        $Group->icon               = null;
        $Group->{'default:ar'}      = 'Y';
        $Group->{'title:ar'}       = 'Company';
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

        $ADMIN_Role   = Role::where('name', 'SYSTEM_ADMIN')->first();
        $DEFAULT_Role = Role::where('name', 'DEFAULT')->first();
        $TRANSPORTAR_Role  = Role::where('name', 'TRANSPORTER')->first();
        if ($ADMIN_Role) {
            $ADMIN_Role->abilities()->syncWithoutDetaching([
                $read->id,
            ]);
        }
        if ($DEFAULT_Role) {
            $DEFAULT_Role->abilities()->syncWithoutDetaching([
                $read->id,
                $create->id,
            ]);
        }
        if ($TRANSPORTAR_Role) {
            $TRANSPORTAR_Role->abilities()->syncWithoutDetaching([
                $read->id,
            ]);
        }

        return true;
    }
}
