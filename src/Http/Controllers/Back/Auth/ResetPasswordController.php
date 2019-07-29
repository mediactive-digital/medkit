<?php

namespace App\Http\Controllers\Back\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;

use Illuminate\Routing\Controller as BaseController;
use Kris\LaravelFormBuilder\FormBuilder;

class ResetPasswordController extends BaseController {
	/*
	  |--------------------------------------------------------------------------
	  | Password Reset Controller
	  |--------------------------------------------------------------------------
	  |
	  | This controller is responsible for handling password reset requests
	  | and uses a simple trait to include this behavior. You're free to
	  | explore this trait and override any methods you wish to tweak.
	  |
	 */

	use ResetsPasswords;

	/**
	 * Where to redirect users after resetting their password.
	 *
	 * @var string
	 */
	protected $redirectTo = '/gestion';

	protected function guard() {

		return Auth::guard('admin');
	}

	public function broker() {

		return Password::broker('admins');
	}

	/**
	 * Show mdp oublié Form
	 *
	 * @param FormBuilder $formBuilder
	 * @param Request $request
	 * @param type $token
	 */
	public function showResetForm( FormBuilder $formBuilder ){
 
		$form = $formBuilder->create('App\Forms\Back\ResetPasswordForm', [
            'method' => 'POST',
            'url' => route('back.password.email')
        ]);
        // return view( 'back.auth.passwords.reset' )->with(['form' => $form , 'token' => $token, 'email' => $request->email]);
       return view( 'users.back.auth.reinitialisation', compact('form'));

		//return view('back.auth.passwords.reset')->with(['token' => $token, 'email' => $request->email]);
	}

	/**
	 * Reset the given user's password.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
	 */
	public function reset(Request $request) {

		$this->validate($request, [
			'token'		 => 'required',
			'email'		 => 'required|max:120|email',
			'password'	 => 'required|min:6|max:120|confirmed'
			], $this->validationErrorMessages());

		// Here we will attempt to reset the user's password. If it is successful we
		// will update the password on an actual user model and persist it to the
		// database. Otherwise we will parse the error and return the response.
		$response = $this->broker()->reset(
			$this->credentials($request), function ($user, $password) {

			$this->resetPassword($user, $password);
		}
		);

		// If the password was successfully reset, we will redirect the user back to
		// the application's home authenticated view. If there is an error we can
		// redirect them back to where they came from with their error message.
		return $response == Password::PASSWORD_RESET ? $this->sendResetResponse($request, $response) : $this->sendResetFailedResponse($request, $response);
	}

	/**
	 * Get the response for a failed password reset.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $response
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
	 */
	protected function sendResetFailedResponse(Request $request, $response) {

		return redirect()->back()
				->withInput($request->only('token', 'email'))
				->withErrors(['email' => trans($response)]);
	}

}
