<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 
use App\PaymentOther;
use App\Setup;
use App\ViewPaymentORNumber;


class CollectionsOtherController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $payments = PaymentOther::select('*')
        ->limit(20)
        ->orderByRaw('created_at DESC')
        ->get();

    	return view('collections.other.index')->with('payments', $payments);
    	
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id) {
        // dd($id);
        $user_id = $id;
        $payments = ViewPaymentORNumber::select('next_or_number', 'or_prefix')
            ->where('id', $user_id)
            ->limit(1)
            ->count();

        if ( $payments < 1 ) {
            $payments = ViewPaymentORNumber::select('next_or_number', 'or_prefix')
                ->limit(1)
                ->get();
        }


        $patient_names = PaymentOther::select('patient_name')->get();

        return view('collections.other.create', compact('payments', 'patient_names'));
    }


}
