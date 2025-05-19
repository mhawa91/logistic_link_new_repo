<?php

namespace Modules\ContentTypes\Entities;

use Validator;
use DB;
use Modules\SystemCore\Entities\Role;
use Modules\SystemCore\Entities\AbilityGroup;
use Modules\SystemCore\Entities\Ability;
use Modules\SystemCore\System\Services\ModelService;
use Modules\SystemCore\System\Services\ExportService;
use Carbon\Carbon;

class DriverService extends ModelService
{
    public function register() 
    {
        $this->modelNamespace = Driver::class;

        $this->guard();

        $this->registerAjaxSelectCallback(function($Row) {
            return [
                'id'   => ! $Row ? '0'            : $Row->id,
                'text' => ! $Row ? '-- Select --' : $Row->name . ' / ' . $Row->id_number,
                'data' => ! $Row ? []             : $Row->toArray(),
            ];
        });
    }

    public function validator($type = 'create', array $data = [])
    {
        $rules = [];
        if ($type == 'create') {
            return Validator::make($data, $rules);
        }
        if ($type == 'update') {
            return Validator::make($data, $rules);
        }
    }

    protected function keywordFilter($query, string $keyword = null)
    {
        $query->when(auth()->check() && auth()->user()->type == 'DEFAULT', function($query) {
            $query->whereNull('lgl_drivers.id');
        });
        $query->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->where('lgl_drivers.transporter_user_id', auth()->id());
        });

        $query->where(function($Model) use($keyword) {
            $Model
            ->where('lgl_drivers.name', 'LIKE', "%{$keyword}%")
            ->orWhere('lgl_drivers.id_number', 'LIKE', "%{$keyword}%")
            ->orWhereHas('nationality', function($query) use($keyword) {
                $query->where(function($query) {
                    $query->where('name_ar', 'LIKE', "%{$keyword}%");
                    $query->orWhere('name_en', 'LIKE', "%{$keyword}%");
                });
            });
        });
    }

    protected function advancedFilter($query, array $filter = [])
    {
        $query->when(auth()->check() && auth()->user()->type == 'DEFAULT', function($query) {
            $query->whereNull('lgl_drivers.id');
        });
        $query->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->where('lgl_drivers.transporter_user_id', auth()->id());
        });

        if (! empty($filter['title'])) {
        }
    }

    public function dataTable($Filter = null)
    {
        $this->model()->select([
            'lgl_drivers.*',
        ])
        ->when(auth()->check() && auth()->user()->type == 'DEFAULT', function($query) {
            $query->whereNull('lgl_drivers.id');
        })
        ->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->where('lgl_drivers.transporter_user_id', auth()->id());
        })
        ->when(auth()->check() && auth()->user()->type != 'DEFAULT' && auth()->user()->type != 'TRANSPORTER', function($query) {
            $query;
        });

        $helper = app(\Modules\SystemCore\System\Classes\Helpers::class);
        $table  = parent::dataTable($Filter);

        return $table;
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
        $ModelData = [];

        $model = parent::create( $ModelData )->data();

        return $model;
    }

    public function update(array $data = [])
    {
        $ModelData = [];

        $model = parent::update( $ModelData )->data();

        return $model;
    }

    public function ajaxSelect($filter = null, array $Options = [], int $page = 1, int $limit = 30)
    {
        // $this->model()->whereNull('id');
        // $this->model()->with([
        //    'translations' => fn($rel) => $rel,
        // ]);

        $this->model()->when(auth()->check() && auth()->user()->type == 'DEFAULT', function($query) {
            $query->whereNull('lgl_drivers.id');
        })
        ->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->where('lgl_drivers.transporter_user_id', auth()->id());
        });

        return parent::ajaxSelect($filter, $Options, $page, $limit);
    }

    public function install() : bool
    {
        $Group = AbilityGroup::firstOrNew([
            'code' => $this->getPermissionGroupName()
        ]);
        $Group->icon               = null;
        $Group->{'default:ar'}      = 'Y';
        $Group->{'title:ar'}       = 'Shippment';
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

        $CLIENT_Role = Role::where('name', 'DEFAULT')->first();
        if ($CLIENT_Role) {
            $CLIENT_Role->abilities()->syncWithoutDetaching([
                $read->id,
                // $create->id,
                // $update->id,
                // $delete->id,
                // $permaDelete->id,
                // $statusUpdate->id,
            ]);
        }

        $TRANSPORTER_Role = Role::where('name', 'TRANSPORTER')->first();
        if ($TRANSPORTER_Role) {
            $TRANSPORTER_Role->abilities()->syncWithoutDetaching([
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
