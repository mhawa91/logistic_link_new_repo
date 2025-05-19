<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShippmentsAddNewPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_shippments', function (Blueprint $table) {
            $table->decimal('updated_price', 16, 2)->nullable();
            $table->decimal('updated_pre_additional_location_price', 16, 2)->nullable();
            $table->decimal('updated_total_price', 16, 2)->nullable();
        });

        Schema::table('lgl_shippments_matched_transporters', function (Blueprint $table) {
            $table->decimal('updated_price', 16, 2)->nullable();
            $table->decimal('updated_pre_additional_location_price', 16, 2)->nullable();
            $table->decimal('updated_total_price', 16, 2)->nullable();
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
            $table->dropColumn('updated_price');
            $table->dropColumn('updated_pre_additional_location_price');
            $table->dropColumn('updated_total_price');
        });

        Schema::table('lgl_shippments_matched_transporters', function (Blueprint $table) {
            $table->dropColumn('updated_price');
            $table->dropColumn('updated_pre_additional_location_price');
            $table->dropColumn('updated_total_price');
        });
    }
}
