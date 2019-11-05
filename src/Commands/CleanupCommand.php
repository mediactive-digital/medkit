<?php

namespace MediactiveDigital\MedKit\Commands;

use Soved\Laravel\Gdpr\Console\Commands\Cleanup as SovedCleanup;

use App\Models\User;

use Soved\Laravel\Gdpr\Jobs\Cleanup\CleanupJob;

class CleanupCommand extends SovedCleanup {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medkit:cleanup';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        
        $config = config('gdpr');
        $users = User::all();
        $strategy = app($config['cleanup']['strategy']);

        CleanupJob::dispatch($users, $strategy);

        $this->info('CleanupJob dispatched');
    }
}
