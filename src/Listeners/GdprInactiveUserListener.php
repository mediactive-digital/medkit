<?php

namespace MediactiveDigital\MedKit\Listeners;

use Illuminate\Support\Facades\Log;
use Soved\Laravel\Gdpr\Events\GdprInactiveUser;

class GdprInactiveUserListener
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(GdprInactiveUser $event)  {
        Log::debug("User considéré comme innactif : ".$event->user->name);
    }
}
