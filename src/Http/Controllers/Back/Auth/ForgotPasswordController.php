<?php

namespace MediactiveDigital\MedKit\Http\Controllers\Back\Auth;

use MediactiveDigital\MedKit\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Routing\Controller as BaseController;

use Kris\LaravelFormBuilder\FormBuilder;

class ForgotPasswordController extends BaseController {
	/*
	  |--------------------------------------------------------------------------
	  | Password Reset Controller
	  |--------------------------------------------------------------------------
	  |
	  | This controller is responsible for handling password reset emails and
	  | includes a trait which assists in sending these notifications from
	  | your application to your users. Feel free to explore this trait.
	  |
	 */

	use SendsPasswordResetEmails;

	public function broker() {

		return Password::broker('admins');
	}

	public function showLinkRequestForm(FormBuilder $formBuilder) {

		$form = $formBuilder->create('App\Forms\Back\ResetPasswordForm', [
			'method' => 'POST',
			'url'	 => route('back.password.email')
		]);

		// return view('back.auth.passwords.email');
		return view('medKitTheme::users.back.auth.demande_reinitialisation', compact('form'));
	}

	/**
	 * Send a reset link to the given user.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
	 */
	public function sendResetLinkEmail(Request $request) {
		$this->validateEmail($request);

		// We will send the password reset link to this user. Once we have attempted
		// to send the link, we will examine the response then see the message we
		// need to show to the user. Finally, we'll send out a proper response.
		$response = $this->broker()->sendResetLink(
			$request->only('email')
		);

		return $response == Password::RESET_LINK_SENT ? $this->sendResetLinkResponse($request, $response) : $this->sendResetLinkFailedResponse($request, $response);
	}

}
