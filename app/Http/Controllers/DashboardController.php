<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Payment;

use Carbon\Carbon;

class DashboardController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $total = 0;
      $sub_total = 0;
      $current_time = Carbon::now('Asia/Manila');
      $date_time = date_format($current_time, 'Y-m-d');
      $daily_earnings_data = Payment::where('created_at', 'LIKE', $date_time . '%')->groupBy('preorno')->get();

      foreach ($daily_earnings_data as $value) {
        $sub_total = $value->amt;
        $total += $sub_total;

      }

      $total = number_format($total, 2);

      $data = array(
        'daily_earnings' => $total,
      );

        return view('dashboard.index')->with('data', $data);
    }

    // public function index ()
    // {
    // 	return view('dashboard.index');
    // }

    // public function show ()

    // {

    // }
}
