<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'name.unique' => 'This username has been taken already.',
            'email.unique' => 'This e-mail has been used already.',
            'email.valid_email' => 'Enter a valid e-mail address.',
            'email.email' => 'Enter a valid e-mail address.',
            'g-recaptcha-response.required' => 'Solve the captcha.',
            'g-recaptcha-response.recaptcha' => 'Solve the captcha.',
        ];

        return Validator::make($data, [
            'name' => 'required|min:4|max:18|alpha_num|unique:users',
            'email' => 'required|email|valid_email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'g-recaptcha-response' => 'required|recaptcha',
        ], $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        \Session::flash('flash_message', 'Successfully registered.');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'register_ip' => \Illuminate\Support\Facades\Request::ip(),
            'last_ip' => \Illuminate\Support\Facades\Request::ip(),
        ]);

        return $user;
    }
}
