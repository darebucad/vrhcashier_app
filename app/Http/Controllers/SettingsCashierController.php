<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsCashierController extends Controller
{

    public function index(){
      return view('settings.cashier_management.index');
    }
}
