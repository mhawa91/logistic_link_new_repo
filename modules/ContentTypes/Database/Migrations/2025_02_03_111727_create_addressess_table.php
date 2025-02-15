<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Model: Address
        Schema::create('lgl_addressess', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('related_id')->unsigned()->index();
            $table->string('related_type', 255)->index();
            $table->string('address_tag', 50)->nullable()->index();

            $table->integer('country_id')->nullable()->unsigned();
            $table->integer('city_id')->nullable()->unsigned();
            $table->string('street', 500)->nullable();
            $table->string('postal_code', 50)->nullable();
            $table->string('line_1', 250)->nullable();
            $table->string('line_2', 250)->nullable();
            $table->text('full_address')->nullable();
            $table->text('public_notes')->nullable();
            $table->text('private_notes')->nullable();
            $table->string('primary_contact_company_name', 255)->nullable();
            $table->string('primary_contact_source_name', 255)->nullable();
            $table->string('primary_contact_name', 255)->nullable();
            $table->string('primary_contact_email', 255)->nullable();
            $table->string('primary_contact_phone_number_ext', 5)->nullable();
            $table->string('primary_contact_phone_number', 20)->nullable();

            $table->decimal('lat', 11, 8);
            $table->decimal('lng', 11, 8);

            $table->enum('round_trip', ['YES', 'NO'])->nullable()->index()->comment('required only for shippments locations (loading and offloading points)');

            $table->systemCrud();

            $table->index(['related_id', 'related_type', 'address_tag'], 'addressess_related_id_type_tag_index');

            $table->foreign('country_id')
            ->references('id')->on('countries')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('city_id')
            ->references('id')->on('cities')
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
        Schema::dropIfExists('lgl_addressess');
    }
}
