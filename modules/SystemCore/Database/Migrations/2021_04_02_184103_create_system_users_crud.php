<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemUsersCrud extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->nullable();
            $table->string('username' , 191)->nullable()->unique();
            $table->string('phone_number' , 30)->nullable()->unique();
            $table->string('email' , 191)->nullable()->unique();
            $table->string('avatar', 500)->nullable();
            $table->string('password', 500)->nullable();
            $table->char('locale', 2)->default('ar');
            $table->string('type', 50)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('reviewd_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
