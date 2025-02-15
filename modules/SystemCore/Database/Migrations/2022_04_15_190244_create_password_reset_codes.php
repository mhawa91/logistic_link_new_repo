<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswordResetCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Model: PasswordResetCode || Service: PasswordResetCodeService
        Schema::create('password_reset_codes', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->string('code', 30)->unique();
            $table->timestamp('valid_until');
            $table->primary(['user_id', 'code']);
            $table->index(['user_id', 'code', 'valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('password_reset_codes');
    }
}
