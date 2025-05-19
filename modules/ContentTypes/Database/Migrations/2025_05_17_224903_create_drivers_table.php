<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lgl_drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transporter_user_id')->unsigned()->index()->nullable();
            $table->string('name')->nullable();
            $table->string('id_number')->nullable();
            $table->integer('nationality_country_id')->unsigned()->index()->nullable();
            $table->systemCrud();
            $table->foreign('transporter_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('nationality_country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lgl_drivers');
    }
}
