<?php

namespace MediactiveDigital\MedKit\Commands;

use Illuminate\Console\Command;

use Log;

class ClearDirectoryCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medkit:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoyage recurcifs des dossiers de logs';

    /**
     * @var array Dossier => nombre de jour a garder
     */
    private $dirToDelete = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {

        parent::__construct();
        $this->dirToDelete = config('mediactive-digital.medkit.clear_directory');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $basePath = base_path();
        
        foreach ($this->dirToDelete as $dir => $time) {

            // Suppression recursive des fichiers n'ayant pas été modifié depuis plus de $time jours
            $cmd = "find {$basePath}/{$dir} -type f -daystart -mtime +{$time} -exec rm -rf {} \;";
            Log::debug("Lancement de $cmd...");
            exec($cmd);

            // Suppression recursive des dossiers n'ayant pas été modifié depuis plus de $time jours
            $cmd = "find {$basePath}/{$dir} -type f -daystart -mtime +{$time} -exec rm -rf {} \;";
            exec($cmd);
        }
    }
}
