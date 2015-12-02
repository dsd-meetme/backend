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
            $table->string('description');

            /*
            'start_time' and 'end_time' can mean different things depending on 'is_scheduled'
            if the meeting is not yet scheduled, this are the earliest and latest meeting times
            and if the meeting is scheduled this are the start and end of the meeting
            */
            $table->dateTime('start_time');
            $table->dateTime('end_time');

            $table->integer('repeat');
            $table->dateTime('repetition_end_time')->nullable();
            $table->boolean('is_scheduled');
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
