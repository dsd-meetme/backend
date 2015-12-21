<?php

namespace plunner\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use plunner\Calendar;
use plunner\Employee;
use plunner\Policies\CalendarPolicy;
use plunner\Policies\EmployeePolicy;
use plunner\Group;
use plunner\Policies\GroupPolicy;
use plunner\Timeslot;
use plunner\Policies\TimeslotPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'plunner\Model' => 'plunner\Policies\ModelPolicy',
        Employee::class => EmployeePolicy::class,
        Group::class => GroupPolicy::class,
        Calendar::class => CalendarPolicy::class,
        Timeslot::class => TimeslotPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        //
    }
}
