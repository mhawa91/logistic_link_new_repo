<?php

namespace Modules\ContactUs\Entities;

use Validator;
use Modules\SystemCore\Entities\Role;
use Modules\SystemCore\Entities\AbilityGroup;
use Modules\SystemCore\Entities\Ability;
use Modules\SystemCore\System\Services\ModelService;

class ContactUsService extends ModelService
{
    public function register() 
    {
        $this->modelNamespace = ContactUs::class;
        $this->guard();
        // if ($this->can('update')) {
            $this->registerDataTableAction('update', function($Row) {
                return [
                    'label'  => __('ui::strings.update'),
                    'action' => route('ContactUsController@update', ['model' => $Row->id]),
                ];
            });
        // }
        // if ($this->can('delete')) {
            $this->registerDataTableAction('delete', function($Row) {
                return $Row->trashed() || ! empty($Row->tag) ? null : [
                    'label'  => __('ui::strings.delete'),
                    'action' => route('ContactUsController@postDelete', ['model' => $Row->id]),
                ];
            });
        // }
        // if ($this->can('restore')) {
            $this->registerDataTableAction('restore', function($Row) {
                return ! $Row->trashed() ? null : [
                    'label'  => __('ui::strings.restore'),
                    'action' => route('ContactUsController@postRestore', ['model' => $Row->id]),
                ];
            });
        // }
        // if ($this->can('permaDelete')) {
            $this->registerDataTableAction('permaDelete', function($Row) {
                return ! empty($Row->tag) ? null : [
                    'label'  => __('ui::strings.perma_delete'),
                    'action' => route('ContactUsController@postPermaDelete', ['model' => $Row->id]),
                ];
            });
        // }
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
            return Validator::make($data, [
                'name'         => 'required|max:191',
                'email'        => 'required|max:191',
                'phone_number' => 'nullable|max:191',
                'subject'      => 'required|max:255',
                'message'      => 'required|max:5000',
            ]);
        }
        if ($type == 'update') {
            return Validator::make($data, [
                'name'         => 'required|max:191',
                'email'        => 'required|max:191',
                'phone_number' => 'nullable|max:191',
                'subject'      => 'required|max:255',
                'message'      => 'required|max:5000',
            ]);
        }
    }

    protected function keywordFilter($query, string $keyword = null)
    {
        $query->where(function($Model) use($keyword) {
            $Model->where('name', 'like', "%{$keyword}%");
            $Model->orWhere('email', 'like', "%{$keyword}%");
            $Model->orWhere('phone_number', 'like', "%{$keyword}%");
            $Model->orWhere('subject', 'like', "%{$keyword}%");
            // $Model->orWhere('message', 'like', "%{$keyword}%");
        });
    }

    protected function advancedFilter($query, array $filter = [])
    {
        if (! empty($filter['name'])) {
            $query->where('name', 'like', "%{$filter['name']}%");
        }
        if (! empty($filter['email'])) {
            $query->where('email', 'like', "%{$filter['email']}%");
        }
        if (! empty($filter['phone_number'])) {
            $query->where('phone_number', 'like', "%{$filter['phone_number']}%");
        }
        if (! empty($filter['subject'])) {
            $query->where('subject', 'like', "%{$filter['subject']}%");
        }
        if (! empty($filter['sys_status'])) {
            $query->where('sys_status', $filter['sys_status']);
        }
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
        ]);

        return parent::dataTable($Filter);
    }

    public function create(array $data = [])
    {
        $data = array_merge([
            'name'               => null,
            'email'              => null,
            'phone_number'       => null,
            'subject'            => null,
            'message'            => null,
            'created_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'updated_by_user_id' => null,
            'sys_status'         => 'PENDING',
        ], $data);
        $data['phone_number'] = strtolower($data['phone_number']);
        $data['phone_number'] = app(\Modules\SystemCore\System\Classes\Helpers::class)->parsePhoneNumber($data['phone_number']);
        return parent::create( $data )->data();
    }

    public function update(array $data = [])
    {
        $data = array_merge([
            'updated_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'sys_status'         => 'PENDING',
        ], $data);
        return parent::update( $data )->data();
    }

    public function ajaxSelect($filter = null, array $Options = [], int $page = 1, int $limit = 30)
    {
        return parent::ajaxSelect($filter, $Options, $page, $limit);
    }

    public function install() : bool
    {
        $Group = AbilityGroup::firstOrNew([
            'code' => $this->getPermissionGroupName()
        ]);
        $Group->icon               = null;
        $Group->{'default:ar'}      = 'Y';
        $Group->{'title:ar'}       = 'طلبات التواصل';
        $Group->{'description:ar'} = null;
        $Group->save();

        $read = Ability::firstOrNew([
            'name' => $this->getPermissionName('read')
        ]);
        $read->group_id           = $Group->id;
        $read->{'default:ar'}      = 'Y';
        $read->{'title:ar'}       = 'قراءة';
        $read->{'description:ar'} = null;
        $read->save();

        $create = Ability::firstOrNew([
            'name' => $this->getPermissionName('create')
        ]);
        $create->group_id           = $Group->id;
        $create->{'default:ar'}      = 'Y';
        $create->{'title:ar'}       = 'اضافة';
        $create->{'description:ar'} = null;
        $create->save();

        $update = Ability::firstOrNew([
            'name' => $this->getPermissionName('update')
        ]);
        $update->group_id           = $Group->id;
        $update->{'default:ar'}      = 'Y';
        $update->{'title:ar'}       = 'تعديل';
        $update->{'description:ar'} = null;
        $update->save();

        $delete = Ability::firstOrNew([
            'name' => $this->getPermissionName('delete')
        ]);
        $delete->group_id           = $Group->id;
        $delete->{'default:ar'}      = 'Y';
        $delete->{'title:ar'}       = 'حذف';
        $delete->{'description:ar'} = null;
        $delete->save();

        $restore = Ability::firstOrNew([
            'name' => $this->getPermissionName('restore')
        ]);
        $restore->group_id           = $Group->id;
        $restore->{'default:ar'}      = 'Y';
        $restore->{'title:ar'}       = 'استعادة';
        $restore->{'description:ar'} = null;
        $restore->save();

        $permaDelete = Ability::firstOrNew([
            'name' => $this->getPermissionName('permaDelete')
        ]);
        $permaDelete->group_id           = $Group->id;
        $permaDelete->{'default:ar'}      = 'Y';
        $permaDelete->{'title:ar'}       = 'حذف نهائي';
        $permaDelete->{'description:ar'} = null;
        $permaDelete->save();

        $statusUpdate = Ability::firstOrNew([
            'name' => $this->getPermissionName('statusUpdate')
        ]);
        $statusUpdate->group_id           = $Group->id;
        $statusUpdate->{'default:ar'}      = 'Y';
        $statusUpdate->{'title:ar'}       = 'تحديث الحالة';
        $statusUpdate->{'description:ar'} = null;
        $statusUpdate->save();

        $ADMIN_Role = Role::where('name', 'SYSTEM_ADMIN')->first();
        if ($ADMIN_Role) {
            $ADMIN_Role->abilities()->syncWithoutDetaching([
                $read->id,
                $create->id,
                $update->id,
                $delete->id,
                $permaDelete->id,
                $statusUpdate->id,
            ]);
        }

        return true;
    }

    public function syncPermissions($Role)
    {
        $read         = Ability::where('name', $this->getPermissionName('read'))->firstOrFail();
        $create       = Ability::where('name', $this->getPermissionName('create'))->firstOrFail();
        $update       = Ability::where('name', $this->getPermissionName('update'))->firstOrFail();
        $delete       = Ability::where('name', $this->getPermissionName('delete'))->firstOrFail();
        $permaDelete  = Ability::where('name', $this->getPermissionName('permaDelete'))->firstOrFail();
        $statusUpdate = Ability::where('name', $this->getPermissionName('statusUpdate'))->firstOrFail();
        $Role->abilities()->syncWithoutDetaching([
            $read->id,
            $create->id,
            $update->id,
            $delete->id,
            $permaDelete->id,
            $statusUpdate->id,
        ]);
    }
}
