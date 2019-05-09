<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\CashierUser;
use Carbon\Carbon;

class UsersController extends Controller
{



  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index() {
  	return view('settings.user_account.index');

  }

  public function create() {
    return view('settings.user_account.create');
  }

  public function changePassword(Request $request){
    $_token = $request->_token;
    $old_password = $request->old_password;
    $new_password = $request->new_password;
    $confirm_new_password = $request->confirm_new_password;
    $current_time = Carbon::now('Asia/Manila');
    // $user_id = $request->user_id;
    // $data = CashierUser::where('id', $user_id)->get();
    $user_id = Auth::user()->id;
    $password = Auth::user()->password;
    $is_saving = 'false';

    if (!(Hash::check($old_password, $password))) {
      $is_saving = 'false';

    } else {
      $is_saving = 'true';

    }

    //Change Password
    if ($is_saving == 'true') {
      CashierUser::where('id', $user_id)
      ->update(['password' => Hash::make($new_password),
        'updated_at' => $current_time->toDateTimeString()]);

      // $user = CashierUser::user();
      // $user->password = bcrypt($new_password);
      // $user->save();
    }

    $response = array(
      'data' => 'success',
      'is_saving' => $is_saving,
    );

    return response()->json($response);
  }


  /**
   * Get user data
   *
   * @return \Illuminate\Http\Response
   */
   public function getUserData() {
     $users = CashierUser::select('id', 'username', 'email', 'area', 'is_admin', 'is_active', 'created_at')
     ->distinct()
     ->get();

     $response = array('data' => $users);
     return response()->json($response);
   }


}
