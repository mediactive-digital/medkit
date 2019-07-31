<?php
namespace MediactiveDigital\MedKit\Database\Seeds;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
  
class UsersTableSeeder extends Seeder {
  
    public function run() {

        Admin::truncate();

        $dateNow = Carbon::now();
  
        Admin::insert([
            'id' => 1,
            'name' => 'Digital',
            'firstname' => 'Mediactive',
            'email' => 'dev@mediactive.fr',
            'login' => 'super-admin',
            'password' => Hash::make('y=M3^SzPF2'),
            'created_at' => $dateNow,
            'updated_at' => $dateNow,
            'created_by' => 1,
            'updated_by' => 1
        ]);
    }
}