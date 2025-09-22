<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatesUsersAddHideMyInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_company_profile', function (Blueprint $table) {
            $table->string('hide_info')->default('NO');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lgl_company_profile', function (Blueprint $table) {
            $table->dropColumn([
                'hide_info'
            ]);
        });
    }
}
