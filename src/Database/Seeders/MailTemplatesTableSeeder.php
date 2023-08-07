<?php

namespace MediactiveDigital\MedKit\Database\Seeders;

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
			'subject' => '{"fr": "Bienvenue, {{ name }}", "en": "Welcome, {{ name }}"}',
			'html_template' => '{"fr": "<h1>Bonjour, {{ name }}</h1>", "en": "<h1>Hello, {{ name }}</h1>"}',
			'text_template' => '{"fr": "Bonjour, {{ name }}", "en": "Hello, {{ name }}"}',
			'created_at' => $dateNow,
			'updated_at' => $dateNow,
			'created_by' => 1,
			'updated_by' => 1
		]);
	}
}
