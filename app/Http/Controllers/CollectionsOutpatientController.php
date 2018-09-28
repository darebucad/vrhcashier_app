<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Payment;
use App\PatientAccount;
use App\ViewOutpatientCharge;
use App\ViewPatientAccount;
use App\Setup;
use App\MedicineCharge;
use App\ViewPayment;
use App\ViewPaymentSum;
use App;

use DataTables;
use DB;
use PDF;

use Carbon\Carbon;


class CollectionsOutpatientController extends Controller
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
    public function index()
    {
        $payments = ViewPayment::select('*')
        ->limit(20)
        ->groupBy('or_no_prefix')
        ->orderByRaw('created_at DESC')
        ->get();

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
        $or_number = Payment::select('orno')->where('id', $user_id)->orderByRaw('created_at DESC')->count();

        if ( $or_number == 0 ) {
            $or_number = Payment::select('orno')->orderByRaw('created_at DESC')->get();
        }
        else {
            $or_number = Payment::select('orno')->where('id', $user_id)->orderByRaw('created_at DESC')->get();
        }

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
        // dd($request->all());
        $charge_slip = $request->pcchrgcod;
        $current_time = Carbon::now('Asia/Manila');
        $outpatient_charges = ViewOutpatientCharge::where('pcchrgcod', $charge_slip)->get();

        foreach ($outpatient_charges as $charge){
            $data = array(
                'enccode'               => $request->enccode,
                'hpercode'              => $request->hpercode,
                'acctno'                => $request->acctno,
                'orno'                  => $request->or_number,
                'ordate'                => date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $request->ordate))),
                'amt'                   => $charge->pcchrgamt,
                'curcode'               => $request->currency,
                'paytype'               => $request->payment_type,
                'paycode'               => $request->payment_mode,
                'paystat'               => $request->paystat,
                'paylock'               => $request->paylock,
                'updsw'                 => $request->updsw,
                'confdl'                => $request->confdl,
                'payctr'                => $request->payctr += 1,
                'pcchrgcod'             => $charge_slip,
                'chrgcode'              => $charge->orderfrom,
                'itemcode'              => $charge->product_code,
                'id'                    => $request->user_id,
                'discount_name'         => $charge->disc_name,
                'discount_percent'      => $charge->disc_percent,
                'discount_computation'  => $request->discount_computation,
                'discount_value'        => $charge->computed_discount,
                'status'                => $request->status,
                'amount_paid'           => $request->amount_paid,
                'amount_tendered'       => $request->amount_tendered,
                'change'                => $request->change,
                'created_at'            => $current_time->toDateTimeString()

            );
            Payment::insert($data);
        }


        // return redirect('/collections/outpatient/print/pdf', ['' => $charge_slip]);

        // $this->showPDF($charge_slip);

        return redirect('/collections/outpatient');
    }

    public function pdfIndex() {
        // $payment_data = $this->getPaymentData('V-200127');

        $payments = ViewPayment::select('enccode', 'or_no_prefix', 'or_date', 'amount_paid')->groupBy('or_no_prefix')->orderByRaw('created_at DESC')->get();

        return view('collections.outpatient.print')->with('payments', $payments);

    }



    public function getPaymentData($id) {
        $payment_data = ViewPayment::where('or_no_prefix', $id)
        ->orderByRaw('payment_counter ASC')
        ->get();
        return $payment_data;

   }

    public function showPDF($id) {

        // $or_no = $request->ids;

        // custom paper = array (0,0,width,length)
        $customPaper = array(0,0,273.6,792);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convertPaymentDataToHtml($id))->setPaper($customPaper);
        return $pdf->download('Official Receipt ('.$id.').pdf');

        // $response = array(

        //     'id' => $or_no
        // );
        // return response()->json($response);
        //PDF::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->save('myfile.pdf')
        // $pdf = PDF::loadView('collections.outpatient.print');
        // return $pdf->download('official_receipt.pdf');
   }


    public function convertPaymentDataToHtml($id) {
        $payment_data = $this->getPaymentData($id);
        $payment_count = ViewPayment::where('or_no_prefix', $id)->count();

        $sub_total = 0;
        $total = 0;
        $output = '
        ';

        foreach ($payment_data as $value) {
            $output .='<p align="right">'.$value->or_no_prefix.'</p>
                <p align="right">'.$value->or_date.'</p>
                <p>Veterans Regional Hospital</p>
                <p>'.$value->patient_name.'</p>';

            break;
        }

        $output .= '<table width="100%">';

        if($payment_count < 9){

            foreach ($payment_data as $payment) {
                $sub_total = $payment->computed_sub_total;

                if ($sub_total == '' || $sub_total == null || $sub_total == 0.00){
                    $sub_total = $payment->amount;
                    $total += $payment->amount;
                }
                else{
                    $total += $payment->computed_sub_total;
                }

                $output .= '
                    <tr>
                        <td style="font-size: 10px;">' .$payment->product_description. '</td>
                        <td style="font-size: 10px;">' .$payment->account_code. '</td>
                        <td style="font-size: 10px;" align="right">' .number_format($sub_total, 2). '</td>
                    </tr>';
            }
        }

        else{
  
        }


        $output .= '</table>
            <p>'.number_format($total, 2) .'</p>
            <p>Amount in Words</p>
            <p>TERESITA T. TAGUINOD</p>
            <p>Supervising Administrative Officer</p>';

        foreach ($payment_data as $key) {
            $output .='<p>'.$key->employee_name.'</p>';
            break;
        }

        return $output;
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


    /**
     * Get all payments from database.
     *
     * @return \Illuminate\Http\Response
     */
    public function getdata()
    {
         $payments = ViewPayment::select('enccode', 'or_date', 'or_no_prefix', 'patient_name', 'discount', 'amount_paid', 'employee_name', 'status')
         ->groupBy('or_no_prefix')
         ->orderByRaw('created_at DESC');

         return DataTables::of($payments)
         ->addColumn('action', function($payment){
            return '<a href="#" class="btn btn-sm btn-outline-primary edit" id="'.$payment->enccode.'"> Edit</a> <a href="#" class="btn btn-sm btn-outline-danger print" id="'.$payment->or_no_prefix.'"> Print Receipt</a>'; })
         ->make(true);
    }

    /**
     * Get selected payments from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Load all patient charges from database.
     *
     * @return \Illuminate\Http\Response
     */
    public function loadData(){
        $outpatient_charges = ViewOutpatientCharge::all();
        return view('collections.outpatient.charges',compact('outpatient_charges'));

    }

    /**
     * Display all patient charges from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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


    /**
     * Apply discount to all patient charges from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function applyDiscountAll(Request $request){
        $current_time = Carbon::now('Asia/Manila');
        $charge_slip = $request->message;
        $discount_percent = $request->discount;
        $new_discount_percent = 0;

        if ($discount_percent == 'SENIOR' || $discount_percent == 'PWD') {
            $new_discount_percent = 0.20;
        }
        else {
            $new_discount_percent = ($discount_percent / 100);
        }

        $outpatient_charges = ViewOutpatientCharge::where('pcchrgcod', $charge_slip)->get();

        foreach ($outpatient_charges as $value) {

            // $disc_amount = number_format($value->pcchrgamt * $new_discount_percent, 2);
            $disc_amount = $value->pcchrgamt * $new_discount_percent;
            MedicineCharge::where('docointkey', '=', $value->docointkey)
            ->update([
                'disc_name' => $discount_percent,
                'disc_percent' => ( $new_discount_percent * 100 ),
                'disc_amount' => $disc_amount,
                'is_pay' => '1',
                'is_discount' => '1',
                'updated_at' => $current_time->toDateTimeString()]);
        }

        $updated_charges = ViewOutpatientCharge::where('pcchrgcod', $charge_slip)->get();

        $response = array(
            'charge_slip' => $charge_slip,
            'data' => $updated_charges,
            'discount_percent' => $discount_percent
        );

        return response()->json($response);

    }

      
    /**
     * Posting of records from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function post(Request $request){

        $response = array(
          'status' => 'success',
          'msg' => $request->message,
      );

        return response()->json($response); 
   }

    /**
     * Get new OR number from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getORNumber(Request $request){

        $user_id = $request->user_id;

        $or_number = Payment::select('orno')
                ->where('id', $user_id)
                ->orderByRaw('ordate DESC')
                ->get();

        return $or_number;

   }


    /**
     * Apply discount to selected patient charges from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function applyDiscountSelected(Request $request) {

        $current_time = Carbon::now('Asia/Manila');
        $charge_slip = $request->charge_slip;
        $ids = $request->ids;
        $discount_percent = $request->discount;
        $new_discount_percent = $discount_percent / 100;


        if ($discount_percent == 'SENIOR' || $discount_percent == 'PWD'){
            $new_discount_percent = 0.20;
        }

        $outpatient_charges = ViewOutpatientCharge::whereIn('docointkey', $ids)->get();

        foreach ($outpatient_charges as $value) {   

            // $disc_amount = number_format($value->pcchrgamt * $new_discount_percent, 2);
            $disc_amount = $value->pcchrgamt * $new_discount_percent;

            MedicineCharge::where('docointkey', $value->docointkey)
            ->update([
                'disc_percent' => $discount_percent,
                'disc_amount' => $disc_amount,
                'is_pay' => '1',
                'is_discount' => '1',
                'updated_at' => $current_time->toDateTimeString()
            ]);

        }

        $updated_charges = ViewOutpatientCharge::where('pcchrgcod', $charge_slip)->get();

        foreach ($updated_charges as $value) {
            MedicineCharge::where('docointkey', $value->docointkey)
            ->update([
                'disc_name' => $discount_percent,
                'is_pay' => '1'

            ]);
        }

        $data = ViewOutpatientCharge::where('pcchrgcod', $charge_slip)->get();

        $response = array(
            'charge_slip' => $charge_slip,
            'ids' => $ids,
            'discount_percent' => $discount_percent,
            'data' => $data

        );
        return response()->json($response);
   }


   /**
     * Apply discount to selected patient charges from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function updateData(Request $request) {

        $current_time = Carbon::now('Asia/Manila');
        $charge_slip = $request->message;
        $discount_percent = $request->discount;
        $new_discount_percent = 0;

        if ($discount_percent == 'SENIOR' || $discount_percent == 'PWD') {
            $new_discount_percent = 0.20;
        }
        else {
            $new_discount_percent = ($discount_percent / 100);
        }

        $outpatient_charges = ViewOutpatientCharge::where('pcchrgcod', $charge_slip)->get();

        foreach ($outpatient_charges as $value) {

            // $disc_amount = number_format($value->pcchrgamt * $new_discount_percent, 2);
            $disc_amount = $value->pcchrgamt * $new_discount_percent;
            MedicineCharge::where('docointkey', '=', $value->docointkey)
            ->update([
                'disc_name' => $discount_percent,
                'disc_percent' => $discount_percent,
                'disc_amount' => $disc_amount,
                'is_pay' => '1',
                'is_discount' => '0',
                'updated_at' => $current_time->toDateTimeString()]);
        }

        $updated_charges = ViewOutpatientCharge::where('pcchrgcod', $charge_slip)->get();

        $response = array(
            'charge_slip' => $charge_slip,
            'data' => $updated_charges,
            'discount_percent' => $discount_percent

        );

        return response()->json($response);

   }







}
