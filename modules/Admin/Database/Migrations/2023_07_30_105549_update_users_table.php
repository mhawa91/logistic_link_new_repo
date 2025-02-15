<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 75)->nullable();
            $table->string('last_name', 75)->nullable();
            $table->integer('country_id')->unsigned()->nullable();

            $table->foreign('country_id')
            ->references('id')->on('countries')
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign([
                'country_id',
            ]);
            $table->dropColumn([
                'first_name',
                'last_name',
                'country_id',
            ]);
        });
    }
}
