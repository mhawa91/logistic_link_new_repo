<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLglInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgl_shippments', function (Blueprint $table) {
            $table->string('invoice_number')->nullable();
            $table->timestamp('invoice_date')->nullable();
            $table->decimal('invoice_amount', 16, 2)->default(0);
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
            $table->dropColumn('invoice_number');
            $table->dropColumn('invoice_date');
            $table->dropColumn('invoice_amount');
        });
    }
}
