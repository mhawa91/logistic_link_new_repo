<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAddressessAddDeliveryConfirmationNotes extends Migration
{
    public function up()
    {
        Schema::table('lgl_addressess', function (Blueprint $table) {
            $table->text('delivery_confirmation_notes')->nullable()->after('delivered_at');
        });
    }

    public function down()
    {
        Schema::table('lgl_addressess', function (Blueprint $table) {
            $table->dropColumn('delivery_confirmation_notes');
        });
    }
}
