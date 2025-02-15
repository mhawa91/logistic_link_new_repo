<?php

namespace Modules\ContentManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use DB;
use Hash;
use Bouncer;
use Exception;
use Carbon\Carbon;
use Modules\SystemCore\Entities\AbilityGroup;
use Modules\SystemCore\Entities\Ability;
use Modules\SystemCore\Entities\Role;
use Modules\ContentManager\Entities\Contracts\CategoryInterface;
use Modules\ContentManager\Entities\Contracts\ContentInterface;
use Modules\ContentManager\Entities\Contracts\SectionInterface;

class ContentManagerDatabaseSeeder extends Seeder
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

        // $this->call("OthersTableSeeder");

        $this->data['CategoryService'] = app(CategoryInterface::class);
        $this->data['ContentService']  = app(ContentInterface::class);
        $this->data['SectionService']  = app(SectionInterface::class);

        DB::transaction(function(){
            $this->Install();
            $this->Roles();
            $this->Users();
        });
    }

    /**
     * Post Module Installation.
     *
     * @return void
     */
    public function Install()
    {
        Category: {
            $this->data['CategoryGroup'] = AbilityGroup::firstOrNew([
                'code' => $this->data['CategoryService']->permissionNamespace(null)
            ]);
            $this->data['CategoryGroup']->icon               = null;
            $this->data['CategoryGroup']->{'default:ar'}      = 'Y';
            $this->data['CategoryGroup']->{'title:ar'}       = 'Content Categories Management';
            $this->data['CategoryGroup']->{'description:ar'} = null;
            $this->data['CategoryGroup']->save();
    
            $this->data['CategoryRead'] = Ability::firstOrNew([
                'name' => $this->data['CategoryService']->permissionNamespace('read')
            ]);
            $this->data['CategoryRead']->group_id           = $this->data['CategoryGroup']->id;
            $this->data['CategoryRead']->{'default:ar'}      = 'Y';
            $this->data['CategoryRead']->{'title:ar'}       = 'read';
            $this->data['CategoryRead']->{'description:ar'} = null;
            $this->data['CategoryRead']->save();
    
            $this->data['CategoryCreate'] = Ability::firstOrNew([
                'name' => $this->data['CategoryService']->permissionNamespace('create')
            ]);
            $this->data['CategoryCreate']->group_id           = $this->data['CategoryGroup']->id;
            $this->data['CategoryCreate']->{'default:ar'}      = 'Y';
            $this->data['CategoryCreate']->{'title:ar'}       = 'create';
            $this->data['CategoryCreate']->{'description:ar'} = null;
            $this->data['CategoryCreate']->save();
    
            $this->data['CategoryUpdate'] = Ability::firstOrNew([
                'name' => $this->data['CategoryService']->permissionNamespace('update')
            ]);
            $this->data['CategoryUpdate']->group_id           = $this->data['CategoryGroup']->id;
            $this->data['CategoryUpdate']->{'default:ar'}      = 'Y';
            $this->data['CategoryUpdate']->{'title:ar'}       = 'update';
            $this->data['CategoryUpdate']->{'description:ar'} = null;
            $this->data['CategoryUpdate']->save();
    
            $this->data['CategoryDelete'] = Ability::firstOrNew([
                'name' => $this->data['CategoryService']->permissionNamespace('delete')
            ]);
            $this->data['CategoryDelete']->group_id           = $this->data['CategoryGroup']->id;
            $this->data['CategoryDelete']->{'default:ar'}      = 'Y';
            $this->data['CategoryDelete']->{'title:ar'}       = 'delete';
            $this->data['CategoryDelete']->{'description:ar'} = null;
            $this->data['CategoryDelete']->save();
    
            $this->data['CategoryRestore'] = Ability::firstOrNew([
                'name' => $this->data['CategoryService']->permissionNamespace('restore')
            ]);
            $this->data['CategoryRestore']->group_id           = $this->data['CategoryGroup']->id;
            $this->data['CategoryRestore']->{'default:ar'}      = 'Y';
            $this->data['CategoryRestore']->{'title:ar'}       = 'restore';
            $this->data['CategoryRestore']->{'description:ar'} = null;
            $this->data['CategoryRestore']->save();
    
            $this->data['CategoryPermaDelete'] = Ability::firstOrNew([
                'name' => $this->data['CategoryService']->permissionNamespace('permaDelete')
            ]);
            $this->data['CategoryPermaDelete']->group_id           = $this->data['CategoryGroup']->id;
            $this->data['CategoryPermaDelete']->{'default:ar'}      = 'Y';
            $this->data['CategoryPermaDelete']->{'title:ar'}       = 'permaDelete';
            $this->data['CategoryPermaDelete']->{'description:ar'} = null;
            $this->data['CategoryPermaDelete']->save();
    
            $this->data['CategoryStatusUpdate'] = Ability::firstOrNew([
                'name' => $this->data['CategoryService']->permissionNamespace('statusUpdate')
            ]);
            $this->data['CategoryStatusUpdate']->group_id           = $this->data['CategoryGroup']->id;
            $this->data['CategoryStatusUpdate']->{'default:ar'}      = 'Y';
            $this->data['CategoryStatusUpdate']->{'title:ar'}       = 'statusUpdate';
            $this->data['CategoryStatusUpdate']->{'description:ar'} = null;
            $this->data['CategoryStatusUpdate']->save();
        }
        Content: {
            $this->data['ContentGroup'] = AbilityGroup::firstOrNew([
                'code' => $this->data['ContentService']->permissionNamespace(null)
            ]);
            $this->data['ContentGroup']->icon               = null;
            $this->data['ContentGroup']->{'default:ar'}      = 'Y';
            $this->data['ContentGroup']->{'title:ar'}       = 'Content Management';
            $this->data['ContentGroup']->{'description:ar'} = null;
            $this->data['ContentGroup']->save();
    
            $this->data['ContentRead'] = Ability::firstOrNew([
                'name' => $this->data['ContentService']->permissionNamespace('read')
            ]);
            $this->data['ContentRead']->group_id           = $this->data['ContentGroup']->id;
            $this->data['ContentRead']->{'default:ar'}      = 'Y';
            $this->data['ContentRead']->{'title:ar'}       = 'read';
            $this->data['ContentRead']->{'description:ar'} = null;
            $this->data['ContentRead']->save();
    
            $this->data['ContentCreate'] = Ability::firstOrNew([
                'name' => $this->data['ContentService']->permissionNamespace('create')
            ]);
            $this->data['ContentCreate']->group_id           = $this->data['ContentGroup']->id;
            $this->data['ContentCreate']->{'default:ar'}      = 'Y';
            $this->data['ContentCreate']->{'title:ar'}       = 'create';
            $this->data['ContentCreate']->{'description:ar'} = null;
            $this->data['ContentCreate']->save();
    
            $this->data['ContentUpdate'] = Ability::firstOrNew([
                'name' => $this->data['ContentService']->permissionNamespace('update')
            ]);
            $this->data['ContentUpdate']->group_id           = $this->data['ContentGroup']->id;
            $this->data['ContentUpdate']->{'default:ar'}      = 'Y';
            $this->data['ContentUpdate']->{'title:ar'}       = 'update';
            $this->data['ContentUpdate']->{'description:ar'} = null;
            $this->data['ContentUpdate']->save();
    
            $this->data['ContentDelete'] = Ability::firstOrNew([
                'name' => $this->data['ContentService']->permissionNamespace('delete')
            ]);
            $this->data['ContentDelete']->group_id           = $this->data['ContentGroup']->id;
            $this->data['ContentDelete']->{'default:ar'}      = 'Y';
            $this->data['ContentDelete']->{'title:ar'}       = 'delete';
            $this->data['ContentDelete']->{'description:ar'} = null;
            $this->data['ContentDelete']->save();
    
            $this->data['ContentRestore'] = Ability::firstOrNew([
                'name' => $this->data['ContentService']->permissionNamespace('restore')
            ]);
            $this->data['ContentRestore']->group_id           = $this->data['ContentGroup']->id;
            $this->data['ContentRestore']->{'default:ar'}      = 'Y';
            $this->data['ContentRestore']->{'title:ar'}       = 'restore';
            $this->data['ContentRestore']->{'description:ar'} = null;
            $this->data['ContentRestore']->save();
    
            $this->data['ContentPermaDelete'] = Ability::firstOrNew([
                'name' => $this->data['ContentService']->permissionNamespace('permaDelete')
            ]);
            $this->data['ContentPermaDelete']->group_id           = $this->data['ContentGroup']->id;
            $this->data['ContentPermaDelete']->{'default:ar'}      = 'Y';
            $this->data['ContentPermaDelete']->{'title:ar'}       = 'permaDelete';
            $this->data['ContentPermaDelete']->{'description:ar'} = null;
            $this->data['ContentPermaDelete']->save();
    
            $this->data['ContentStatusUpdate'] = Ability::firstOrNew([
                'name' => $this->data['ContentService']->permissionNamespace('statusUpdate')
            ]);
            $this->data['ContentStatusUpdate']->group_id           = $this->data['ContentGroup']->id;
            $this->data['ContentStatusUpdate']->{'default:ar'}      = 'Y';
            $this->data['ContentStatusUpdate']->{'title:ar'}       = 'statusUpdate';
            $this->data['ContentStatusUpdate']->{'description:ar'} = null;
            $this->data['ContentStatusUpdate']->save();
        }
        Sections: {
            $this->data['SectionsGroup'] = AbilityGroup::firstOrNew([
                'code' => $this->data['SectionService']->permissionNamespace(null)
            ]);
            $this->data['SectionsGroup']->icon               = null;
            $this->data['SectionsGroup']->{'default:ar'}      = 'Y';
            $this->data['SectionsGroup']->{'title:ar'}       = 'Sections Management';
            $this->data['SectionsGroup']->{'description:ar'} = null;
            $this->data['SectionsGroup']->save();
    
            $this->data['SectionsRead'] = Ability::firstOrNew([
                'name' => $this->data['SectionService']->permissionNamespace('read')
            ]);
            $this->data['SectionsRead']->group_id           = $this->data['SectionsGroup']->id;
            $this->data['SectionsRead']->{'default:ar'}      = 'Y';
            $this->data['SectionsRead']->{'title:ar'}       = 'read';
            $this->data['SectionsRead']->{'description:ar'} = null;
            $this->data['SectionsRead']->save();
    
            $this->data['SectionsCreate'] = Ability::firstOrNew([
                'name' => $this->data['SectionService']->permissionNamespace('create')
            ]);
            $this->data['SectionsCreate']->group_id           = $this->data['SectionsGroup']->id;
            $this->data['SectionsCreate']->{'default:ar'}      = 'Y';
            $this->data['SectionsCreate']->{'title:ar'}       = 'create';
            $this->data['SectionsCreate']->{'description:ar'} = null;
            $this->data['SectionsCreate']->save();
    
            $this->data['SectionsUpdate'] = Ability::firstOrNew([
                'name' => $this->data['SectionService']->permissionNamespace('update')
            ]);
            $this->data['SectionsUpdate']->group_id           = $this->data['SectionsGroup']->id;
            $this->data['SectionsUpdate']->{'default:ar'}      = 'Y';
            $this->data['SectionsUpdate']->{'title:ar'}       = 'update';
            $this->data['SectionsUpdate']->{'description:ar'} = null;
            $this->data['SectionsUpdate']->save();
    
            $this->data['SectionsDelete'] = Ability::firstOrNew([
                'name' => $this->data['SectionService']->permissionNamespace('delete')
            ]);
            $this->data['SectionsDelete']->group_id           = $this->data['SectionsGroup']->id;
            $this->data['SectionsDelete']->{'default:ar'}      = 'Y';
            $this->data['SectionsDelete']->{'title:ar'}       = 'delete';
            $this->data['SectionsDelete']->{'description:ar'} = null;
            $this->data['SectionsDelete']->save();
    
            $this->data['SectionsRestore'] = Ability::firstOrNew([
                'name' => $this->data['SectionService']->permissionNamespace('restore')
            ]);
            $this->data['SectionsRestore']->group_id           = $this->data['SectionsGroup']->id;
            $this->data['SectionsRestore']->{'default:ar'}      = 'Y';
            $this->data['SectionsRestore']->{'title:ar'}       = 'restore';
            $this->data['SectionsRestore']->{'description:ar'} = null;
            $this->data['SectionsRestore']->save();
    
            $this->data['SectionsPermaDelete'] = Ability::firstOrNew([
                'name' => $this->data['SectionService']->permissionNamespace('permaDelete')
            ]);
            $this->data['SectionsPermaDelete']->group_id           = $this->data['SectionsGroup']->id;
            $this->data['SectionsPermaDelete']->{'default:ar'}      = 'Y';
            $this->data['SectionsPermaDelete']->{'title:ar'}       = 'permaDelete';
            $this->data['SectionsPermaDelete']->{'description:ar'} = null;
            $this->data['SectionsPermaDelete']->save();
    
            $this->data['SectionsStatusUpdate'] = Ability::firstOrNew([
                'name' => $this->data['SectionService']->permissionNamespace('statusUpdate')
            ]);
            $this->data['SectionsStatusUpdate']->group_id           = $this->data['SectionsGroup']->id;
            $this->data['SectionsStatusUpdate']->{'default:ar'}      = 'Y';
            $this->data['SectionsStatusUpdate']->{'title:ar'}       = 'statusUpdate';
            $this->data['SectionsStatusUpdate']->{'description:ar'} = null;
            $this->data['SectionsStatusUpdate']->save();
        }
    }

    /**
     * Create Module Roles.
     *
     * @return void
     */
    public function Roles()
    {
        $this->data['CONTENT_EDITOR_Role']                     = Role::firstOrNew(['name' => 'CONTENT_EDITOR']);
        $this->data['CONTENT_EDITOR_Role']->name               = 'CONTENT_EDITOR';
        $this->data['CONTENT_EDITOR_Role']->suspendable        = 'N';
        $this->data['CONTENT_EDITOR_Role']->updatable          = 'N';
        $this->data['CONTENT_EDITOR_Role']->{'default:en'}     = 'N';
        $this->data['CONTENT_EDITOR_Role']->{'title:en'}       = 'CONTENT_EDITOR';
        $this->data['CONTENT_EDITOR_Role']->{'description:en'} = 'CONTENT_EDITOR Group for all new users.';
        $this->data['CONTENT_EDITOR_Role']->{'default:ar'}     = 'Y';
        $this->data['CONTENT_EDITOR_Role']->{'title:ar'}       = 'محرر محتوى';
        $this->data['CONTENT_EDITOR_Role']->{'description:ar'} = 'مجموعة الاشخاص محرري المحتوى.';
        $this->data['CONTENT_EDITOR_Role']->save();

        $this->data['ADMIN_Role'] = Role::where('name', 'SYSTEM_ADMIN')->first();
        if ($this->data['ADMIN_Role']) {
            $this->data['ADMIN_Role']->abilities()->syncWithoutDetaching([
                $this->data['CategoryRead']->id,
                $this->data['CategoryCreate']->id,
                $this->data['CategoryUpdate']->id,
                $this->data['CategoryDelete']->id,
                $this->data['CategoryStatusUpdate']->id,

                $this->data['ContentRead']->id,
                $this->data['ContentCreate']->id,
                $this->data['ContentUpdate']->id,
                $this->data['ContentDelete']->id,
                $this->data['ContentStatusUpdate']->id,

                $this->data['SectionsRead']->id,
                $this->data['SectionsCreate']->id,
                $this->data['SectionsUpdate']->id,
                $this->data['SectionsDelete']->id,
                $this->data['SectionsStatusUpdate']->id,
            ]);
        }
        
        $this->data['CONTENT_EDITOR_Role']->abilities()->syncWithoutDetaching([
            $this->data['CategoryRead']->id,
            $this->data['CategoryCreate']->id,
            $this->data['CategoryUpdate']->id,
            $this->data['CategoryDelete']->id,
            $this->data['CategoryStatusUpdate']->id,

            $this->data['ContentRead']->id,
            $this->data['ContentCreate']->id,
            $this->data['ContentUpdate']->id,
            $this->data['ContentDelete']->id,
            $this->data['ContentStatusUpdate']->id,
        ]);
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
