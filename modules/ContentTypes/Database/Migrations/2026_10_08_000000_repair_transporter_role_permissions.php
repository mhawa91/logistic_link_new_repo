<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\ContentTypes\Entities\Shippment;
use Modules\SystemCore\Entities\Ability;
use Modules\SystemCore\Entities\Role;
use Silber\Bouncer\Bouncer;

class RepairTransporterRolePermissions extends Migration
{
    /**
     * Repair the shipment read permission that was skipped when the
     * TRANSPORTER role was created after the ContentTypes services ran.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function() {
            $role = Role::where('name', 'TRANSPORTER')->first();

            if (! $role) {
                return;
            }

            $ability = Ability::where(
                'name',
                str_replace('\\', '_', Shippment::class) . '@read'
            )->first();

            if ($ability) {
                $role->abilities()->syncWithoutDetaching([$ability->id]);
                app(Bouncer::class)->refresh();
            }
        });
    }

    /**
     * Permission repair is intentionally not reversed because some or all of
     * these permissions may have been assigned before this migration ran.
     *
     * @return void
     */
    public function down()
    {
        // No destructive rollback for existing role permissions.
    }
}
