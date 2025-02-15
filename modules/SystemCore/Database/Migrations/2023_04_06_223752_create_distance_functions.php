<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistanceFunctions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // public function up()
    // {
    //     // EXAMPLE
    //     // SELECT DISTANCE(
    //     //     POINT(36.2197006,36.1466967), 
    //     //     coordinates
    //     // ) AS distance_in_meters FROM providers
    //     // HAVING distance_in_meters <= 2000;
    //     \DB::unprepared('
    //         CREATE FUNCTION DISTANCE ( point_x POINT, point_y POINT )
    //         RETURNS DOUBLE
    //         DETERMINISTIC
    //         BEGIN
    //             RETURN ST_Distance_Sphere(
    //                 point_x, point_y
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
    //     Schema::dropIfExists('');
    // }
}
