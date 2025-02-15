<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesAndCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Model: Country
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->char('sortname', 2);
            $table->string('name_en', 100)->nullable();
            $table->string('name_ar', 100)->nullable();
            $table->string('flag', 191)->nullable();
            $table->string('code', 10)->nullable();
            $table->string('currency', 6)->nullable();
            $table->string('currency_icon', 6)->nullable();
            $table->string('status', 6)->nullable();
            $table->systemCrud();
        });
        
        // Model: City
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->unsigned();
            $table->string('name_en', 100)->nullable();
            $table->string('name_ar', 100)->nullable();
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
        Schema::dropIfExists('cities');
        Schema::dropIfExists('countries');
    }
}
