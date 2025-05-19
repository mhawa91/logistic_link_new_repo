<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCompanyProfileAddRegTaxStrings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_company_profile', function (Blueprint $table) {
            $table->string('registration_number_string')->nullable()->after('registration_number');
            $table->string('tax_identification_number_string')->nullable()->after('tax_identification_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lgl_company_profile', function (Blueprint $table) {
            $table->dropColumn('registration_number_string');
            $table->dropColumn('tax_identification_number_string');
        });
    }
}
