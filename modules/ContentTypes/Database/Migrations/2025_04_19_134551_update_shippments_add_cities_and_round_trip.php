<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShippmentsAddCitiesAndRoundTrip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_shippments', function (Blueprint $table) {
            $table->integer('from_country_id')->nullable()->unsigned()->index();
            $table->integer('from_city_id')->nullable()->unsigned()->index();

            $table->integer('to_country_id')->nullable()->unsigned()->index();
            $table->integer('to_city_id')->nullable()->unsigned()->index();

            $table->enum('round_trip', ['YES', 'NO'])->default('NO')->index()->comment('required only for shippments locations (loading and offloading points)');

            $table->foreign('from_country_id')
            ->references('id')->on('countries')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('from_city_id')
            ->references('id')->on('cities')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('to_country_id')
            ->references('id')->on('countries')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('to_city_id')
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
        Schema::table('lgl_shippments', function (Blueprint $table) {
            $table->dropForeign(['from_country_id']);
            $table->dropForeign(['from_city_id']);
            $table->dropForeign(['to_country_id']);
            $table->dropForeign(['to_city_id']);

            $table->dropIndex(['from_country_id']);
            $table->dropIndex(['from_city_id']);
            $table->dropIndex(['to_country_id']);
            $table->dropIndex(['to_city_id']);
            $table->dropIndex(['round_trip']);

            $table->dropColumn('from_country_id');
            $table->dropColumn('from_city_id');
            $table->dropColumn('to_country_id');
            $table->dropColumn('to_city_id');
            $table->dropColumn('round_trip');
        });
    }
}
