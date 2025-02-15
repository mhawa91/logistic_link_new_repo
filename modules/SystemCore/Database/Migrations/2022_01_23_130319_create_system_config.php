<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Model: SystemConfig - Contract: SystemConfigContract - Service: SystemConfigService
        Schema::create('system_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('x_key', 50);
            $table->longText('x_value')->nullable();
            $table->systemCrud();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_config');
    }
}
