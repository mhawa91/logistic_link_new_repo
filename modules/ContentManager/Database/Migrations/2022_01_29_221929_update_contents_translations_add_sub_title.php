<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContentsTranslationsAddSubTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Model: ContentTranslation
        Schema::table('contents_translations', function (Blueprint $table) {
            $table->string('sub_title', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contents_translations', function (Blueprint $table) {
            $table->dropColumn([
                'sub_title'
            ]);
        });
    }
}
