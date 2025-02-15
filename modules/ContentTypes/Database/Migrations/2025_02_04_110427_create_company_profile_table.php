<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Model: CompanyProfile
        Schema::create('lgl_company_profile', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('profile_type_category_id')->nullable()->unsigned()->index();
            $table->integer('registration_type_category_id')->nullable()->unsigned()->index();

            $table->string('company_logo', 255)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('contact_name', 255)->nullable();
            $table->string('company_email', 255)->nullable();
            $table->string('company_phone_number_ext', 5)->nullable();
            $table->string('company_phone_number', 20)->nullable();
            $table->string('registration_number', 50)->nullable();
            $table->string('tax_identification_number', 50)->nullable();

            // PIVOT: has one primary address [address_tag=PRIMARY_COMPANY_ADDRESS]
            // PIVOT: has many files [file_tag=REGISTRATION_CERTIFICATE, file_tag=TAX_IDENTIFICATION]

            $table->systemCrud();

            $table->foreign('user_id')
            ->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('profile_type_category_id')
            ->references('id')->on('contents')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('registration_type_category_id')
            ->references('id')->on('contents')
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
        Schema::dropIfExists('lgl_company_profile');
    }
}
