<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactUsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Moel: ContactUs
        Schema::create('contact_us', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email' , 191)->nullable();
            $table->string('phone_number' , 191)->nullable();
            $table->string('name' , 191)->nullable();
            $table->string('subject' , 255)->nullable();
            $table->text('message' , 5000)->nullable();
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
        Schema::dropIfExists('contact_us');
    }
}
