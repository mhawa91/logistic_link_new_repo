<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShippmentAddDriverId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_shippments', function (Blueprint $table) {
            $table->integer('driver_id')->unsigned()->index('lgl_shippments_driver_id_index')->nullable()->after('transporter_user_id');
            $table->foreign('driver_id', 'lgl_s_driver_id_fk')
            ->references('id')->on('lgl_drivers')
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
        Schema::table('lgl_shippments', function (Blueprint $table) {
            $table->dropForeign('lgl_s_driver_id_fk');
            $table->dropIndex('lgl_shippments_driver_id_index');
            $table->dropColumn('driver_id');
        });
    }
}
