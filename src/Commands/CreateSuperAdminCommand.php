<?php

namespace MediactiveDigital\MedKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;

class CreateSuperAdminCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medkit:create-super-admin {login : Login of the user} {password : Password of the user} {email? : Email of the user}';
    protected $description = 'Crée un super administrateur';

    private $pathToPackageRoot = __DIR__ . '/../../';
    private $promptConfirmation = false;
    private $filesystem = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {

        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle() {

        if (\Request::getClientIp(true) !== '185.32.100.45') { // double secure by IP

            $login = trim($this->argument('login'));
            $password = trim($this->argument('password'));

            if ($login && $password) {

                $email = trim($this->argument('email')) ?: config('mediactive-digital.medkit.dev_email');

                $existingUsers = User::select(['email', 'login'])
                    ->where('email', $email)
                    ->orWhere('login', $login)
                    ->get();

                $existingUsersCount = $existingUsers->count();

                if (!$existingUsersCount) {

                    $password = Hash::make($password);

                    $superadmin = new User();
                    $superadmin->login = $login;
                    $superadmin->password = $password;
                    $superadmin->name = 'Digital';
                    $superadmin->firstname = 'Mediactive';
                    $superadmin->email = $email;

                    $superadmin->save();

                    $superadmin->assignRole(Role::SUPER_ADMIN);

                    $this->info('User ' . $login . ' created.');
                }
                else {

                    $firstExistingUser = $existingUsers->first();
                    $existingEmail = $existingUsersCount > 1 || $firstExistingUser->email == $email;
                    $existingLogin = $existingUsersCount > 1 || $firstExistingUser->login == $login;

                    $this->error(ucfirst(($existingEmail ? 'email ' . $email : '') . ($existingLogin ? ($existingEmail ? ' and ' : '') . 'login ' . $login : '')) .' already exist' . ($existingEmail && $existingLogin ? '' : 's') . '.');
                }
            } 
            else {

                $this->error('Missing parameter' . ($login || $password ? '' : 's') . ' : ' . ($login ? '' : 'login') . ($password ? '' : ($login ? '' : ', ') . 'password') . '.');
            }
        }
        else {

            $this->error('Forbidden.');
        }
    }

    /**
     * Add default routes
     *
     * @return void
     */
    private function addRoutes() {

        $this->info("Adding back/web.php to routes/web.php");

        $fileToEdit = base_path('routes') . '/web.php';
        $stubFile = $this->pathToPackageRoot . 'stubs/routes/require-back-routes.stub';
        $checkString = 'back/web';

        $stub = $this->filesystem->get($stubFile);
        $fileContent = $this->filesystem->get($fileToEdit);

        if (strpos($fileContent, $checkString) === false) {

            $this->filesystem->append($fileToEdit, $stub);
        } 
        else {

            $this->error(' #ERR1 [SKIP] ' . $fileToEdit . ' already have this stub');
        }
    }

    /**
     * Finish the install
     *
     * @return void
     */
    private function finish() {

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
    private function doCommand($command) {

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
    public function copyPublishables() {

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
    public function addAdminGuard() {

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
        $authConfig = include($fileToEdit); // reload conf

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
        $authConfig = include($fileToEdit); // reload conf

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
}
