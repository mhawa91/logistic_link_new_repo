<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippmentsRfqTransporters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lgl_shippments_rfq_transporters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shippment_id')->unsigned()->index('lgl_srt_shippment_id_index');
            $table->integer('company_user_id')->unsigned()->index('lgl_srt_company_user_id_index');
            $table->integer('transporter_user_id')->unsigned()->index('lgl_srt_transporter_user_id_index');
            $table->integer('contract_id')->unsigned()->index('lgl_srt_transporter_contract_id_index')->nullable()->comment('NULL only when is_rfq=YES and the contract (RFQ) is not yet created');
            $table->integer('contract_instance_id')->unsigned()->index('lgl_srt_transporter_contract_instance_id_index')->nullable()->comment('NULL only when is_rfq=YES and the contract (RFQ) is not yet created');

            $table->decimal('price', 16, 2);
            $table->decimal('pre_additional_location_price', 16, 2);
            $table->decimal('total_price', 16, 2);

            $table->enum('status', ['PENDING', 'PENDING_RESPONSE', 'RESPONSE_DELAYED', 'PENDING_ACCEPTANCE', 'ACCEPTED', 'REJECTED'])->default('PENDING')->index();
            $table->timestamp('requested_at');
            $table->timestamp('responded_at')->nullable();

            $table->foreign('shippment_id', 'lgl_srt_shippment_id_fk')
            ->references('id')->on('lgl_shippments')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('contract_id', 'lgl_srt_contract_id_fk')
            ->references('id')->on('lgl_transporters_contracts')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('contract_instance_id', 'lgl_srt_contract_instance_id_fk')
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
        Schema::dropIfExists('lgl_shippments_rfq_transporters');
    }
}
