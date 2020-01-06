<?php

namespace MediactiveDigital\MedKit\Jobs\Cleanup\Strategies;

use Soved\Laravel\Gdpr\Jobs\Cleanup\Strategies\DefaultStrategy as SovedDefaultStrategy;

use App\Models\Role;
use App\Models\User;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Auth\Authenticatable;

use Soved\Laravel\Gdpr\Events\GdprInactiveUser;
use Soved\Laravel\Gdpr\Jobs\Cleanup\CleanupStrategy;
use Soved\Laravel\Gdpr\Events\GdprInactiveUserDeleted;

use DB;
use Hash;

class DefaultStrategy extends SovedDefaultStrategy {

    /**
     * Execute cleanup strategy.
     *
     * @param \Illuminate\Database\Eloquent\Collection $users
     * @return void
     */
    public function execute(Collection $users) {

        $config = $this->config->get('gdpr.cleanup.defaultStrategy');

        // Users are considered inactive if their last activity is older than this timestamp
        $inactivity = Carbon::now()->subMonths($config['keepInactiveUsersForMonths']);

        $this->notifyInactiveUsers($inactivity, $config['notifyUsersDaysBeforeDeletion'], $users);
        $this->deleteInactiveUsers($inactivity, $users);
    }

    /**
     * Notify inactive users about their deletion.
     *
     * @param \Carbon\Carbon $inactivity
     * @param int $notificationThreshold
     * @param \Illuminate\Database\Eloquent\Collection $users
     * @return void
     */
    private function notifyInactiveUsers(Carbon $inactivity, int $notificationThreshold, Collection $users) {

        $users->filter(function(Authenticatable $user) use ($inactivity, $notificationThreshold) {

                return $user->last_activity->diffInDays($inactivity) === $notificationThreshold && !$this->isRoot($user);

            })->each(function (Authenticatable $user) {

                event(new GdprInactiveUser($user));
            });
    }

    /**
     * Delete inactive users.
     *
     * @param \Carbon\Carbon $inactivity
     * @param \Illuminate\Database\Eloquent\Collection $users
     * @return void
     */
    private function deleteInactiveUsers(Carbon $inactivity, Collection $users) {

        $users->filter(function(Authenticatable $user) use ($inactivity) {

                return $user->last_activity < $inactivity && !$this->isRoot($user);

            })->each(function (Authenticatable $user) {

                $table = (new User)->getTable();
                $now = Carbon::now();

                $datas = [
                    'updated_at' => $now,
                    'deleted_at' => $now,
                    'updated_by' => 1,
                    'deleted_by' => 1
                ];

                foreach (config('mediactive-digital.medkit.gdpr.' . $table) as $field) {

                    $datas[$field] = Hash::needsRehash($user->$field) ? Hash::make($user->$field) : $user->$field;
                }

                DB::table($table)->where('id', $user->id)->update($datas);

                event(new GdprInactiveUserDeleted($user));
            });
    }

    /**
     * Checks if user is deletable.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return bool
     */
    public function isRoot(Authenticatable $user) {

        return $user->hasRole(Role::SUPER_ADMIN) ? $user->email == config('mediactive-digital.medkit.dev_email') : false;
    }
}
