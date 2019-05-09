<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Setup;
use Carbon\Carbon;

class SettingsCashierController extends Controller
{

    public function index(){

      $cashier = Setup::select('sucode', 'or_prefix', 'cashier_officer', 'cashier_designation')->get();

      return view('settings.cashier_management.index', compact('cashier'));

    }

    public function saveCashier(Request $request){
      $sucode = $request->sucode;
      $or_number_prefix = $request->or_number_prefix;
      $cashier_officer = $request->cashier_officer;
      $cashier_designation = $request->cashier_designation;
      $current_time = Carbon::now('Asia/Manila');

      Setup::where('sucode', $sucode)->update([
          'or_prefix' => $or_number_prefix,
          'cashier_officer' => $cashier_officer,
          'cashier_designation' => $cashier_designation,
          'updated_at' => $current_time->toDateTimeString()
      ]);

      $data = array(
        'sucode' => $sucode,
        'or_prefix' => $or_number_prefix,
        'cashier_officer' => $cashier_officer,
        'cashier_designation' => $cashier_designation,
        'updated_at' => $current_time->toDateTimeString()
      );



      $response = array('data' => $data);

      return response()->json($response);

    }




}
