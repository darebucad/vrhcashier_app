<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\PaymentOther;
use App\ViewPaymentOR;
use App\WalkinCharge;
use App\Discount;

class CollectionsWalkinController extends Controller
{

  /**
   * Prevent unauthorized access to the web pages .
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
   * Show the form for creating a new Walk-In payment.
   *
   * @param $id
   * @return \Illuminate\Http\Response
   */
  public function create($id)
  {
      $user_id = $id;
      $payments = ViewPaymentOR::select('next_or_number', 'or_prefix')->where('id', $user_id)->limit(1)->count();

      if ( $payments > 0 ) {
        $payments = ViewPaymentOR::select('next_or_number','or_prefix', 'created_at')
        ->where('id', $user_id)
        ->limit(1)
        ->orderBy('created_at', 'desc')
        ->get();

      }
      else {
        $payments = ViewPaymentOR::select('next_or_number', 'or_prefix', 'created_at')
        ->limit(1)
        ->orderBy('created_at', 'desc')
        ->get();

      }

      $discounts = Discount::all();

      return view('collections.walkin.create', compact('payments', 'discounts'));

  }

    /**
    * Search for Walk-In patient charges .
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function searchWalkinCharges(Request $request) {
      $charge_slip_number = $request->charge_slip_number;
      $user_id = $request->user_id;
      $walkin_charges = WalkinCharge::where('chargeslipno', $charge_slip_number)->get();
      $patient_name = WalkinCharge::select('patient')->where('chargeslipno', $charge_slip_number)->first();

      $response = array(
        'data' => $walkin_charges,
        'charge_slip_number' => $charge_slip_number,
        'user_id' => $user_id,
        'patient_name' => $patient_name
      );

      return response()->json($response);

    }

}
