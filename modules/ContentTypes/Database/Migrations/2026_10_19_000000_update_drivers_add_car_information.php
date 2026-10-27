<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDriversAddCarInformation extends Migration
{
    public function up()
    {
        Schema::table('lgl_drivers', function (Blueprint $table) {
            $table->text('car_information')->nullable()->after('id_number');
        });
    }

    public function down()
    {
        Schema::table('lgl_drivers', function (Blueprint $table) {
            $table->dropColumn('car_information');
        });
    }
}
