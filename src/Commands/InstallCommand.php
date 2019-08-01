<?php

namespace MediactiveDigital\MedKit\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Illuminate\Filesystem\Filesystem;
use MediactiveDigital\MedKit\Helpers\FormatHelper as FormatHelper;
use MediactiveDigital\MedKit\Helpers\ConfigHelper as ConfigHelper;

class InstallCommand extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medkit:install {--theme=light}';
    protected $description = 'Installation du starterkit';

    private $pathToPackageRoot = __DIR__ . '/../../';
    private $promptConfirmation = false;
    private $filesystem = null;



    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @return mixed
     */
    public function handle(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        if (!$this->promptConfirmation || $this->confirm("Confirm installation ?")) {



            $this->addRequirePackages();

            $this->line('---------------------');
            $this->line('| Medkit installation ');
            $this->line('---------------------');

            $this->copyPublishables();
            $this->addRoutes();
            $this->addAdminGuard();
            $this->addFacades();




            /**
             * Finish the install : dump autoload
             */
            $this->line('---------------------');
            $this->line('| Last step... ');
            $this->line('---------------------');
            $this->finish();
        }


        //$this->table(array('test'), array( array( "Starterkit installation..") ) );
    }








    private function addRequirePackages()
    {
        $devPackages = [
            "orangehill/iseed",
            "barryvdh/laravel-debugbar",
            "barryvdh/laravel-ide-helper",
            "laravel/dusk",
            "noeldemartin/laravel-dusk-mocking",
            "reliese/laravel",
            "xethron/migrations-generator"
        ];

        $this->doCommand("composer require " . implode(' ', $packages) . " --dev");


        if ($this->arguments('--theme') ) {

            $requirePackages = [
                "mediactive-digital/medkit-theme:^".$this->arguments('--theme')
            ];

            $this->doCommand("composer require " . implode(' ', $packages));
        }
    }







    /**
     * Add default routes
     *
     * @return void
     */
    private function addRoutes()
    {
        $this->info("Adding back/web.php to routes/web.php");

        $fileToEdit = base_path('routes') . '/web.php';
        $stubFile = $this->pathToPackageRoot . 'stubs/routes/require-back-routes.stub';
        $checkString = 'back/web';


        $stub = $this->filesystem->get($stubFile);
        $fileContent = $this->filesystem->get($fileToEdit);

        if (strpos($fileContent, $checkString) === false) {
            $this->filesystem->append($fileToEdit, $stub);
        } else {
            $this->error(' #ERR1 [SKIP] ' . $fileToEdit . ' already have this stub');
        }
    }


    /**
     * Finish the install
     *
     * @return void
     */
    private function finish()
    {
        $this->line('---------------------');
        $this->line('| Publish vendor files');
        $this->line('---------------------');
        $this->doCommand("php artisan vendor:publish --force --no-interaction");

        $this->line('---------------------');
        $this->line('| Migrations & Seeds');
        $this->line('---------------------');
        $this->doCommand("php artisan migrate:refresh --seed");

        if ($this->arguments('--theme')) {
            $this->line('---------------------');
            $this->line('| Theme ' . $this->arguments('--theme'));
            $this->line('---------------------');
            $this->doCommand("php artisan medkit-theme:install --force");
        }

        $this->line('---------------------');
        $this->info('composer dump-autoload');
        $this->doCommand('composer dump-autoload');
        $this->info('Installation done.');
    }






    /**
     * Execute a command
     *
     * @param [type] $command
     * @return void
     */
    private function doCommand($command)
    {
        $process = new Process($command);
        $process->setTimeout(null); // Setting timeout to null to prevent installation from stopping at a certain point in time

        $process->setWorkingDirectory(base_path())->run(function ($type, $buffer) {
            $this->line($buffer);
        });
    }


    /**
     * Copy required files for starterkit
     *
     * @return void
     */
    public function copyPublishables()
    {

        $src = $this->pathToPackageRoot . 'publishable';
        $this->info('Copying publishable files...');
        if (!file_exists($src)) {
            $this->error(' #ERR2 [SKIP] ' . $src . ' does not exists');
        }
        $res = $this->filesystem->copyDirectory($src, base_path());


        $this->info('Update app/Kernel.php');
    }


    /**
     * Edit config/auth.php to add admin guard
     *
     * @return void
     */
    public function addAdminGuard()
    {



        $this->info("Adding guard to config/auth.php");

        $fileToEdit = base_path('config') . '/auth.php';
        $authConfig = include($fileToEdit);


        /**
         * Add guard
         */
        $authConfigGuards = ['guards' => array_merge($authConfig['guards'], [
            'admin' => [
                'driver' => 'session',
                'provider' => 'admins'
            ],
            'admin-api' => [
                'driver' => 'token',
                'provider' => 'admins',
            ],
        ])];
        $sectionTitle = "Authentication Guards";
        $nextSectionTitle = "User Providers";

        ConfigHelper::replaceArrayInConfig($fileToEdit, $sectionTitle, $nextSectionTitle, $authConfigGuards);

        /**
         * Add Provider
         */
        $authConfig = include($fileToEdit); //reload conf
        $authConfigProvider = ['providers' => array_merge($authConfig['providers'], [
            'admins' => [
                'driver' => 'eloquent',
                'model' => \App\Models\Admin::class,
            ],
        ])];
        $sectionTitle = "User Providers";
        $nextSectionTitle = "Resetting Passwords";

        ConfigHelper::replaceArrayInConfig($fileToEdit, $sectionTitle, $nextSectionTitle, $authConfigProvider);


        /**
         * Add password broker
         */
        $authConfig = include($fileToEdit);   //reload conf
        $authConfigPasswordBroker = ['passwords' => array_merge($authConfig['passwords'], [
            'admins' => [
                'provider' => 'admins',
                'table' => 'password_resets',
                'expire' => 1440,
                'broker' => App\Passwords\Back\PasswordBroker::class,
            ],
        ])];
        $sectionTitle = "Resetting Passwords";
        ConfigHelper::replaceArrayInConfig($fileToEdit, $sectionTitle, null, $authConfigPasswordBroker);
    }



    public function addFacades()
    {
        $this->info("Add Facades to config/app.php");
        $fileToEdit = base_path('config') . '/app.php';
        $appConfig = include($fileToEdit);

        /**
         * Add facades
         */
        $appConfigFacades = ['aliases' => array_merge($appConfig['aliases'], [
            'Debugbar' => \Barryvdh\Debugbar\Facade::class,
            'Translation' => \App\Helpers\TranslationHelper::class,
        ])];
        $sectionTitle = "Class Aliases";
        ConfigHelper::replaceArrayInConfig($fileToEdit, $sectionTitle, null, $appConfigFacades);
    }
}
