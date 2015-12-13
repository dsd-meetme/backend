<?php

namespace plunner\Http\Controllers\Employees\Calendars;

use Illuminate\Http\Request;
use plunner\Calendar;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests\Employees\Calendar\TimeslotRequest;
use plunner\Timeslot;


class TimeslotsController extends Controller
{
    /**
     * @var \plunner\Company
     */
    private $user;

    /**
     * ExampleController constructor.
     */
    public function __construct()
    {
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);
        $this->middleware('jwt.authandrefresh:mode-en');
    }

    //TODO check that the calendar is not a caldav calendar

    /**
     * Display a listing of the resource.
     *
     * @param  int  $calendarId
     * @return \Illuminate\Http\Response
     */
    public function index($calendarId)
    {
        //TODO improvement return small period
        /**
         * @var $employee Employee
         */
        $calendar = Calendar::findOrFail($calendarId);
        $this->authorize($calendar);
        return $calendar->timeslots;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TimeslotRequest  $request
     * @param  int  $calendarId
     * @return \Illuminate\Http\Response
     */
    public function store(TimeslotRequest $request, $calendarId)
    {
        //TODO check that end is after start and start is after now
        //TODO CHECK
        $calendar = Calendar::findOrFail($calendarId);
        $this->authorize($calendar);
        $input = $request->all();
        $timeslot = $calendar->timeslots()->create($input);
        if( $timeslot->time_start > $timeslot->time_end)
        return $timeslot;
        //TODO else
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $calendarId
     * @param  int  $timeslotId
     * @return \Illuminate\Http\Response
     */
    public function show($calendarId, $timeslotId)
    {
        //
        $calendar = Calendar::findOrFail($calendarId);
        $this->authorize($calendar);
        $timeslot = Timeslot::findOrFail($timeslotId);
        $this->authorize($timeslot);
        return $timeslot;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TimeslotRequest  $request
     * @param  int  $calendarId
     * @param  int  $timeslotId
     * @return \Illuminate\Http\Response
     */
    public function update(TimeslotRequest $request, $calendarId, $timeslotId)
    {
        //
        $calendar = Calendar::findOrFail($calendarId);
        $this->authorize($calendar);
        $timeslot = Timeslot::findOrFail($timeslotId);
        $this->authorize($timeslot);
        $input = $request->all();
        $timeslot->update($input);
        return $timeslot;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $calendarId
     * @param  int  $timeslotId
     * @return \Illuminate\Http\Response
     */
    public function destroy($calendarId, $timeslotId)
    {
        //
        $calendar = Calendar::findOrFail($calendarId);
        $this->authorize($calendar);
        $timeslot = Timeslot::findOrFail($timeslotId);
        $this->authorize($timeslot);
        $timeslot->delete();
        return $timeslot;
    }
}
