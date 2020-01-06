<?php

namespace MediactiveDigital\MedKit\Commands;

use Illuminate\Console\Command;

use Illuminate\Routing\Router;

use Illuminate\Filesystem\Filesystem;

use MediactiveDigital\MedKit\Helpers\BladeRouteGenerator;

class GenerateJsRoutesCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medkit:generate-js-routes {path=public/js/routes.js} {--group=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère le fichier JS qui permet d\'avoir accès aux routes';

    public function __construct(Router $router, Filesystem $files) {

        parent::__construct();

        $this->router = $router;
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $this->info('Generating JS routes');

        $path = $this->argument('path');

        // Le ou les groupes (si plusieurs groupes, séparés par des virgules)
        $group = $this->option('group');
        $group = $group ? explode(',', $group) : false;

        $script = (new BladeRouteGenerator($this->router))->generate($group);
        
        $this->makeDirectory($path);
        $this->files->put($path, $script);
    }

    protected function makeDirectory($path) {

        if (!$this->files->isDirectory(dirname($path))) {

            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }
}
