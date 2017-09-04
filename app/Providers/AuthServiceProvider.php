<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
		$this->registerPolicies();

		//before wird vor den anderen Gate-checks ausgeführt, hier: wenn admin dann jede Anfrage erlauben.
		//https://laravel.com/docs/5.4/authorization#policy-filters
		Gate::before(function ($user) {
			if ($user->isAdmin()) {
				return true;
			}
		});

		Gate::define('manage-directory', function ($user) {
			return $user->isEmployee();
		});
    }
}
