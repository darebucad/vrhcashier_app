<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Payment;
use App\PatientAccount;
use App\ViewOutpatientCharge;
use App\ViewPatientAccount;
use App\Setup;

use DataTables;
use DB;

use Carbon\Carbon;


class CollectionsOutpatientController extends Controller
{

    


    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    public function __construct()
    {
        $this->middleware('auth');
    }

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $payments = Payment::all();
        return view('collections.outpatient.index')->with('payments', $payments);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {

        // dd($id);

        $user_id = $id;

        $or_number = Payment::select('orno')
                ->where('id', $user_id)
                ->orderByRaw('ordate DESC')
                ->get();

        return view('collections.outpatient.create')->with('or_number', $or_number);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        dd($request->all());

        $charge_slip = $request->pcchrgcod;

        $current_time = Carbon::now('Asia/Manila');
        

        $outpatient_charges = ViewOutpatientCharge::where('pcchrgcod', $charge_slip)->get();

      
        foreach ($outpatient_charges as $charge){

            $data = array(

                'enccode'   => $request->enccode,
                'hpercode'  => $request->hpercode,
                'acctno'    => $request->acctno,
                'orno'      => $request->or_number,
                'ordate'    => date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $request->ordate))),
                'amt'       => $charge->pcchrgamt,
                'curcode'   => $request->currency,
                'paytype'   => $request->payment_type,
                'paycode'   => $request->payment_mode,
                'paystat'   => $request->paystat,
                'paylock'   => $request->paylock,
                'updsw'     => $request->updsw,
                'confdl'    => $request->confdl,
                'payctr'    => $request->payctr += 1,
                'pcchrgcod' => $charge_slip,
                'chrgcode'  => $charge->orderfrom,
                'itemcode'  => $charge->product_code,
                'id'        => $request->user_id,

                'discount_percent'      => $charge->disc_percent,
                'discount_computation'  => $request->discount_computation,
                'discount_value'        => $charge->disc_amount,
                'amount_paid'           => $request->amount_paid,
                'amount_tendered'       => $request->amount_tendered,
                'change'                => $request->change,
                'created_at'            => $current_time->toDateTimeString(),
                'updated_at'            => $current_time->toDateTimeString(),

            );

            Payment::insert($data);
        }

        return redirect('/collections/outpatient');

        // return redirect()->back();
        
    }

    /**
     * Display the specified resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $barcode = $request->input('search_barcode');

        if ($request->filled('search_barcode')) {
            $outpatient_charges = ViewOutpatientCharge::where('pcchrgcod', $barcode)->get();
            return view('collections.outpatient.show', compact('outpatient_charges', $barcode));
        }
        else {
            $outpatient_charges = ViewOutpatientCharge::where('pcchrgcod', '0')->get();
            return view('collections.outpatient.show', compact('outpatient_charges', '0'));
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getdata()
    {
         $payments = Payment::select('enccode', 'ordate', 'orno', 'hpercode', 'discount_percent', 'amt', 'entryby', 'status');

         return DataTables::of($payments)->addColumn('action', function($payment){
            return '<a href="#" class="btn btn-sm btn-primary edit" id="'.$payment->enccode.'"> Edit</a>'; })->make(true);

    }


    public function getCustomFilterData(Request $request)
    {

        $outpatient_charges = ViewOutpatientCharge::select(['dodate', 'pcchrgcod', 'drug_name', 'qtyissued', 'pchrgup', 'pcchrgamt']);

        return DataTables::of($outpatient_charges)
        ->filter(function ($instance) use ($request) {
            if ($request->has('pcchrgcod')) {
                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                    return Str::contains($row['pcchrgcod'], $request->get('pcchrgcod')) ? true : false;
                });
            }
        })
        ->make(true);


    }

    public function loadData(){

        $outpatient_charges = ViewOutpatientCharge::all();
        return view('collections.outpatient.charges',compact('outpatient_charges'));
    }


    public function postData(Request $request){

        $current_time = Carbon::now('Asia/Manila');
        $charge_slip = $request->message;
        $user_id = $request->user_id;

        $outpatient_charges = ViewOutpatientCharge::where('pcchrgcod', $charge_slip)->get();
        $outpatient_charges_account = ViewOutpatientCharge::select('acctno', 'enccode', 'hpercode')->where('pcchrgcod', $charge_slip)->first();
        $patient_account = ViewPatientAccount::select('next_account_no_year', 'next_account_no_increment')->first();

        $account_no = $outpatient_charges_account->acctno;

        $encounter_code = $outpatient_charges_account->enccode;
        $patient_no = $outpatient_charges_account->hpercode;
        $new_account_no = $patient_account->next_account_no_year . '-' . $patient_account->next_account_no_increment;

        if ($account_no == '' or $account_no == null){


                $data = array(
                    'paacctno'   => $new_account_no,
                    'enccode'   =>  $encounter_code,
                    'hpercode'  =>  $patient_no,
                    'upicode'   =>  null,
                    'padteas'   =>  $current_time->toDateTimeString(),
                    'patmeas'   =>  $current_time->toDateTimeString(),
                    'patotchrg' =>  '0',
                    'patotprof' =>  '0',
                    'patotamt'  =>  '0',
                    'patotdisc' =>  '0',
                    'patmsstot' =>  '0',
                    'panetamt'  =>  '0',
                    'papay'     =>  '0',
                    'pabal'     =>  '0',
                    'pastat'    =>  'A',
                    'palock'    =>  'N',
                    'datemod'   =>  null,
                    'updsw'     =>  'N',
                    'confdl'    =>  'N',
                    'ptdisc'    =>  '0',
                    'paphic'    =>  '0',
                    'id'        =>  $user_id,
                    'created_at'=>  $current_time->toDateTimeString(),
                    'updated_at'=>  $current_time->toDateTimeString()
                );

            PatientAccount::insert($data);

        }
        else{

            $new_account_no = $account_no;

        }

        $response = array(
            'charge_slip' => $charge_slip,
            'data'  =>  $outpatient_charges,
            'account_no' => $account_no,
            'new_account_no' => $new_account_no,
            'user_id' => $user_id,
            'current_time' => $current_time
           
        );

        return response()->json($response);
    }

    public function applyDiscountAll(Request $request){

        $current_time = Carbon::now('Asia/Manila');

        $charge_slip = $request->message;
        $user_id = $request->user_id;

        $outpatient_charges = ViewOutpatientCharge::where('pcchrgcod', $charge_slip)->get();


        


    }

      

    public function post(Request $request){

        $response = array(
          'status' => 'success',
          'msg' => $request->message,
      );

        return response()->json($response); 
   }

    public function getORNumber(Request $request){

        $user_id = $request->user_id;

        $or_number = Payment::select('orno')
                ->where('id', $user_id)
                ->orderByRaw('ordate DESC')
                ->get();

        return $or_number;
  

   }



}
