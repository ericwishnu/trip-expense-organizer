<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\Trip;
use App\Models\TripDay;
use App\Policies\ExpensePolicy;
use App\Policies\TripDayPolicy;
use App\Policies\TripPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Trip::class => TripPolicy::class,
        TripDay::class => TripDayPolicy::class,
        Expense::class => ExpensePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
