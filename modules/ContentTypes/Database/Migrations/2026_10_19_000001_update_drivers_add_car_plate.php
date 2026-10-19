<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateDriversAddCarPlate extends Migration
{
    public function up()
    {
        Schema::table('lgl_drivers', function (Blueprint $table) {
            $table->string('car_plate')->nullable()->after('id_number');
        });

        DB::statement("UPDATE lgl_drivers AS drivers
            SET drivers.car_plate = (
                SELECT shippments.car_plate
                FROM lgl_shippments AS shippments
                WHERE shippments.driver_id = drivers.id
                    AND shippments.car_plate IS NOT NULL
                    AND shippments.car_plate != ''
                    AND shippments.deleted_at IS NULL
                ORDER BY shippments.id DESC
                LIMIT 1
            )");
    }

    public function down()
    {
        Schema::table('lgl_drivers', function (Blueprint $table) {
            $table->dropColumn('car_plate');
        });
    }
}
