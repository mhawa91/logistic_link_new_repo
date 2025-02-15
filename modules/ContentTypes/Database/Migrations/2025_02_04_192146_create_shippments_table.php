<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lgl_shippments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_user_id')->unsigned()->index();
            $table->integer('transporter_user_id')->unsigned()->index()->nullable();
            $table->enum('is_rfq', ['YES', 'NO'])->default('NO')->index();

            $table->decimal('total_weight', 16, 2)->default(0)->comment('weight in tons');
            $table->decimal('decalred_value', 16, 2)->default(0)->comment('value in SAR');

            $table->integer('load_size_id')->unsigned()->index('lgl_s_load_size_id_index');
            $table->integer('load_type_id')->unsigned()->index('lgl_s_load_type_id_index');
            $table->decimal('number_of_pallets', 16, 2)->default(0)->comment('number of pallets');

            $table->integer('number_of_trucks')->unsigned();
            $table->integer('truck_type_id')->unsigned()->index();
            $table->decimal('temperature', 3, 2)->default(0)->comment('temperature in celsius');
            $table->timestamp('delivery_required_at')->nullable();

            $table->text('public_notes')->nullable();
            $table->text('private_notes')->nullable();

            $table->enum('type', ['CITY_TO_CITY', 'CITY_TO_CITY_MULTIPLE_LOCATIONS'])->index();

            // PIVOT: has many file [file_tag=ADDITIONAL_DATA, file_tag=WAY_BILL]
            // PIVOT: has one loading point address [address_tag=SHIPPMENT_LOADING_POINT]
            // PIVOT: has many offloading point address [address_tag=SHIPPMENT_OFFLOADING_POINT]

            $table->integer('contract_id')->unsigned()->index()->nullable()->comment('NULL only when is_rfq=YES and the contract (RFQ) is not yet created');
            $table->integer('contract_instance_id')->unsigned()->index()->nullable()->comment('NULL only when is_rfq=YES and the contract (RFQ) is not yet created');
            $table->decimal('price', 16, 2);
            $table->decimal('pre_additional_location_price', 16, 2);
            $table->decimal('total_price', 16, 2);

            $table->string('sys_status', 50)->index()->default('DRAFT')->comment('DRAFT | PENDING_TRANSPORTER_APPROVAL | TRANSPORTER_APPROVED_WITH_PRICE_CHANGE | PENDING_PICKUP | PENDING_DELIVERY | DELIVERED_PENDING_PAYMENT | COMPLETED | CANCELLED');
            $table->integer('created_by_user_id')->unsigned()->nullable();
            $table->integer('updated_by_user_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('company_user_id', 'lgl_s_company_user_id_fk')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('transporter_user_id', 'lgl_s_transporter_user_id_fk')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('load_size_id', 'lgl_s_load_size_id_fk')
            ->references('id')->on('contents')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('load_type_id', 'lgl_s_load_type_id_fk')
            ->references('id')->on('contents')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('truck_type_id', 'lgl_s_truck_type_id_fk')
            ->references('id')->on('contents')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::create('lgl_shippments_commodities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shippment_id')->unsigned()->index('lgl_sc_shippment_id_index');
            $table->integer('commodity_id')->unsigned()->index('lgl_sc_commodity_id_index');

            $table->foreign('shippment_id', 'lgl_sc_shippment_id_fk')
            ->references('id')->on('lgl_shippments')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('commodity_id', 'lgl_sc_commodity_id_fk')
            ->references('id')->on('contents')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::create('lgl_shippments_timeline', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shippment_id')->unsigned()->index('lgl_st_shippment_id_index');
            $table->integer('user_id')->unsigned()->index();
            $table->string('status', 50)->index();
            $table->timestamps();

            $table->foreign('shippment_id', 'lgl_st_shippment_id_fk')
            ->references('id')->on('lgl_shippments')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::create('lgl_shippments_locations_status', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shippment_id')->unsigned()->index('lgl_sls_shippment_id_index');
            $table->integer('location_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->string('status', 50)->index();
            $table->timestamps();

            $table->foreign('shippment_id', 'lgl_sls_shippment_id_fk')
            ->references('id')->on('lgl_shippments')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('location_id', 'lgl_sls_location_id_fk')
            ->references('id')->on('lgl_addressess')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::create('lgl_shippments_matched_transporters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shippment_id')->unsigned()->index('lgl_smt_shippment_id_index');
            $table->integer('company_user_id')->unsigned()->index('lgl_smt_company_user_id_index');
            $table->integer('transporter_user_id')->unsigned()->index('lgl_smt_transporter_user_id_index');

            $table->decimal('price', 16, 2);
            $table->decimal('pre_additional_location_price', 16, 2);
            $table->decimal('total_price', 16, 2);

            $table->enum('status', ['PENDING', 'RESPONSE_DELAYED', 'ACCEPTED', 'REJECTED'])->default('PENDING')->index();
            $table->timestamp('requested_at');
            $table->timestamp('responded_at')->nullable();

            $table->foreign('shippment_id', 'lgl_smt_shippment_id_fk')
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
        Schema::dropIfExists('lgl_shippments_matched_transporters');
        Schema::dropIfExists('lgl_shippments_locations_status');
        Schema::dropIfExists('lgl_shippments_timeline');
        Schema::dropIfExists('lgl_shippments_commodities');
        Schema::dropIfExists('lgl_shippments');
    }
}
