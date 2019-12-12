<?php

namespace MediactiveDigital\MedKit\Database\Seeds;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Carbon\Carbon;

use DB;
class PermissionsTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Permission::truncate();

		$dateNow = Carbon::now();

		DB::table('permissions')->insert([
			'id'		 => 1,
			'name'		 => 'permissions_create',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 2,
			'name'		 => 'permissions_edit_all',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 3,
			'name'		 => 'permissions_delete_any',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 4,
			'name'		 => 'permissions_view_all',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 5,
			'name'		 => 'roles_create',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 6,
			'name'		 => 'roles_edit_all',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 7,
			'name'		 => 'roles_delete_any',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 8,
			'name'		 => 'roles_view_all',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 9,
			'name'		 => 'users_create',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 10,
			'name'		 => 'users_edit_all',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 11,
			'name'		 => 'users_delete_any',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 12,
			'name'		 => 'users_view_all',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 13,
			'name'		 => 'users_edit_own',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 14,
			'name'		 => 'users_delete_own',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);

		DB::table('permissions')->insert([
			'id'		 => 15,
			'name'		 => 'users_view_own',
			'guard_name' => 'web',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);
	}

}