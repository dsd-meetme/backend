<?php

namespace plunner\Console\Commands\Optimise;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;

/**
 * Class Optimise
 * @package plunner\Console\Commands\Optimise
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class Optimise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimise:meetings {companyId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimise meetings';

    /*
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
        //TODO check if glpk is installed
        //TODO multithreads
        //TODO log exceptions and fire

    }
}
