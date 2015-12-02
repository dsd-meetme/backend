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
            $table->dateTime('soonest_meeting_start_time');
            $table->dateTime('latest_meeting_start_time');
            $table->integer('repeat');
            $table->dateTime('repetition_end_time')->nullable();
            $table->boolean('is_scheduled');
            $table->dateTime('scheduled_start_time');
            $table->dateTime('scheduled_end_time');
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
