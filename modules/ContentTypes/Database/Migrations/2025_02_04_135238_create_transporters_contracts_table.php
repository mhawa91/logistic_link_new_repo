<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransportersContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lgl_transporters_contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_user_id')->unsigned()->index();
            $table->integer('transporter_user_id')->unsigned()->index();

            $table->enum('is_rfq', ['YES', 'NO'])->default('NO')->index();
            // $table->integer('shippment_id')->unsigned()->index()->comment('only if is_rfq=YES');

            $table->timestamp('order_start_date')->nullable()->comment('if is_rfq==YES then contract start date else Order date');
            $table->timestamp('order_end_date')->nullable()->comment('if is_rfq==YES then contract end date else Order date');

            $table->systemCrud();

            $table->foreign('company_user_id')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('transporter_user_id')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::create('lgl_transporters_contracts_instances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id')->unsigned()->index();
            $table->integer('company_user_id')->unsigned()->index();
            $table->integer('transporter_user_id')->unsigned()->index();
            $table->integer('payment_term_id')->unsigned()->index();

            $table->enum('is_rfq', ['YES', 'NO'])->default('NO')->index();
            // $table->integer('shippment_id')->unsigned()->index()->comment('only if is_rfq=YES');

            $table->timestamp('order_start_date')->nullable()->comment('if is_rfq==YES then contract start date else Order date');
            $table->timestamp('order_end_date')->nullable()->comment('if is_rfq==YES then contract end date else Order date');

            $table->enum('is_current', ['YES', 'NO'])->default('NO')->index();
            $table->systemCrud();

            // PIVOT: has many files 
            // [file_tag=TRANSPORTER_TAX_CERTIFICATE, file_tag=TRANSPORTER_REGISTAION_LICENCE]
            // [file_tag=TRANSPORTER_INSURANCE_CERTIFICATE, file_tag=TRANSPORTER_TRANSPORTATION_LICENCE]

            $table->foreign('contract_id')
            ->references('id')->on('lgl_transporters_contracts')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('company_user_id')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('transporter_user_id')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('payment_term_id')
            ->references('id')->on('contents')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::create('lgl_transporters_contracts_metadata', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id')->unsigned()->index();
            $table->integer('contract_instance_id')->unsigned()->index();
            $table->integer('company_user_id')->unsigned()->index();
            $table->integer('transporter_user_id')->unsigned()->index();
            $table->enum('type', ['TERMS_AND_CONDITIONS', 'INSURANCE_DETAILS', 'PENALITIES'])->index();
            $table->text('description')->nullable();

            $table->foreign('contract_id')
            ->references('id')->on('lgl_transporters_contracts')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('contract_instance_id')
            ->references('id')->on('lgl_transporters_contracts_instances')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('company_user_id')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('transporter_user_id')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::create('lgl_transporters_contracts_commodities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id')->unsigned()->index('lgl_tcc_contract_id_index');
            $table->integer('contract_instance_id')->unsigned()->index('lgl_tcc_contract_instance_id_index');
            $table->integer('company_user_id')->unsigned()->index('lgl_tcc_company_user_id_index');
            $table->integer('transporter_user_id')->unsigned()->index('lgl_tcc_transporter_user_id_index');
            $table->integer('commodity_id')->unsigned()->index('lgl_tcc_commodity_id_index');

            $table->foreign('contract_id', 'lgl_tcc_contract_id_fk')
            ->references('id')->on('lgl_transporters_contracts')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('contract_instance_id', 'lgl_tcc_contract_instance_id_fk')
            ->references('id')->on('lgl_transporters_contracts_instances')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('company_user_id', 'lgl_tcc_company_user_id_fk')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('transporter_user_id', 'lgl_tcc_transporter_user_id_fk')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('commodity_id', 'lgl_tcc_commodity_id_fk')
            ->references('id')->on('contents')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::create('lgl_transporters_contracts_pricing', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id')->unsigned()->index();
            $table->integer('contract_instance_id')->unsigned()->index();
            $table->integer('company_user_id')->unsigned()->index();
            $table->integer('transporter_user_id')->unsigned()->index();
            
            $table->integer('truck_type_id')->unsigned()->index();
            $table->enum('type', ['CITY_TO_CITY', 'CITY_TO_CITY_MULTIPLE_LOCATIONS', 'CITY_TO_CITY_ROUND_TRIP'])->index();
            $table->integer('from_country_id')->unsigned()->index();
            $table->integer('from_city_id')->unsigned()->index();

            $table->integer('to_country_id')->unsigned()->index();
            $table->integer('to_city_id')->unsigned()->index();

            $table->decimal('price', 10, 2);
            $table->decimal('pre_additional_location_price', 10, 2);

            $table->foreign('contract_id', 'lgl_tcp_contract_id_fk')
            ->references('id')->on('lgl_transporters_contracts')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('contract_instance_id', 'lgl_tcp_contract_instance_id_fk')
            ->references('id')->on('lgl_transporters_contracts_instances')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('company_user_id', 'lgl_tcp_company_user_id_fk')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('transporter_user_id', 'lgl_tcp_transporter_user_id_fk')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('truck_type_id', 'lgl_tcp_truck_type_id_fk')
            ->references('id')->on('contents')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('from_country_id', 'lgl_tcp_from_country_id_fk')
            ->references('id')->on('countries')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('from_city_id', 'lgl_tcp_from_city_id_fk')
            ->references('id')->on('cities')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('to_country_id', 'lgl_tcp_to_country_id_fk')
            ->references('id')->on('countries')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('to_city_id', 'lgl_tcp_to_city_id_fk')
            ->references('id')->on('cities')
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
        Schema::dropIfExists('lgl_transporters_contracts_pricing');
        Schema::dropIfExists('lgl_transporters_contracts_commodities');
        Schema::dropIfExists('lgl_transporters_contracts_metadata');
        Schema::dropIfExists('lgl_transporters_contracts_instances');
        Schema::dropIfExists('lgl_transporters_contracts');
    }
}
