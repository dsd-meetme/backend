<?php

namespace plunner\Console\Commands\Optimise;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;

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
        $event = $this->schedule->exec('glpsol --math '.__DIR__.'/model.mod')->withoutOverlapping()->sendOutputTo(__DIR__.'/out.txt');
        if($event->isDue($this->laravel))
            $event->run($this->laravel);
    }
}
