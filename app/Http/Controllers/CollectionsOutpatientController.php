<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Payment;
use App\ViewOutpatientCharge;
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
    public function create()
    {
        // $outpatient_charges = ViewOutpatientCharge::all();
        // return view('collections.outpatient.create')->with('outpatient_charges', $outpatient_charges);
        return view('collections.outpatient.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


     

        $charge_slip = $request->pcchrgcod;
        $current_time = Carbon::now();

        $outpatient_charges = ViewOutpatientCharge::where('pcchrgcod', $charge_slip)->get();

      
        foreach ($outpatient_charges as $charge){

            $data = array(
                'enccode' => $request->enccode,
                'hpercode' => $request->hpercode,
                'acctno' => $request->acctno,
                'orno' => $request->or_number,
                'ordate' => date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $request->ordate))),
                'amt' => $charge->pcchrgamt,
                'curcode' => $request->currency,
                'paytype' => $request->payment_type,
                'paycode' => $request->payment_mode,
                'paystat' => $request->paystat,
                'paylock' => $request->paylock,
                'updsw' => $request->updsw,
                'confdl' => $request->confdl,
                'payctr' => $request->payctr += 1,
                'pcchrgcod' => $charge_slip,
                'chrgcode' => $charge->order_from,
                'itemcode' => $charge->product_code,
                'discount_percent' => $request->discount_percent,
                'discount_computation' => $request->discount_computation,
                'amount_tendered' => $request->amount_tendered,
                'change' => $request->change,
                'created_at' => $current_time->toDateTimeString(),
                'updated_at' => $current_time->toDateTimeString(),
            );

               Payment::insert($data);
        }


        
        return redirect('/collections/outpatient');

        // return redirect()->back();
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
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

         return DataTables::of($payments)
         ->addColumn('action', function($payment){
            return '<a href="#" class="btn btn-xs btn-primary edit" id="'.$payment->enccode.'"> Edit</a>'; 
        })
         ->make(true);

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


 



}
