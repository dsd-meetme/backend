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
        //TODO insert a timeout
        //TODO try...catch with destruct
        $companyId = $this->argument('companyId');
        if (is_numeric($companyId))
            $this->makeForeground(Company::findOrFail($companyId));
        else
            $this->syncAll();
    }

    /**
     * optimise company foreground
     * @param Company $company
     */
    private function makeForeground(Company $company)
    {
        $this->info('Optimisation company ' . $company->id . ' started');
        try {
            (new Optimise($company, $this->schedule, $this->laravel))->optimise()->save();
            $this->info('Optimisation ' . $company->id . ' completed');
        }catch(OptimiseException $e) {
            if ($e->isEmpty()) {
                $mex = 'Company ' . $company->id . ' doesn\'t have sufficient data';
                $this->warn($mex);
                \Log::info($mex);
            } else {
                $mex = 'Error during optimisation of company ' . $company->id . ': ' . $e->getMessage();
                $this->error($mex);
                //TODO log cause, but don't send it to the user
                //\Log::error($mex); //already logged in listener
            }
        }
    }

    private function syncAll()
    {
        //TODO if are there no companies?
        $companies = Company::all();
        if ($this->option('background')) {
            \Log::debug(self::BACKGROUND_MOD_MEX);
            $this->info(self::BACKGROUND_MOD_MEX);
            foreach ($companies as $company)
                $this->makeBackground($company);
            \Log::debug(self::BACKGROUND_COMPLETED_MEX);
            $this->info(self::BACKGROUND_COMPLETED_MEX);
        } else
            foreach ($companies as $company)
                $this->makeForeground($company);
    }

    /**
     * optimise company via exec command
     * @param Company $company
     */
    private function makeBackground(Company $company)
    {
        $event = $this->schedule->command('optimise:meetings ' . $company->id)->withoutOverlapping();
        if ($event->isDue($this->laravel))
            $event->run($this->laravel);
    }
}
