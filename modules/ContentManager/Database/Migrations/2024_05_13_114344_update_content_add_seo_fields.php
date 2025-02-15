<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContentAddSeoFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contents_translations', function (Blueprint $table) {
            $table->string('meta_alt_img', 191)->nullable();
            $table->string('meta_title', 191)->nullable();
            $table->string('meta_description', 5000)->nullable()->comment('Short content for preview and seo');
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
            $table->dropColumn('meta_alt_img');
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
        });
    }
}
