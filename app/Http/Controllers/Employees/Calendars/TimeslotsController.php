<?php

namespace plunner\Http\Controllers\Employees\Calendars;

use Illuminate\Http\Request;
use plunner\Calendar;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests\Employees\CalendarRequest;


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


    /**
     * Display a listing of the resource.
     *
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
     * @param  CalendarRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CalendarRequest $request)
    {
        //TODO check that end is after start and start is after now
        $employee = \Auth::user();
        $input = $request->all();
        $calendar = $employee->calendars()->create($input);
        return $calendar;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($calendarId, $timeslotId)
    {
        //
        $calendar = Calendar::findOrFail($id);
        $this->authorize($calendar);
        return $calendar;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CalendarRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CalendarRequest $request, $calendarId, $timeslotId)
    {
        //
        $calendar = Calendar::findOrFail($id);
        $this->authorize($calendar);
        $input = $request->all();
        $calendar->update($input);
        return $calendar;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($calendarId, $timeslotId)
    {
        //
        $calendar = Calendar::findOrFail($id);
        $this->authorize($calendar);
        $calendar->delete();
        return $calendar;
    }
}
