<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlannersView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement( 'CREATE VIEW planners AS SELECT DISTINCT E.* FROM employees E JOIN groups G ON E.id = G.planner_id' );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement( 'DROP VIEW planners' );
    }
}
