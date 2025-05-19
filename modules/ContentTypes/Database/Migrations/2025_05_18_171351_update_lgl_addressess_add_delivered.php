<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLglAddressessAddDelivered extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_addressess', function (Blueprint $table) {
            $table->timestamp('delivered_at')->nullable()->after('sys_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lgl_addressess', function (Blueprint $table) {
            $table->dropColumn('delivered_at');
        });
    }
}
