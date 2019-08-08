<?php

namespace MediactiveDigital\MedKit\Http\Controllers\Back\Auth;

use MediactiveDigital\MedKit\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller as BaseController;
use Kris\LaravelFormBuilder\FormBuilder;

class LoginController extends BaseController {
	/*
	  |--------------------------------------------------------------------------
	  | Login Controller
	  |--------------------------------------------------------------------------
	  |
	  | This controller handles authenticating users for the application and
	  | redirecting them to your home screen. The controller uses a trait
	  | to conveniently provide its functionality to your applications.
	  |
	 */

	use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = '/gestion';

	protected function guard() {

		return Auth::guard('admin');
	}

	public function username() {

		return 'login';
	}

	/**
	 * Show login Form
	 * @param FormBuilder $formBuilder
	 * @return type
	 */
	public function showLoginForm(FormBuilder $formBuilder) {
		$form = $formBuilder->create('App\Forms\Back\LoginForm', [
			'method' => 'POST',
			'url'	 => route('back.login')
		]);

		return view('medKitTheme::users.back.auth.login', compact('form'));
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
	 *
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function login(Request $request) {

		$this->validateLogin($request);

		// If the class is using the ThrottlesLogins trait, we can automatically throttle
		// the login attempts for this application. We'll key this by the username and
		// the IP address of the client making these requests into this application.
		if ($this->hasTooManyLoginAttempts($request)) {
			$this->fireLockoutEvent($request);

			return $this->sendLockoutResponse($request);
		}

		$user = Admin::role(['Super admin', 'Admin'])->where($this->username(), $request[$this->username()])->first();

		if ($user && Hash::check($request['password'], $user->password)) {

			$this->guard()->login($user, $request->filled('remember'));
			return $this->sendLoginResponse($request);
		}

		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		$this->incrementLoginAttempts($request);

		return $this->sendFailedLoginResponse($request);
	}

	public function logout() {

		Auth::logout();

		return redirect()->route('back.login');
	}

}
