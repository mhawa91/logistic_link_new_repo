<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContractsAddRfqShippmentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_transporters_contracts', function (Blueprint $table) {
            $table->integer('shippment_id')->nullable()->unsigned()->index()->comment('only if is_rfq=YES');

            $table->foreign('shippment_id')
            ->references('id')->on('lgl_shippments')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::table('lgl_transporters_contracts_instances', function (Blueprint $table) {
            $table->integer('shippment_id')->nullable()->unsigned()->index()->comment('only if is_rfq=YES');

            $table->foreign('shippment_id')
            ->references('id')->on('lgl_shippments')
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
        Schema::table('lgl_transporters_contracts', function (Blueprint $table) {
            $table->dropForeign(['shippment_id']);
            $table->dropColumn('shippment_id');
        });

        Schema::table('lgl_transporters_contracts_instances', function (Blueprint $table) {
            $table->dropForeign(['shippment_id']);
            $table->dropColumn('shippment_id');
        });
    }
}
