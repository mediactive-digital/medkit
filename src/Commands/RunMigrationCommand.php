<?php

namespace MediactiveDigital\MedKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;

class RunMigrationCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medkit:migrate';
    protected $description = 'Execute la migration';

    /**
     * Lance les migrations avec seed
     */
    public function handle() {
        $this->refreshMigration();
        $this->info("Done");
    }

    private function refreshMigration(){
        $this->line('---------------------');
        $this->line('| Migrations & Seeds');
        $this->line('---------------------');

        // Verification de l'existance de la DB
        try {
            DB::statement("SELECT 1");
        } catch (\Exception $e) {

            $this->alert("Pas de bdd, on tente de la creer");
            $pdo = new \PDO(
                "mysql:host=".config('database.connections.mysql.host').";",
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password')
            );
            $pdo->exec("CREATE DATABASE IF NOT EXISTS ".config('database.connections.mysql.database').";");
            unset($pdo);
        }

        // On lance les migrations et seeds
        $this->runCommand("migrate:refresh",['--seed' => 'true'],$this->output);
    }

}
