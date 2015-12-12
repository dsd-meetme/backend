<?php

namespace plunner\Console\Commands\Optimise;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use plunner\Company;

/**
 * Class OptimiseCommand
 * @package plunner\Console\Commands\Optimise
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class OptimiseCommand extends Command
{
    const BACKGROUND_MOD_MEX = 'background mode';
    const BACKGROUND_COMPLETED_MEX = 'All background tasks started';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimise:meetings {companyId?}  {--background}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimise meetings';

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
        //TODO check if glpk is installed
        //TODO multithreads
        //TODO log exceptions and fire
        //TODO insert a timeout

        //TODO try...catch with destruct
        $companyId = $this->argument('companyId');
        if(is_numeric($companyId))
            $this->makeForeground(Company::findOrFail($companyId));
        else
            $this->syncAll();
    }

    private function syncAll()
    {
        $calendars = Caldav::all();
        if($this->option('background')) {
            \Log::debug(self::BACKGROUND_MOD_MEX);
            $this->info(self::BACKGROUND_MOD_MEX);
            foreach ($calendars as $calendar)
                $this->makeBackground($calendar);
            \Log::debug(self::BACKGROUND_COMPLETED_MEX);
            $this->info(self::BACKGROUND_COMPLETED_MEX);
        }else
            foreach($calendars as $calendar)
                $this->makeForeground($calendar);
    }

    /**
     * optimise company via exec command
     * @param Company $company
     */
    private function makeBackground(Company $company)
    {
        $event = $this->schedule->command('optimise:meetings '.$company->id)->withoutOverlapping();
        if($event->isDue($this->laravel))
            $event->run($this->laravel);
    }

    /**
     * optimise company foreground
     * @param Company $company
     */
    private function makeForeground(Company $company)
    {
        $this->info('Optimisation company '. $company->id.' started');
        (new Optimise($company))->optmise();
        $this->info('Optimisation '. $company->id.' completed');
    }
}
