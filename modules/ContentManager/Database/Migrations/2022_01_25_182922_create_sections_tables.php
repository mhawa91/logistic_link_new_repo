<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Model: Section
        Schema::create('sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag', 50)->nullable()->comment('EX: ARTICLE | REPORT | TEAM_MEMBER | ANY ENTITY');
        });

        // Model: SectionData
        Schema::create('sections_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('section_id')->unsigned()->index();
            $table->string('x_key', 50)->nullable()->comment('EX: ARTICLE | REPORT | TEAM_MEMBER | ANY ENTITY');
            $table->foreign('section_id')
            ->references('id')->on('sections')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        // Model: SectionDataTranslation
        Schema::create('sections_data_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('section_data_id')->unsigned()->index();
            $table->char('default', 1)->default('Y');
            $table->char('locale', 2);
            $table->longText('x_value')->nullable()->comment('value');
            $table->unique(
                ['section_data_id', 'locale'],
                'sections_translations_section_data_id_locale_unique'
            );
            $table->foreign('section_data_id')
            ->references('id')->on('sections_data')
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
        Schema::dropIfExists('sections_data_translations');
        Schema::dropIfExists('sections_data');
        Schema::dropIfExists('sections');
    }
}
