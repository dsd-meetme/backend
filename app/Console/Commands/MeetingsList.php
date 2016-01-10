<?php

namespace plunner\Console\Commands;

use Illuminate\Console\Command;
use plunner\Company;

class MeetingsList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meetings:list {companyId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List of all meetings of a company that must be taken';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $companyId = $this->argument('companyId');
        if (is_numeric($companyId))
            print_r(Company::with(self::withFilter())->findOrFail($companyId)->toArray());
        else
            print_r(Company::with(self::withFilter())->select('id')->get()->toArray());
    }

    private static function withFilter()
    {
        return ['groups' => function ($query) {
            $query->select('id', 'company_id');
        }, 'groups.meetings' => function ($query) {
            $query->select('id', 'group_id', 'start_time');
        }, 'groups.meetings.employees' => function ($query) {
            $query->select('id');
        }];
    }
}
