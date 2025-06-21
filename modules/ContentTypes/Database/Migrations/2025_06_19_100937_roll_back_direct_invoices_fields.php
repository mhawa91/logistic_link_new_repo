<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RollBackDirectInvoicesFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('lgl_shippments', function (Blueprint $table) {
                $table->dropColumn('invoice_number');
                $table->dropColumn('invoice_date');
                $table->dropColumn('invoice_amount');
                $table->dropColumn('invoice_file');
            });
        } catch (\Exception $e) {
            // Handle the exception if the columns do not exist
            // This is optional, you can log the error or ignore it
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
