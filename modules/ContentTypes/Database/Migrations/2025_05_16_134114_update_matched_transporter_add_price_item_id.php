<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMatchedTransporterAddPriceItemId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_shippments_matched_transporters', function (Blueprint $table) {
            $table->integer('contract_instance_pricing_id')->unsigned()->index('lgl_smt_cipi_id_index')->nullable();

            $table->foreign('contract_instance_pricing_id', 'lgl_smt_cipi_id_fk')
            ->references('id')->on('lgl_transporters_contracts_pricing')
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
        Schema::table('lgl_shippments_matched_transporters', function (Blueprint $table) {
            $table->dropForeign('lgl_smt_cipi_id_fk');
            $table->dropIndex('lgl_smt_cipi_id_index');
            $table->dropColumn(['contract_instance_pricing_id']);
        });
    }
}
