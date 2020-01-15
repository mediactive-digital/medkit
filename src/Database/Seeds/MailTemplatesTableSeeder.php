<?php

namespace MediactiveDigital\MedKit\Database\Seeds;

use Illuminate\Database\Seeder;

use App\Models\MailTemplate;

use App\Mails\WelcomeMail;

use Carbon\Carbon;

class MailTemplatesTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		MailTemplate::truncate();

		$dateNow = Carbon::now();

		MailTemplate::insert([
			'mailable' => WelcomeMail::class,
			'subject' => 'Welcome, {{ name }}',
			'html_template' => '<h1>Hello, {{ name }}!</h1>',
			'text_template' => 'Hello, {{ name }}!',
			'created_at' => $dateNow,
			'updated_at' => $dateNow
		]);
	}
}
