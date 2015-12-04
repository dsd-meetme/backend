<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCaldavsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('caldavs', function (Blueprint $table) {
            $table->integer('calendar_id')->unsigned()->primary();
            $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('cascade')->onUpdate('cascade');
            $table->string('url');
            $table->string('username');
            $table->text('password'); //since we encrypt password 255 is not enough
            $table->string('calendar_name');
            $table->string('sync_errors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('caldavs');
    }
}
