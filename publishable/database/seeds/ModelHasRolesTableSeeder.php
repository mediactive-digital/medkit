<?php
use \MediactiveDigital\MedKit\Database\Migrations\ModelHasRolesTableSeeder as MedKitModelHasRolesTableSeeder;
class ModelHasRolesTableSeeder extends MedKitModelHasRolesTableSeeder {

}




namespace MediactiveDigital\MedKit\Database\Seeds;

use Illuminate\Database\Seeder;
use App\Models\ModelHasRole;
  
class ModelHasRolesTableSeeder extends Seeder {
  
    public function run() {

        ModelHasRole::truncate();
  
        ModelHasRole::insert([
            'role_id' => 1,
            'model_type' => 'App\Models\Admin',
            'model_id' => 1
        ]);
    }
}