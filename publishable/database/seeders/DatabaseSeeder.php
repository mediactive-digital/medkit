<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB;

class DatabaseSeeder extends Seeder {

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        $this->call([
            RolesTableSeeder::class,
            ModelHasRolesTableSeeder::class,
            PermissionsTableSeeder::class,
            RoleHasPermissionsTableSeeder::class,
            MailTemplatesTableSeeder::class
	    ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
