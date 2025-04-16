<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Model: Address - Service: AddressesService
        Schema::create('addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();

            $table->string('title' , 191);
            $table->enum('type' , ['HOME', 'WORK', 'OTHER'])->nullable();

            $table->string('phone_number' , 30)->nullable();
            $table->string('email' , 191)->nullable();

            $table->integer('country_id')->unsigned()->nullable();
            $table->integer('city_id')->unsigned()->nullable();
            $table->integer('municipality_id')->unsigned()->nullable();
            $table->integer('neighbourhood_id')->unsigned()->nullable();
            $table->string('postal_code', 50)->nullable();

            $table->string('street', 191)->nullable();
            $table->string('building', 191)->nullable();
            $table->string('floor', 191)->nullable();
            $table->string('appartment', 191)->nullable();
            $table->string('instructions', 2500)->nullable();
            $table->string('full_address', 2500)->nullable();

            $table->decimal('lat', 11, 8);
            $table->decimal('lng', 11, 8);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'lat', 'lng', 'deleted_at'], 'addresses_uid_lat_lng_dat_index');
            $table->index(['lat', 'lng', 'deleted_at'], 'addresses_lat_lng_dat_index');

            $table->foreign('user_id')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('country_id')
            ->references('id')->on('countries')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('city_id')
            ->references('id')->on('cities')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('municipality_id')
            ->references('id')->on('municipalities')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('neighbourhood_id')
            ->references('id')->on('neighbourhoods')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
