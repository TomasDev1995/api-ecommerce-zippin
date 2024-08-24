<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Policies\User\AdminPolicy;
use App\Policies\User\CustomerPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::policy('admin', AdminPolicy::class);
        Gate::policy('customer', CustomerPolicy::class);
    }
}
