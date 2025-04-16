<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShippmentsAddEquipmentLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_shippments', function (Blueprint $table) {
            $table->decimal('equipment_length', 3, 2)->default(0)->comment('length in meters')->after('temperature');
            $table->decimal('equipment_width', 3, 2)->default(0)->comment('width in meters')->after('equipment_length');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lgl_shippments', function (Blueprint $table) {
            $table->dropColumn('equipment_length');
            $table->dropColumn('equipment_width');
        });
    }
}
