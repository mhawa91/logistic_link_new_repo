<?php

namespace Modules\SystemCore\Database\Seeders;

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
use Modules\SystemCore\Entities\Country;
use Modules\SystemCore\Entities\City;
use Modules\Admin\Entities\RoleService;

use Modules\SystemCore\Entities;

class SystemCoreDatabaseSeeder extends Seeder
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

        DB::transaction(function(){
            $this->Permissions();
            $this->Roles();
            $this->CountriesAndCities();
        });
        DB::transaction(function(){
            $this->Users();
            $this->Install();
        });
    }

    /**
     * Post Module Installation.
     *
     * @return void
     */
    public function Install()
    {
        app(Entities\CountriesService::class)->install();
        app(Entities\CitiesService::class)->install();
        app(Entities\MunicipalitiesService::class)->install();
        app(Entities\NeighbourhoodsService::class)->install();
    }

    /**
     * Create Module Permissions.
     *
     * @return void
     */
    public function Permissions()
    {
    }

    /**
     * Create Module Roles.
     *
     * @return void
     */
    public function Roles()
    {
        $this->data['Default_Role']                     = Role::firstOrNew(['name' => 'DEFAULT']);
        $this->data['Default_Role']->name               = 'DEFAULT';
        $this->data['Default_Role']->suspendable        = 'N';
        $this->data['Default_Role']->updatable          = 'Y';
        $this->data['Default_Role']->{'default:en'}     = 'N';
        $this->data['Default_Role']->{'title:en'}       = 'Customer';
        $this->data['Default_Role']->{'description:en'} = 'Customers / Clients Default Group.';
        $this->data['Default_Role']->{'default:ar'}     = 'Y';
        $this->data['Default_Role']->{'title:ar'}       = 'عميل';
        $this->data['Default_Role']->{'description:ar'} = 'مجموعة العملاء الافتراضية.';
        $this->data['Default_Role']->save();

        $this->data['ROOT_Role']                     = Role::firstOrNew(['name' => 'ROOT']);
        $this->data['ROOT_Role']->name               = 'ROOT';
        $this->data['ROOT_Role']->suspendable        = 'N';
        $this->data['ROOT_Role']->updatable          = 'N';
        $this->data['ROOT_Role']->{'default:en'}     = 'N';
        $this->data['ROOT_Role']->{'title:en'}       = 'Root Group';
        $this->data['ROOT_Role']->{'description:en'} = 'All Permissions Are Assigned To This Group By Default.';
        $this->data['ROOT_Role']->{'default:ar'}     = 'Y';
        $this->data['ROOT_Role']->{'title:ar'}       = 'مطور';
        $this->data['ROOT_Role']->{'description:ar'} = 'جميع الصلاحيات مضافة افتراضيا الى هذه المجموعة.';
        $this->data['ROOT_Role']->save();

        $this->data['ADMIN_Role']                     = Role::firstOrNew(['name' => 'SYSTEM_ADMIN']);
        $this->data['ADMIN_Role']->name               = 'SYSTEM_ADMIN';
        $this->data['ADMIN_Role']->suspendable        = 'N';
        $this->data['ADMIN_Role']->updatable          = 'Y';
        $this->data['ADMIN_Role']->{'default:en'}     = 'N';
        $this->data['ADMIN_Role']->{'title:en'}       = 'Admin Group';
        $this->data['ADMIN_Role']->{'description:en'} = 'All Permissions Are Assigned To This Group By Default.';
        $this->data['ADMIN_Role']->{'default:ar'}     = 'Y';
        $this->data['ADMIN_Role']->{'title:ar'}       = 'مدير النظام';
        $this->data['ADMIN_Role']->{'description:ar'} = 'جميع الصلاحيات مضافة افتراضيا الى هذه المجموعة.';
        $this->data['ADMIN_Role']->save();
    }

    /**
     * Create Module Needed Users.
     *
     * @return void
     */
    public function Users()
    {
        foreach (config('systemcore.users') ?? [] as $key => $data) {
            $Role = Role::where('name', $data['type'])->firstOrFail();
            $User = app(config('systemcore.models.User'))->firstOrNew([
                'username' => $data['username'],
                'email'    => $data['email'],
            ]);
            $User->password       = Hash::make($data['password']);
            $User->name           = $data['name'];
            $User->type           = $Role->name;
            $User->completed_at   = Carbon::now()->format('Y-m-d H:i:s');
            $User->reviewd_at     = Carbon::now()->format('Y-m-d H:i:s');
            $User->sys_status     = 'ACTIVE';
            $User->save();
            Bouncer::assign($Role->name)->to($User);
        }
    }

    /**
     * Insert Countries from seed template
     *
     * @return void
     */
    public function CountriesAndCities()
    {
        if (Country::count() > 0) {
            return;
        }
        $Countries = collect(require_once(
            module_path('SystemCore') . '/Database/Templates/countries.php'
        ));
        $Cities = collect(require_once(
            module_path('SystemCore') . '/Database/Templates/cities.php'
        ));
        $Countries->map(function($Country) use($Cities) {
            $CountryCities  = $Cities->where('country_id', $Country['id']);
            $CreatedCountry = Country::create($Country);
            City::insert($CountryCities->toArray());
        });
        City::query()->update([
            'sys_status' => 'ACTIVE',
        ]);
        Country::query()->update([
            'sys_status' => 'ACTIVE',
        ]);
    }
}
