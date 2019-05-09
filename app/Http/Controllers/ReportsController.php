<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ViewMasterCollection;




class ReportsController extends Controller
{
  public function index(){

    return view('reports.index');

  }


  public function getCollectionSummaryData() {

    $payments = ViewMasterCollection::select('created_at', 'prefix_or_number', 'patient_name', 'amount_paid', 'account_code', 'charge_description', 'name', 'payment_status', 'collection_type')
    ->orderBy('created_at', 'desc')
    ->get();

    $response = array('data' => $payments);
    return response()->json($response);


  }
}
