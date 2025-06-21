<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lgl_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_number')->nullable()->index();
            $table->integer('company_user_id')->unsigned()->index();
            $table->integer('company_id')->unsigned()->index();
            $table->integer('transportar_user_id')->unsigned()->index();
            $table->integer('transportar_id')->unsigned()->index();
            $table->string('invoice_file')->nullable();
            $table->string('note')->nullable();

            $table->timestamp('invoiced_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->decimal('sub_total', 16, 2)->default(0.00);
            $table->decimal('discounts_total', 16, 2)->default(0.00);
            $table->decimal('total', 16, 2)->default(0.00);

            $table->string('sys_status')->default('PENDING')->comment('PENDING | PENDING_SUPPLIER_REVIEW | PAID');
            $table->integer('created_by_user_id')->unsigned()->nullable();
            $table->integer('updated_by_user_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('company_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('lgl_company_profile')->onDelete('cascade');
            $table->foreign('transportar_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('transportar_id')->references('id')->on('lgl_company_profile')->onDelete('cascade');
        });

        Schema::create('lgl_invoices_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned()->index();
            $table->integer('shippment_id')->unsigned()->index();
            $table->string('description')->nullable();
            
            $table->decimal('sub_cost', 16, 2)->default(0.00);
            $table->decimal('discount', 16, 2)->default(0.00);
            $table->decimal('cost', 16, 2)->default(0.00);

            $table->foreign('invoice_id')->references('id')->on('lgl_invoices')->onDelete('cascade');
            $table->foreign('shippment_id')->references('id')->on('lgl_shippments')->onDelete('cascade');
        });

        Schema::table('lgl_shippments', function (Blueprint $table) {
            $table->integer('invoice_id')->unsigned()->nullable()->index()->after('sys_status');
            $table->foreign('invoice_id')->references('id')->on('lgl_invoices')->onDelete('set null');
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
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
        });
        Schema::dropIfExists('lgl_invoices_items');
        Schema::dropIfExists('lgl_invoices');
    }
}
