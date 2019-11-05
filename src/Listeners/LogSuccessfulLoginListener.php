<?php

namespace MediactiveDigital\MedKit\Listeners;

use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Soved\Laravel\Gdpr\Events\GdprInactiveUser;

class LogSuccessfulLoginListener
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)  {
        $user = $event->user;
        Log::debug("Utilisateur connecté : ".$user->id);

        try {
            $user->last_activity = new Carbon();
            $user->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
