<?php

namespace Modules\Address\Entities;

use Validator;
use DB;
use Modules\SystemCore\Entities\Role;
use Modules\SystemCore\Entities\AbilityGroup;
use Modules\SystemCore\Entities\Ability;
use Modules\SystemCore\System\Services\ModelService;

class AddressService extends ModelService
{
    public function register() 
    {
        $this->modelNamespace = Address::class;
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
                'text' => ! $Row ? '-- Select --' : $Row->title . ' - [' . $Row->type . ']',
                'data' => ! $Row ? []             : $Row->toArray(),
            ];
        });
    }

    public function validator($type = 'create', array $data = [])
    {
        $rules = [
            'user_id'          => 'nullable|exists:users,id',
            'title'            => 'required|max:191',
            'type'             => 'nullable|in:' . implode(',', array_keys(config('address.address_types'))),
            'phone_number'     => 'required',
            'email'            => 'nullable',
            'country_id'       => 'required|exists:countries,id',
            'city_id'          => 'required|exists:cities,id',
            'municipality_id'  => 'required|exists:municipalities,id',
            'neighbourhood_id' => 'required|exists:neighbourhoods,id',
            'postal_code'      => 'nullable',
            'street'           => 'nullable',
            'building'         => 'nullable',
            'floor'            => 'nullable',
            'appartment'       => 'nullable',
            'instructions'     => 'nullable',
            'full_address'     => 'required',
            'lat'              => 'required|numeric',
            'lng'              => 'required|numeric',
        ];
        if ($type == 'create') {
            return Validator::make($data, $rules);
        }
        if ($type == 'update') {
            return Validator::make($data, $rules);
        }
    }

    protected function keywordFilter($query, string $keyword = null)
    {
        $query->where(function($Model) use($keyword) {
            $Model->whereHas('translations', function($Rel) use($keyword) {
                $Rel->where('title', 'like', "%{$keyword}%");
            });
        });
    }

    protected function advancedFilter($query, array $filter = [])
    {
        if (! empty($filter['title'])) {
            $query->whereHas('translations', function($Rel) use($filter) {
                $Rel->where('title', 'like', "%{$filter['title']}%");
            });
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
            'User'          => fn($Rel) => $Rel->withTrashed(),
            'Provider'      => fn($Rel) => $Rel->withTrashed(),
            'Country'       => fn($Rel) => $Rel->withTrashed(),
            'City'          => fn($Rel) => $Rel->withTrashed(),
            'Municipality'  => fn($Rel) => $Rel->withTrashed(),
            'Neighbourhood' => fn($Rel) => $Rel->withTrashed(),
        ]);

        return parent::dataTable($Filter);
    }

    public function create(array $data = [])
    {
        $data = array_merge([
            'user_id'          => null,
            'title'            => null,
            'type'             => null,
            'phone_number'     => null,
            'email'            => null,
            'country_id'       => null,
            'city_id'          => null,
            'municipality_id'  => null,
            'neighbourhood_id' => null,
            'postal_code'      => null,
            'street'           => null,
            'building'         => null,
            'floor'            => null,
            'appartment'       => null,
            'instructions'     => null,
            'full_address'     => null,
            'lat'              => null,
            'lng'              => null,
        ], $data);

        $data['phone_number'] = strtolower($data['phone_number']);
        $data['phone_number'] = app(\Modules\SystemCore\System\Classes\Helpers::class)->parsePhoneNumber($data['phone_number']);
        
        return parent::create( $data )->data();
    }

    public function update(array $data = [])
    {
        $data = array_merge([
            'user_id'          => null,
            'title'            => null,
            'type'             => null,
            'phone_number'     => null,
            'email'            => null,
            'country_id'       => null,
            'city_id'          => null,
            'municipality_id'  => null,
            'neighbourhood_id' => null,
            'postal_code'      => null,
            'street'           => null,
            'building'         => null,
            'floor'            => null,
            'appartment'       => null,
            'instructions'     => null,
            'full_address'     => null,
            'lat'              => null,
            'lng'              => null,
        ], $data);
        $data['phone_number'] = strtolower($data['phone_number']);
        $data['phone_number'] = app(\Modules\SystemCore\System\Classes\Helpers::class)->parsePhoneNumber($data['phone_number']);
        return parent::update( $data )->data();
    }

    public function ajaxSelect($filter = null, array $Options = [], int $page = 1, int $limit = 30)
    {
        // $this->model()->whereNull('id');
        return parent::ajaxSelect($filter, $Options, $page, $limit);
    }

    public function install() : bool
    {
        $Group = AbilityGroup::firstOrNew([
            'code' => $this->getPermissionGroupName()
        ]);
        $Group->icon               = null;
        $Group->{'default:ar'}      = 'Y';
        $Group->{'title:ar'}       = 'ادارة العناوين';
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
