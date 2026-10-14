<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippmentImportsTables extends Migration
{
    public function up()
    {
        Schema::create('lgl_shippment_imports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('original_filename');
            $table->string('disk', 50)->default('local');
            $table->string('file_path', 1000);
            $table->string('locale', 5)->default('en');
            $table->string('status', 40)->default('PENDING')->index();
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('processed_rows')->default(0);
            $table->unsignedInteger('created_count')->default(0);
            $table->unsignedInteger('updated_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('lgl_shippment_import_rows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('import_id')->index();
            $table->unsignedInteger('row_number');
            $table->string('import_key')->nullable()->index();
            $table->integer('shippment_id')->unsigned()->nullable()->index();
            $table->text('shippment_ids')->nullable();
            $table->string('operation', 20)->nullable();
            $table->string('status', 20)->default('PENDING')->index();
            $table->longText('payload')->nullable();
            $table->longText('errors')->nullable();
            $table->timestamps();

            $table->foreign('import_id')->references('id')->on('lgl_shippment_imports')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('shippment_id')->references('id')->on('lgl_shippments')->onUpdate('cascade')->onDelete('set null');
            $table->unique(['import_id', 'row_number'], 'lgl_shippment_import_rows_import_row_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lgl_shippment_import_rows');
        Schema::dropIfExists('lgl_shippment_imports');
    }
}
