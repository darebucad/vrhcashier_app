<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollectionsWalkinController extends Controller
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

  /**
   * Display a listing of the walkin collection payments.
   *
   */
  public function index() {

    return view('collections.walkin.index');
  }


  /**
   * Create walk-in payment page.
   *
   */
  public function create(){

    return view('collections.walkin.create');

  }



}
