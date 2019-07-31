<?php
/**
 * Observer pour exemple d'application
 */


namespace App\Observers;

use App\Models\Admin;
use Illuminate\Support\Str;

class AdminObserver {

    /**
     * Handle to the admin "creating" event.
     *
     * @param \App\Models\Admin $admin
     * @return void
     */
    public function creating(Admin $admin) {

        $this->capitalizeName($admin);
    }

    /**
     * Handle the admin "updating" event.
     *
     * @param \App\Models\Admin $admin
     * @return void
     */
    public function updating(Admin $admin) {

        $this->capitalizeName($admin);
    }

    /**
     * Capitalize admin name and firstname
     *
     * @param \App\Models\Admin $admin
     * @return void
     */
    private function capitalizeName(Admin $admin) {

        $admin->name = Str::ucfirst($admin->name);
        $admin->firstname = Str::ucfirst($admin->firstname);
    }
}
