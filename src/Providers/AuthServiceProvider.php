<?php

namespace MediactiveDigital\MedKit\Providers;


use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Services\Auth\SessionGuard;
use App\Services\Auth\TokenGuard;
use Illuminate\Support\Facades\Auth;
use MediactiveDigital\MedKit\Models\User;


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
    public function boot() {

        $this->registerPolicies();

        Gate::define('admin', function (\App\Models\User $user) {
            return $user->hasRole([Role::SUPER_ADMIN, Role::ADMIN]);
        });

        Auth::extend('session', function($app, $name, array $config) {

            $guard = new SessionGuard($name, Auth::createUserProvider($config['provider'] ?? null), $app['session.store']);

            if (method_exists($guard, 'setCookieJar')) {

                $guard->setCookieJar($app['cookie']);
            }

            if (method_exists($guard, 'setDispatcher')) {

                $guard->setDispatcher($app['events']);
            }

            if (method_exists($guard, 'setRequest')) {

                $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
            }

            return $guard;
        });

        Auth::extend('token', function($app, $name, array $config) {

            $guard = new TokenGuard(Auth::createUserProvider($config['provider'] ?? null), $app['request']);

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }
}
