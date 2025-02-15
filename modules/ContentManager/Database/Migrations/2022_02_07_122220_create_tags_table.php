<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Model: Tag
        Schema::create('content_tags', function (Blueprint $table) {
            $table->increments('id');
        });

        // Model: TagTranslation
        Schema::create('contents_tags_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tag_id')->unsigned()->index();
            $table->char('locale', 2);
            $table->string('title', 191)->index();
            $table->unique(
                ['tag_id', 'locale'],
                'contents_tags_translations_tag_id_locale_unique'
            );
            $table->foreign('tag_id')
            ->references('id')->on('content_tags')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
        DB::statement('ALTER TABLE contents_tags_translations ADD FULLTEXT search(title)');

        // Pivot
        Schema::create('content_taggables', function (Blueprint $table) {
            $table->integer('tag_id')->unsigned()->index();
            $table->string('content_taggables_type')->index();
            $table->integer('content_taggables_id')->unsigned()->index();
            $table->index(
                ['tag_id', 'content_taggables_type', 'content_taggables_id'],
                'contents_connection_cttid_ctcid_index'
            );
            $table->unique(
                ['tag_id', 'content_taggables_type', 'content_taggables_id'],
                'contents_connection_cttid_ctcid_unique'
            );
            $table->foreign('tag_id')
            ->references('id')->on('content_tags')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        // Model: TagView
        Schema::create('contents_tags_views', function (Blueprint $table) {
            $table->integer('tag_id')->unsigned()->index();
            $table->string('taggable_type')->index();
            $table->integer('views_count')->default(0);
            $table->index(
                ['tag_id', 'taggable_type'],
                'contents_tags_views_index'
            );
            $table->unique(
                ['tag_id', 'taggable_type'],
                'contents_tags_views_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contents_tags_views');
        Schema::dropIfExists('content_taggables');
        Schema::dropIfExists('content_tags');
    }
}
