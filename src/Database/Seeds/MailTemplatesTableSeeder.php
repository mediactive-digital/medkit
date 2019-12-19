<?php

namespace MediactiveDigital\MedKit\Database\Seeds;

use Illuminate\Database\Seeder;
//use App\Models\Permission;   // Todo passer par un model et faire truncate dans le run
use Carbon\Carbon;

use DB;

class MailTemplatesTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		\App\Models\MailTemplate::truncate();

		$dateNow = Carbon::now();

		DB::table('mail_templates')->insert([
			'mailable' => \App\Mails\WelcomeMail::class,
			'subject' => 'Welcome, {{ name }}',
			'html_template' => '<h1>Hello, {{ name }}!</h1>',
			'text_template' => 'Hello, {{ name }}!',
			//'created_by' => 1,
			//'updated_by' => 1,
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);
 
	}

}
