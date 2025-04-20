<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShippmentsMathedTransportersAddChangedContractInstance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_shippments_matched_transporters', function (Blueprint $table) {
            $table->integer('approved_contract_instance_id')->unsigned()->index('lgl_s_a_con_instance_id_index')->nullable();
            $table->foreign('approved_contract_instance_id', 'lgl_s_a_con_instance_id_fk')
            ->references('id')->on('lgl_transporters_contracts_instances')
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
            $table->dropForeign('lgl_s_a_con_instance_id_fk');
            $table->dropIndex('lgl_s_a_con_instance_id_index');
            $table->dropColumn('approved_contract_instance_id');
        });
    }
}
