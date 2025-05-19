<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLglShippmentReviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lgl_shippments_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shippment_id')->unsigned()->index();
            $table->integer('company_user_id')->unsigned()->index();
            $table->integer('company_id')->unsigned()->index();
            $table->integer('transportar_user_id')->unsigned()->index();
            $table->integer('transportar_id')->unsigned()->index();
            $table->enum('rate', [0, 1, 2, 3, 4, 5])->index();
            $table->timestamps();

            $table->foreign('shippment_id')->references('id')->on('lgl_shippments')->onDelete('cascade');
            $table->foreign('company_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('lgl_company_profile')->onDelete('cascade');
            $table->foreign('transportar_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('transportar_id')->references('id')->on('lgl_company_profile')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lgl_shippments_reviews');
    }
}
