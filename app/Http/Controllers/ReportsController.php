<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ViewMasterCollection;
use App\Payment;
use App\PaymentOther;




class ReportsController extends Controller
{
  public function index(){

    return view('reports.index');

  }


  public function getCollectionSummaryData() {

    $payments = Payment::leftjoin('hperson AS p', 'p.hpercode', '=', 'hpay.hpercode')
    ->leftjoin('hcharge AS c', 'c.chrgcode', '=', 'hpay.chrgcode')
    ->leftjoin('cashier_users AS cu', 'cu.id', '=', 'hpay.id')
    ->select('hpay.enccode', 'hpay.hpercode', 'hpay.preorno', 'hpay.amount_paid', 'hpay.id', 'hpay.payment_status', 'hpay.advance_payment', 'hpay.created_at',
            'p.patlast', 'p.patfirst', 'p.patsuffix', 'p.patmiddle', 'c.chrgdesc', 'c.acctcode', 'cu.name')
    ->orderBy('hpay.created_at', 'desc')
    ->groupBy('hpay.preorno')
    ->get();

    // $master_payments = PaymentOther::leftjoin('hcharge AS c', 'c.chrgcode', '=', 'cashier_payment_other.charge_code')
    // ->leftjoin('cashier_users AS cu', 'cu.id', '=', 'cashier_payment_other.id')
    // ->select('cashier_payment_other.payment_id', 'cashier_payment_other.patient_id', 'cashier_payment_other.prefix_or_number', 'cashier_payment_other.amount_paid',
    // 'cashier_payment_other.id', 'cashier_payment_other.payment_status', 'cashier_payment_other.payment_status', 'cashier_payment_other.created_at',
    // 'cashier_')

    // $payments = ViewMasterCollection::select('created_at', 'prefix_or_number', 'patient_name', 'amount_paid', 'account_code', 'charge_description', 'name', 'payment_status', 'collection_type')
    // ->orderBy('created_at', 'desc')
    // ->get();

    $response = array(
      'data' => $payments,
    );

    return response()->json($response);
  }
}
