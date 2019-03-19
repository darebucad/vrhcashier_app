<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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





}
