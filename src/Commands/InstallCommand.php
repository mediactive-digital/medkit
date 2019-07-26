<?php

namespace MediactiveDigital\MedKit\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Illuminate\Filesystem\Filesystem;
use MediactiveDigital\MedKit\MedKit;

class InstallCommand extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medkit:install';
    protected $description = 'Installation du starterkit';

    private $pathToPackageRoot = __DIR__.'/../../';
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

            /**
             * Put publishable files to app
             */
            $this->copyPublishables();

            /**
             * Add routes
             */
            $this->addRoutes();




            /**
             * Finish the install : dump autoload
             */
            //$this->finish();
        }


        //$this->table(array('test'), array( array( "Starterkit installation..") ) );
    }











    /**
     * Add default routes
     *
     * @return void
     */
    private function addRoutes()
    { 
        $this->info( "Adding back/web.php to routes/web.php");

        $fileToEdit = base_path('routes').'/web.php';
        $stubFile = $this->pathToPackageRoot.'stubs/routes/require-back-routes.stub';
        $checkString = 'back/web';


        $stub = $this->filesystem->get( $stubFile );
        $fileContent = $this->filesystem->get( $fileToEdit );
        
        if( strpos( $fileContent, $checkString ) === false ){
            $this->filesystem->append( $fileToEdit, $stub );
        }else{
            $this->error( ' #ERR1 [SKIP] '. $fileToEdit.' already have this stub');
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

        $src = $this->pathToPackageRoot.'publishable';
        $this->info('Copying publishable files...' );
        if( !file_exists( $src ) ){
            $this->error( $src.' does not exists');
        }
        $res = $this->filesystem->copyDirectory( $src, base_path() );

    }
}
