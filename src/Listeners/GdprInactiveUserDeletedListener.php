<?php

namespace MediactiveDigital\MedKit\Listeners;

use Illuminate\Support\Facades\Log;
use Soved\Laravel\Gdpr\Events\GdprInactiveUserDeleted;

class GdprInactiveUserDeletedListener
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(GdprInactiveUserDeleted $event) {
        Log::debug("User innactif supprimé : ". $event->user->name);
    }
}
