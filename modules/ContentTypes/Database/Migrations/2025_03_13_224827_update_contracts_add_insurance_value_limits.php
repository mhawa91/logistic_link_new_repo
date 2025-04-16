<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContractsAddInsuranceValueLimits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_transporters_contracts_instances', function (Blueprint $table) {
            $table->decimal('insurance_value_limit', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lgl_transporters_contracts_instances', function (Blueprint $table) {
            $table->dropColumn('insurance_value_limit');
        });
    }
}
