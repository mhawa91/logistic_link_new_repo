<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Entities\RoleService;
use Modules\Admin\Entities\UserService;
use Modules\SystemCore\Entities\Contracts\SystemConfigInterface;

use DB;
use Hash;
use Bouncer;
use Exception;
use Carbon\Carbon;
use Modules\SystemCore\Entities\AbilityGroup;
use Modules\SystemCore\Entities\Ability;
use Modules\SystemCore\Entities\Role;

class AdminDatabaseSeeder extends Seeder
{
    public $data = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->data['UserService']         = app(UserService::class);
        $this->data['SystemConfigService'] = app(SystemConfigInterface::class);

        // $this->call("OthersTableSeeder");
        
        \DB::transaction(function() {
            $this->seedBaseRoles();
            $this->Install();
            $this->Roles();
            $this->Users();
        });
    }

    protected function seedBaseRoles()
    {
        $RoleService = app(RoleService::class);
    }

    /**
     * Post Module Installation.
     *
     * @return void
     */
    public function Install()
    {
        User: {
            $this->data['UserGroup'] = AbilityGroup::firstOrNew([
                'code' => $this->data['UserService']->getPermissionGroupName()
            ]);
            $this->data['UserGroup']->icon               = null;
            $this->data['UserGroup']->{'default:ar'}      = 'Y';
            $this->data['UserGroup']->{'title:ar'}       = 'Users Management';
            $this->data['UserGroup']->{'description:ar'} = null;
            $this->data['UserGroup']->save();
    
            $this->data['UserRead'] = Ability::firstOrNew([
                'name' => $this->data['UserService']->getPermissionName('read')
            ]);
            $this->data['UserRead']->group_id           = $this->data['UserGroup']->id;
            $this->data['UserRead']->{'default:ar'}      = 'Y';
            $this->data['UserRead']->{'title:ar'}       = 'read';
            $this->data['UserRead']->{'description:ar'} = null;
            $this->data['UserRead']->save();
    
            $this->data['UserCreate'] = Ability::firstOrNew([
                'name' => $this->data['UserService']->getPermissionName('create')
            ]);
            $this->data['UserCreate']->group_id           = $this->data['UserGroup']->id;
            $this->data['UserCreate']->{'default:ar'}      = 'Y';
            $this->data['UserCreate']->{'title:ar'}       = 'create';
            $this->data['UserCreate']->{'description:ar'} = null;
            $this->data['UserCreate']->save();
    
            $this->data['UserUpdate'] = Ability::firstOrNew([
                'name' => $this->data['UserService']->getPermissionName('update')
            ]);
            $this->data['UserUpdate']->group_id           = $this->data['UserGroup']->id;
            $this->data['UserUpdate']->{'default:ar'}      = 'Y';
            $this->data['UserUpdate']->{'title:ar'}       = 'update';
            $this->data['UserUpdate']->{'description:ar'} = null;
            $this->data['UserUpdate']->save();
    
            $this->data['UserDelete'] = Ability::firstOrNew([
                'name' => $this->data['UserService']->getPermissionName('delete')
            ]);
            $this->data['UserDelete']->group_id           = $this->data['UserGroup']->id;
            $this->data['UserDelete']->{'default:ar'}      = 'Y';
            $this->data['UserDelete']->{'title:ar'}       = 'delete';
            $this->data['UserDelete']->{'description:ar'} = null;
            $this->data['UserDelete']->save();
    
            $this->data['UserRestore'] = Ability::firstOrNew([
                'name' => $this->data['UserService']->getPermissionName('restore')
            ]);
            $this->data['UserRestore']->group_id           = $this->data['UserGroup']->id;
            $this->data['UserRestore']->{'default:ar'}      = 'Y';
            $this->data['UserRestore']->{'title:ar'}       = 'restore';
            $this->data['UserRestore']->{'description:ar'} = null;
            $this->data['UserRestore']->save();
    
            $this->data['UserPermaDelete'] = Ability::firstOrNew([
                'name' => $this->data['UserService']->getPermissionName('permaDelete')
            ]);
            $this->data['UserPermaDelete']->group_id           = $this->data['UserGroup']->id;
            $this->data['UserPermaDelete']->{'default:ar'}      = 'Y';
            $this->data['UserPermaDelete']->{'title:ar'}       = 'permaDelete';
            $this->data['UserPermaDelete']->{'description:ar'} = null;
            $this->data['UserPermaDelete']->save();
    
            $this->data['UserStatusUpdate'] = Ability::firstOrNew([
                'name' => $this->data['UserService']->getPermissionName('statusUpdate')
            ]);
            $this->data['UserStatusUpdate']->group_id           = $this->data['UserGroup']->id;
            $this->data['UserStatusUpdate']->{'default:ar'}      = 'Y';
            $this->data['UserStatusUpdate']->{'title:ar'}       = 'statusUpdate';
            $this->data['UserStatusUpdate']->{'description:ar'} = null;
            $this->data['UserStatusUpdate']->save();
        }
        
        SystemConfig: {
            $this->data['SystemConfigGroup'] = AbilityGroup::firstOrNew([
                'code' => $this->data['SystemConfigService']->permissionNamespace(null)
            ]);
            $this->data['SystemConfigGroup']->icon               = null;
            $this->data['SystemConfigGroup']->{'default:ar'}      = 'Y';
            $this->data['SystemConfigGroup']->{'title:ar'}       = 'Content Categories Management';
            $this->data['SystemConfigGroup']->{'description:ar'} = null;
            $this->data['SystemConfigGroup']->save();
    
            $this->data['SystemConfigRead'] = Ability::firstOrNew([
                'name' => $this->data['SystemConfigService']->permissionNamespace('read')
            ]);
            $this->data['SystemConfigRead']->group_id           = $this->data['SystemConfigGroup']->id;
            $this->data['SystemConfigRead']->{'default:ar'}      = 'Y';
            $this->data['SystemConfigRead']->{'title:ar'}       = 'read';
            $this->data['SystemConfigRead']->{'description:ar'} = null;
            $this->data['SystemConfigRead']->save();
    
            $this->data['SystemConfigCreate'] = Ability::firstOrNew([
                'name' => $this->data['SystemConfigService']->permissionNamespace('create')
            ]);
            $this->data['SystemConfigCreate']->group_id           = $this->data['SystemConfigGroup']->id;
            $this->data['SystemConfigCreate']->{'default:ar'}      = 'Y';
            $this->data['SystemConfigCreate']->{'title:ar'}       = 'create';
            $this->data['SystemConfigCreate']->{'description:ar'} = null;
            $this->data['SystemConfigCreate']->save();
    
            $this->data['SystemConfigUpdate'] = Ability::firstOrNew([
                'name' => $this->data['SystemConfigService']->permissionNamespace('update')
            ]);
            $this->data['SystemConfigUpdate']->group_id           = $this->data['SystemConfigGroup']->id;
            $this->data['SystemConfigUpdate']->{'default:ar'}      = 'Y';
            $this->data['SystemConfigUpdate']->{'title:ar'}       = 'update';
            $this->data['SystemConfigUpdate']->{'description:ar'} = null;
            $this->data['SystemConfigUpdate']->save();
    
            $this->data['SystemConfigDelete'] = Ability::firstOrNew([
                'name' => $this->data['SystemConfigService']->permissionNamespace('delete')
            ]);
            $this->data['SystemConfigDelete']->group_id           = $this->data['SystemConfigGroup']->id;
            $this->data['SystemConfigDelete']->{'default:ar'}      = 'Y';
            $this->data['SystemConfigDelete']->{'title:ar'}       = 'delete';
            $this->data['SystemConfigDelete']->{'description:ar'} = null;
            $this->data['SystemConfigDelete']->save();
    
            $this->data['SystemConfigRestore'] = Ability::firstOrNew([
                'name' => $this->data['SystemConfigService']->permissionNamespace('restore')
            ]);
            $this->data['SystemConfigRestore']->group_id           = $this->data['SystemConfigGroup']->id;
            $this->data['SystemConfigRestore']->{'default:ar'}      = 'Y';
            $this->data['SystemConfigRestore']->{'title:ar'}       = 'restore';
            $this->data['SystemConfigRestore']->{'description:ar'} = null;
            $this->data['SystemConfigRestore']->save();
    
            $this->data['SystemConfigPermaDelete'] = Ability::firstOrNew([
                'name' => $this->data['SystemConfigService']->permissionNamespace('permaDelete')
            ]);
            $this->data['SystemConfigPermaDelete']->group_id           = $this->data['SystemConfigGroup']->id;
            $this->data['SystemConfigPermaDelete']->{'default:ar'}      = 'Y';
            $this->data['SystemConfigPermaDelete']->{'title:ar'}       = 'permaDelete';
            $this->data['SystemConfigPermaDelete']->{'description:ar'} = null;
            $this->data['SystemConfigPermaDelete']->save();
    
            $this->data['SystemConfigStatusUpdate'] = Ability::firstOrNew([
                'name' => $this->data['SystemConfigService']->permissionNamespace('statusUpdate')
            ]);
            $this->data['SystemConfigStatusUpdate']->group_id           = $this->data['SystemConfigGroup']->id;
            $this->data['SystemConfigStatusUpdate']->{'default:ar'}      = 'Y';
            $this->data['SystemConfigStatusUpdate']->{'title:ar'}       = 'statusUpdate';
            $this->data['SystemConfigStatusUpdate']->{'description:ar'} = null;
            $this->data['SystemConfigStatusUpdate']->save();
        }
    }

    /**
     * Create Module Roles.
     *
     * @return void
     */
    public function Roles()
    {
        $this->data['ADMIN_Role'] = Role::where('name', 'SYSTEM_ADMIN')->first();
        if ($this->data['ADMIN_Role']) {
            $this->data['ADMIN_Role']->abilities()->syncWithoutDetaching([
                $this->data['UserRead']->id,
                $this->data['UserCreate']->id,
                $this->data['UserUpdate']->id,
                $this->data['UserDelete']->id,
                $this->data['UserStatusUpdate']->id,

                $this->data['SystemConfigRead']->id,
                $this->data['SystemConfigCreate']->id,
                $this->data['SystemConfigUpdate']->id,
                $this->data['SystemConfigDelete']->id,
                $this->data['SystemConfigStatusUpdate']->id,
            ]);
        }
    }

    /**
     * Create Module Needed Users.
     *
     * @return void
     */
    public function Users()
    {
        // $this->data['User'] = app(config('systemcore.models.User'));
    }
}
