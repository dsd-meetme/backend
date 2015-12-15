<?php

namespace plunner\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use plunner\Caldav;

/**
 * Class SyncCaldav
 * @package plunner\Console\Commands\SyncCaldav
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class timeslots extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timeslots:timeslots {employeeId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'timeslots';


    /**
     * @var Schedule laravel schedule object needed to perform command in background
     */
    private $schedule;


    /**
     * Create a new command instance.
     * @param Schedule $schedule
     *
     */
    public function __construct(Schedule $schedule)
    {
        parent::__construct();
        $this->schedule = $schedule;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $employeeId = $this->argument('employeeId');
        if(is_numeric($employeeId))
            print_r (\plunner\Employee::with('calendars.timeslots')->findOrFail($employeeId)->toArray());
    }


}
