<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShippmentsMathedTransportersAddContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_shippments_matched_transporters', function (Blueprint $table) {
            $table->integer('contract_id')->unsigned()->index()->nullable()->comment('NULL only when is_rfq=YES and the contract (RFQ) is not yet created');
            $table->integer('contract_instance_id')->unsigned()->index()->nullable()->comment('NULL only when is_rfq=YES and the contract (RFQ) is not yet created');

            $table->foreign('contract_id', 'lgl_s_contract_id_fk')
            ->references('id')->on('lgl_transporters_contracts')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('contract_instance_id', 'lgl_s_contract_instance_id_fk')
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
            $table->dropForeign('lgl_s_contract_id_fk');
            $table->dropForeign('lgl_s_contract_instance_id_fk');
            $table->dropColumn(['contract_id', 'contract_instance_id']);
        });
    }
}
