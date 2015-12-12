<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('description'); //TODO improvement change with text? (remember to change request)
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('start_time')->unsigned()->nullabe()->default(NULL);
            $table->foreign('start_time')->references('id')->on('meeting_timeslots')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('duration'); //minutes
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
        Schema::drop('meetings');
    }
}
