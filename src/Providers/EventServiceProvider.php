<?php

namespace MediactiveDigital\MedKit\Providers;

use App\Listeners\GdprInactiveUserDeletedListener;
use App\Listeners\GdprInactiveUserListener;
use App\Listeners\LogSuccessfulLoginListener;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Soved\Laravel\Gdpr\Events\GdprInactiveUser;
use Soved\Laravel\Gdpr\Events\GdprInactiveUserDeleted;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        GdprInactiveUser::class => [
            GdprInactiveUserListener::class,
        ],
        GdprInactiveUserDeleted::class => [
            GdprInactiveUserDeletedListener::class,
        ],
        Login::class => [
            LogSuccessfulLoginListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
