<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatedFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Model: RelatedFile
        Schema::create('lgl_related_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('related_id')->unsinged()->index();
            $table->string('related_type', 255)->nullable()->index();
            $table->string('file_tag', 50)->nullable()->index();
            $table->string('file_name', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('file_size', 255)->nullable();
            $table->string('file_extension', 255)->nullable();
            $table->systemCrud();

            $table->index(['related_id', 'related_type', 'file_tag'], 'related_files_related_id_type_tag_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lgl_related_files');
    }
}
