<?php

namespace Modules\Address\Database\Seeders;

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
use Modules\Address\Entities;

class AddressDatabaseSeeder extends Seeder
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
        app(Entities\AddressService::class)->install();
    }

    /**
     * Create Module Roles.
     *
     * @return void
     */
    public function Roles()
    {
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
