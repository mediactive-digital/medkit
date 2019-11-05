<?php
namespace MediactiveDigital\MedKit\Database\Seeds;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Carbon\Carbon;

class RolesTableSeeder extends Seeder {

    public function run() {

        Role::truncate();

        $dateNow = Carbon::now();

        Role::insert([
            [
                'id' => 1,
                'name' => 'Super admin',
                'guard_name' => 'web',
                'created_at' => $dateNow,
                'updated_at' => $dateNow
            ],
            [
                'id' => 2,
                'name' => 'Admin',
                'guard_name' => 'web',
                'created_at' => $dateNow,
                'updated_at' => $dateNow
            ]
        ]);
    }
}
