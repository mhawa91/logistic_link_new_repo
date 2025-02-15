<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLatLngFunctions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // public function up()
    // {
    //     \DB::unprepared('
    //         CREATE FUNCTION LAT_START (lat DECIMAL(16, 11), lng DECIMAL(16, 11), radius DECIMAL(16, 11))
    //         RETURNS DECIMAL(16, 11)
    //         DETERMINISTIC
    //         BEGIN
    //             RETURN DEGREES(
    //                 ASIN(
    //                     SIN(RADIANS(lat)) 
    //                     * COS(radius / 6373) 
    //                     + COS(RADIANS(lat)) 
    //                     * SIN(radius / 6373) 
    //                     * COS(RADIANS(180))
    //                 )
    //             );
    //         END
    //     ');

    //     \DB::unprepared('
    //         CREATE FUNCTION LAT_END (lat DECIMAL(16, 11), lng DECIMAL(16, 11), radius DECIMAL(16, 11))
    //         RETURNS DECIMAL(16, 11)
    //         DETERMINISTIC
    //         BEGIN
    //             RETURN DEGREES(
    //                 ASIN(
    //                     SIN(RADIANS(lat)) 
    //                     * COS(radius / 6373) 
    //                     + COS(RADIANS(lat)) 
    //                     * SIN(radius / 6373) 
    //                     * COS(RADIANS(0))
    //                 )
    //             );
    //         END
    //     ');

    //     \DB::unprepared('
    //         CREATE FUNCTION LNG_START (lat DECIMAL(16, 11), lng DECIMAL(16, 11), radius DECIMAL(16, 11))
    //         RETURNS DECIMAL(16, 11)
    //         DETERMINISTIC
    //         BEGIN
    //             RETURN DEGREES(
    //                 RADIANS(lng) 
    //                 + ATAN2(
    //                     SIN(RADIANS(270)) 
    //                     * SIN(radius / 6373) 
    //                     * COS(RADIANS(lat))
    //                     , COS(radius / 6373) 
    //                     - SIN(RADIANS(lat)) 
    //                     * SIN(RADIANS(lat))
    //                 )
    //             );
    //         END
    //     ');

    //     \DB::unprepared('
    //         CREATE FUNCTION LNG_END (lat DECIMAL(16, 11), lng DECIMAL(16, 11), radius DECIMAL(16, 11))
    //         RETURNS DECIMAL(16, 11)
    //         DETERMINISTIC
    //         BEGIN
    //             RETURN DEGREES(
    //                 RADIANS(lng) 
    //                 + ATAN2(
    //                     SIN(RADIANS(90)) 
    //                     * SIN(radius / 6373) 
    //                     * COS(RADIANS(lat))
    //                     , COS(radius / 6373) 
    //                     - SIN(RADIANS(lat)) 
    //                     * SIN(RADIANS(lat))
    //                 )
    //             );
    //         END
    //     ');
    // }

    // /**
    //  * Reverse the migrations.
    //  *
    //  * @return void
    //  */
    // public function down()
    // {
    // }
}
