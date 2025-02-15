<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsCrud extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Model: Content
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('base_type', ['PAGE', 'CATEGORY', 'POST'])->nullable()->comment('Base Type');
            $table->string('type', 50)->nullable()->comment('EX: ARTICLE | REPORT | TEAM_MEMBER | ANY ENTITY');
            $table->integer('parent_id')->unsigned()->nullable()->comment('Only connected to base_type == CATEGORY => CATEGORY | POST => POST');
            $table->integer('ordering')->unsigned()->default(1);
            $table->timestamp('published_at');
            $table->systemCrud();
            $table->foreign('parent_id')
            ->references('id')->on('contents')
            ->onUpdate('cascade')
            ->onDelete('set NULL');
        });

        // Model: ContentTranslation
        Schema::create('contents_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('content_id')->unsigned()->index();
            $table->char('default', 1)->default('Y');
            $table->char('locale', 2);
            $table->string('title', 191)->nullable();
            $table->string('short_content', 5000)->nullable()->comment('Short content for preview and seo');
            $table->longText('long_content')->nullable()->comment('HTML Detailed Content');
            $table->string('image', 500)->nullable();
            $table->string('url', 1000)->nullable();
            $table->unique(
                ['content_id', 'locale'],
                'contents_translations_content_id_locale_unique'
            );
            $table->foreign('content_id')
            ->references('id')->on('contents')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        // Model: Pivot (No Model)
        Schema::create('contents_categories', function (Blueprint $table) {
            $table->integer('category_id')->unsigned()->index();
            $table->integer('content_id')->unsigned()->index();
            $table->index(
                ['category_id', 'content_id'],
                'contents_connection_ccid_cid_index'
            );
            $table->unique(
                ['category_id', 'content_id'],
                'contents_connection_ccid_cid_unique'
            );
            $table->foreign('category_id')
            ->references('id')->on('contents')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreign('content_id')
            ->references('id')->on('contents')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        if (getenv('DB_CONNECTION') == 'mysql') {
            \DB::statement('ALTER TABLE contents_translations ADD FULLTEXT search(title, short_content)');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contents_categories');
        Schema::dropIfExists('contents_translations');
        Schema::dropIfExists('contents');
    }
}
