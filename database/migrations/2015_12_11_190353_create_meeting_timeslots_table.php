<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingTimeslotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('meeting_timeslots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('time_start');
            $table->dateTime('time_end');
            $table->integer('meeting_id')->unsigned();
            $table->foreign('meeting_id')->references('id')->on('meetings')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
        Schema::table('meetings', function ($table) {
           $table->foreign('start_time')->references('id')->on('meeting_timeslots')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::table('meetings', function ($table) {
            $table->dropForeign('meetings_start_time_foreign');
        });
        Schema::drop('meeting_timeslots');
    }
}
