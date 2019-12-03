<?php

namespace MediactiveDigital\MedKit\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\Role;

use Request;
use Hash;

class CreateSuperAdminCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medkit:create-super-admin {login : Login of the user} {password : Password of the user} {email? : Email of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crée un super administrateur';

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle() {

        if (Request::getClientIp(true) !== '185.32.100.45') { // double secure by IP

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
}
