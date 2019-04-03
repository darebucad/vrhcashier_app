<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;
use App\ViewPaymentOR;
use App\ViewPatientBill;



class CollectionsInpatientController extends Controller
{

    public function index() {

    	return view('collections.inpatient.index');
    }

    public function create(Request $request) {
      $user_id = $request->user_id;
      $payment_count =  ViewPaymentOR::select('orno')->where('id', $user_id)->count();

      if ($payment_count > 0) {
        $payments = ViewPaymentOR::select('next_or_number', 'or_prefix', 'created_at')
          ->where('id', $user_id)
          ->limit(1)
          ->orderBy('created_at', 'desc')
          ->get();

      } else {
        $payments = ViewPaymentOR::select('next_or_number', 'or_prefix', 'created_at')
          ->limit(1)
          ->orderBy('created_at', 'desc')
          ->get();

      }


      return view('collections.inpatient.create', compact('payments'));
    }

    public function getPatientBill() {


    }


    /**
     * Searching of patient bill records .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function autoCompleteSearch(Request $request) {
      $search = $request->term;
      $patient_bill = ViewPatientBill::where('patient_name', 'LIKE', '%'. $search .'%')
      ->limit(20)
      ->get();

      $data = [];
      foreach ($patient_bill as $key => $value) {
        $data[] = [
          'id' => $value->billing_id,
          'value' => $value->patient_name
        ];
      }

      return response($data);
    }

}
