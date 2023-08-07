<?php

namespace MediactiveDigital\MedKit\Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Permission;

use Carbon\Carbon;

class PermissionsTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		Permission::truncate();

		$dateNow = Carbon::now();

		Permission::insert([
			[
				'id' => 1,
				'name' => 'permissions_create',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 2,
				'name' => 'permissions_edit_all',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 3,
				'name' => 'permissions_delete_any',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 4,
				'name' => 'permissions_view_all',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 5,
				'name' => 'roles_create',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 6,
				'name' => 'roles_edit_all',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 7,
				'name' => 'roles_delete_any',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 8,
				'name' => 'roles_view_all',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 9,
				'name' => 'users_create',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 10,
				'name' => 'users_edit_all',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 11,
				'name' => 'users_delete_any',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 12,
				'name' => 'users_view_all',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 13,
				'name' => 'users_edit_own',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 14,
				'name' => 'users_delete_own',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 15,
				'name' => 'users_view_own',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 16,
				'name' => 'role-has-permissions_edit_all',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 17,
				'name' => 'role-has-permissions_view_all',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 18,
				'name' => 'mail-templates_create',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 19,
				'name' => 'mail-templates_edit_all',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 20,
				'name' => 'mail-templates_delete_any',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 21,
				'name' => 'mail-templates_view_all',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 22,
				'name' => 'mail-templates_edit_own',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 23,
				'name' => 'mail-templates_delete_own',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 24,
				'name' => 'mail-templates_view_own',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 25,
				'name' => 'histories_view_all',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			],
			[
				'id' => 26,
				'name' => 'dev-tools_view',
				'guard_name' => 'web',
				'created_at' => $dateNow,
				'updated_at' => $dateNow
			]
		]);
	}
}
