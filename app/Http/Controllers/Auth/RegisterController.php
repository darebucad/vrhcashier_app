<?php

namespace App\Http\Controllers\Auth;

use App\CashierUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/settings/user_account';

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('guest');
    // }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
      // 'email' => 'required|string|email|max:255|unique:cashier_users',
      return Validator::make($data, [
        'name' => 'required|string|max:255',
        'username' => 'required|string|unique:cashier_users',
        'password' => 'required|string|min:5|confirmed',
        'area' => 'required|string',
        'is_active' => 'required|string',
        'is_admin' => 'string',
      ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\CashierUser
     */
    protected function create(array $data)
    {
      // 'email' => $data['email'],
        return CashierUser::create([
          'name' => $data['name'],
          'username' => $data['username'],
          'password' => Hash::make($data['password']),
          'area' => $data['area'],
          'is_active' => $data['is_active'],
          'is_admin' => $data['is_admin'],
        ]);
    }
}
