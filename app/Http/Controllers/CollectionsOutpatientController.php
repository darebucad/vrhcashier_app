<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App;
use App\Payment;
use App\PatientAccount;
use App\ViewOutpatientCharge;
use App\ViewPatientAccount;
use App\Setup;
use App\MedicineCharge;
use App\ViewPayment;
use App\ViewPaymentSum;
use App\ViewOutpatientChargeLab;
use App\DoctorsOrder;
use App\ViewPaymentMainOR;
use App\SuppliesCharge;
use App\ViewPaymentOR;
use App\ViewOutpatientChargeSupply;
use App\ViewOutpatientChargeExam;
use App\ViewOutpatientChargeDrugs;
use App\ViewOutpatientChargeMisc;
use App\Discount;
use App\ViewCollection;
use App\ViewMasterCollection;
use App\PaymentDetails;
use App\PaymentOther;

use DataTables;
use DB;
use PDF;
use NumberToWords\NumberToWords;
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
     * Display a listing of Out-Patient payments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $payments = ViewPayment::select('or_date', 'or_no_prefix', 'patient_name', 'discount', 'amount_paid', 'employee_name', 'status')
        // ->distinct()
        // ->limit(20)
        // ->whereNotNull('advance_payment')
        // ->orderByRaw('created_at DESC')
        // ->get();

        return view('collections.outpatient.index');
        // return view('collections.outpatient.index')->with('payments', $payments);
    }


    /**
     * Show the form for creating a new Out-Patient payment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        $user_id = $id;
        $get_or_prefix = Setup::select('or_prefix')->first();
        $or_prefix = $get_or_prefix->or_prefix;
        $payments_count = ViewPaymentOR::select('orno')->where('id', $user_id)->count();

        if ($payments_count > 0) {
          $payments = ViewPaymentOR::select('next_or_number')->where('id', $user_id)->orderBy('created_at', 'desc')->first();
          $or_number = $payments->next_or_number;

        } else {
          $or_number = '0000001';
        }

        $discounts = Discount::all();

        return view('collections.outpatient.create', compact('or_number', 'discounts', 'or_prefix'));

    }

    /**
     * Display outpatient charges filtered by charge slip number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postData(Request $request) {

        $current_time = Carbon::now('Asia/Manila');
        $_token = $request->_token;
        $charge_slip_number = $request->charge_slip_number;
        $user_id = $request->user_id;
        $account_number = '';
        $encounter_code = '';
        $patient_id = '';
        $new_account_number = '';
        $get_outpatient_charges = $this::getOutpatientCharges($charge_slip_number);


        foreach ($get_outpatient_charges as $value) {
          $account_number = $value->account_number;
          $encounter_code = $value->encounter_code;
          $patient_id = $value->patient_id;
          break;
        }

        if ($account_number == '' or $account_number == null) {
          $patient_account = ViewPatientAccount::select('next_account_no_year', 'next_account_no_increment')->first();
          $new_account_number = $patient_account->next_account_no_year . '-' . $patient_account->next_account_no_increment;
          $data = array(
              'paacctno'   => $new_account_number,
              'enccode'   =>  $encounter_code,
              'hpercode'  =>  $patient_id,
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
        } else {
            $new_account_number = $account_number;
        }

        $response = array(
          'charge_slip_number' => $charge_slip_number,
          'data'  =>  $get_outpatient_charges,
          'account_number' => $account_number,
          'new_account_number' => $new_account_number,
          'user_id' => $user_id,
          'current_time' => $current_time,
          '_token' => $_token,
          // 'is_paid' => $is_paid
        );
        return response()->json($response);
    }

    public function getOutpatientCharges($cslip) {
      $charge_slip_number = $cslip;
      $prefix = substr($charge_slip_number, 0, 1);

      if ($prefix == 'P' || $prefix == 'p') {
        $outpatient_charges = ViewOutpatientChargeDrugs::where('charge_slip_number', $charge_slip_number)->get();

      } elseif ($prefix == 'C' || $prefix == 'c') {
        $outpatient_charges = ViewOutpatientChargeSupply::where('charge_slip_number', $charge_slip_number)->get();

      } elseif ($prefix == 'B' || $prefix == 'b') {
        $outpatient_charges = ViewOutpatientChargeMisc::where('charge_slip_number', $charge_slip_number)->get();

      } else {
        $outpatient_charges = ViewOutpatientChargeExam::where('charge_slip_number', $charge_slip_number)->get();

      }

      return $outpatient_charges;

    }




    /**
     * Apply discount to all patient charges from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function applyDiscountAll(Request $request) {
        $current_time = Carbon::now('Asia/Manila');

        $_token = $request->_token;
        $charge_slip_number = $request->charge_slip_number;
        $discount_id = $request->discount;
        $user_id = $request->user_id;
        // $discount_name = $request->discount;
        // $discount_percent = $request->discount;
        // $prefix_charge_slip = substr($charge_slip_number, 0, 1);

        $get_outpatient_charges = ViewOutpatientCharge::where('charge_slip_number', $charge_slip_number)->get();

        // if ($prefix_charge_slip == 'P' || $prefix_charge_slip == 'p') {
        //   $outpatient_charges = ViewOutpatientCharge::where('charge_slip_number', $charge_slip_number)->get();
        //
        // } elseif ($prefix_charge_slip == 'C' || $prefix_charge_slip == 'c') {
        //   $outpatient_charges = ViewOutpatientChargeSupply::where('charge_slip_number', $charge_slip_number)->get();
        //
        // } else {
        //   $outpatient_charges = ViewOutpatientChargeExam::where('charge_slip_number', $charge_slip_number)->get();
        // }

        // if ($discount_name == 'SENIOR' || $discount_name == 'PWD') {
        //     $discount_percent = 20;
        //
        // } else {
        //   code...
        // }

        // $discount_percent = $discount_percent / 100;

        // foreach ($get_outpatient_charges as $value) {
        //
        //   $category = $value->charge_code;
        //   $amount = $value->amount;
        //   $disc_name = $discount_name;
        //   $disc_percent = $discount_percent;
        //   $disc_amount = $amount * ( $disc_percent / 100 );
        //
        //   if ($category == 'DRUMN') {
        //     SuppliesCharge::where('docointkey', '=', $value->order_number)->update([
        //       'disc_name' => $disc_name,
        //       'disc_percent' => $disc_percent,
        //       'disc_amount' => $disc_amount,
        //       'is_pay' => '1',
        //       'is_discount' => '1',
        //       'updated_at' => $current_time->toDateTimeString()]);
        //
        //   } elseif ($category == 'DRUME' || $category == 'DRUMP') {
        //     MedicineCharge::where('docointkey', '=', $value->order_number)->update([
        //       'disc_name' => $disc_name,
        //       'disc_percent' => $disc_percent,
        //       'disc_amount' => $disc_amount,
        //       'is_pay' => '1',
        //       'is_discount' => '1',
        //       'updated_at' => $current_time->toDateTimeString()]);
        //
        //   } else {
        //     DoctorsOrder::where('docointkey', '=', $value->order_number)->update([
        //      'disc_name' => $disc_name,
        //      'disc_percent' => $disc_percent,
        //      'disc_amount' => $disc_amount,
        //      'is_pay' => '1',
        //      'is_discount' => '1',
        //      'updated_at' => $current_time->toDateTimeString()]);
        //   }
        //
        // }


        // if ($prefix_charge_slip == 'P' || $prefix_charge_slip == 'p') {
        //   $data = ViewOutpatientCharge::where('charge_slip_number', $charge_slip_number)->get();
        //
        // } elseif ($prefix_charge_slip == 'C' || $prefix_charge_slip == 'c') {
        //   $data = ViewOutpatientChargeSupply::where('charge_slip_number', $charge_slip_number)->get();
        //
        // } else {
        //   $data = ViewOutpatientChargeExam::where('charge_slip_number', $charge_slip_number)->get();
        // }

        $discount = Discount::select('discount_percent')->where('id', $discount_id)->first();

        $response = array(
          // 'data' => $data,
          // 'charge_slip' => $charge_slip_number,
          // 'discount_name' => $discount_name,
          'discount_id' => $discount->discount_percent,
        );
        return response()->json($response);
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
        $charge_slip_number = $request->pcchrgcod;
        $current_time = Carbon::now('Asia/Manila');
        $prefix_charge_slip = substr($charge_slip_number, 0, 1);
        $payment_counter = 0;
        $charge_code = '';
        $charge_table = '';
        $or_number = $request->or_number;

        if ($prefix_charge_slip == 'P' || $prefix_charge_slip == 'p') {
          $outpatient_charges = ViewOutpatientCharge::where('charge_slip_number', $charge_slip_number)->get();

        } elseif ($prefix_charge_slip == 'C' || $prefix_charge_slip == 'c') {
          $outpatient_charges = ViewOutpatientChargeSupply::where('charge_slip_number', $charge_slip_number)->get();

        } else {
          $outpatient_charges = ViewOutpatientChargeExam::where('charge_slip_number', $charge_slip_number)->get();

        }

        foreach ($outpatient_charges as $charge){
          $category = $charge->charge_code;
          $charge_code = $charge->charge_code;
          $charge_table = $charge->charge_table;
          $sub_total = $charge->computed_sub_total;
          $amount = $charge->amount;
          $is_pay = $charge->is_pay;
          $is_discount = $charge->is_discount;
          $final_amount = 0;
          $discount_percent = $charge->disc_percent;

          if ($charge_code == '' || $charge_code == null || $charge_code == 0) {
            if ($prefix_charge_slip == 'L' || $prefix_charge_slip == 'l') {
              $charge_code = 'LABOR';
              $charge_table = 'LABOR';

            } elseif ($prefix_charge_slip == 'R' || $prefix_charge_slip == 'r') {
              $charge_code = 'RADIO';
              $charge_table = 'RADIO';

            } elseif ($prefix_charge_slip == 'T' || $prefix_charge_slip == 't') {
              $charge_code = 'PT';
              $charge_table = 'PT';

            } elseif ($prefix_charge_slip == 'D' || $prefix_charge_slip == 'd') {
              $charge_code = 'DENTA';
              $charge_table = 'DENTA';

            } else {

            }
          } else {

          }

          if ($is_pay == null) {
            $final_amount = $amount;

          } elseif ($is_pay == '1') {
            $final_amount = $sub_total;

          } elseif ($is_pay == '0' && $is_discount == null) {
            $final_amount = 0;

          } elseif ($is_pay == '0' && ($discount_percent == '100' || $discount_percent == '100.00')) {
            $final_amount = 0;
          } else {
            $final_amount = $sub_total;

          }

            $data = array(
                '/enccode'               => $request->enccode,
                '/hpercode'              => $request->hpercode,
                '/acctno'                => $request->acctno,
                'orno'                  => substr($request->or_number, 2),
                'ordate'                => date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $request->ordate))),
                'amt'                   => $final_amount,
                'curcode'               => $request->currency,
                'paytype'               => $request->payment_type,
                'paycode'               => $request->payment_mode,
                'paystat'               => $request->paystat,
                'paylock'               => $request->paylock,
                'updsw'                 => $request->updsw,
                'confdl'                => $request->confdl,
                'payctr'                => $payment_counter,
                'pcchrgcod'             => $charge_slip_number,
                'chrgcode'              => $charge_code,
                'itemcode'              => $charge->product_code,
                'id'                    => $request->user_id,
                'discount_name'         => $charge->disc_name,
                'discount_percent'      => $discount_percent,
                'discount_computation'  => $request->discount_computation,
                'discount_value'        => $charge->computed_discount,
                'payment_status'        => $request->status,
                'amount_paid'           => $request->amount_paid,
                'amount_tendered'       => $request->amount_tendered,
                'amount_change'         => $request->change,
                'created_at'            => $current_time->toDateTimeString(),
                'is_pay'                => $is_pay,
                'is_discount'           => $is_discount
            );
            Payment::insert($data);

            $payment_counter += 1;


        }


        // $outpatient_payment = ViewPayment::select('or_no_prefix')->where('charge_slip_no', $charge_slip_number)->first();

        // $or_number = $outpatient_payment->or_no_prefix;
        // return redirect('/collections/outpatient/print/pdf', ['' => $charge_slip]);
        // $this->showPDF($charge_slip);
        // return redirect('/collections/outpatient');

        // return redirect('/collections/outpatient/print/pdf', ['' => '']);

        return $this->showPDF($or_number);
    }

    public function pdfIndex() {
        // $payment_data = $this->getPaymentData('V-200127');
        $payments = ViewPayment::select('enccode', 'or_no_prefix', 'or_date', 'amount_paid')
        ->groupBy('or_no_prefix')
        ->orderByRaw('created_at DESC')
        ->get();
        return view('collections.outpatient.print')->with('payments', $payments);

    }

    public function getPaymentData($id) {
        $payment_data = ViewPayment::where('or_no_prefix', $id)
        ->groupBy('itemcode')
        ->orderByRaw('payment_counter ASC')
        ->get();
        return $payment_data;
    }

    /**
     * Show pdf official receipt
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showPDF($id) {
        $or_number = $id;

        // $outpatient_payments = ViewPayment::select('enccode', 'charge_slip_no', 'status')
        // ->where('or_no_prefix', $or_number)
        // ->first();

        // $payment_status = $outpatient_payments->status;

        // $get_outpatient_payment_data = ViewPayment::select('enccode', 'or_no_prefix', 'charge_slip_no')
        //   ->where('charge_slip_no', $charge_slip)
        //   ->first();
        // if ($payment_status == 'For Payment') {
        //
        //   Payment::where('preorno', $or_number)
        //   ->update(['payment_status' => 'Paid']);
        //
        // }
        // MedicineCharge::where('pcchrgcod', $outpatient_payments->charge_slip_no)
        // ->update(['invoice_status' => 'Paid']);

        // $or_number = $get_outpatient_payment_data->or_no_prefix;

        // custom paper = array (0,0,width,length)
        $customPaper = array(0,0,273.6,792);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convertPaymentDataToHtml($or_number))->setPaper($customPaper);

        // return $pdf->download('outpatient.collection.receipt('.$or_no.').pdf');
        return $pdf->download('outpatient.collection.receipt.pdf');

        // $response = array(
        //     'id' => $or_no
        // );
        // return response()->json($response);
        //PDF::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->save('myfile.pdf')
        // $pdf = PDF::loadView('collections.outpatient.print');
        // return $pdf->download('official_receipt.pdf');
    }

    public function convertPaymentDataToHtml($id) {
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('en');
        $payment_data = $this->getPaymentData($id);
        $payment_count = ViewPayment::where('or_no_prefix', $id)->count();
        $sub_total = 0;
        $total = 0;
        $decimal_value = 0;
        $output = '';

        foreach ($payment_data as $value) {
          $output .= '<html>
          <style>@page {margin-left: 17px; margin-right:27px;}</style></head>
          <body>
          <br>
          <br>
          <br>
          <br>
          <br>
          <p align="center" style="font-family: Helvetica; font-size: 15px; margin-right: -70px; margin-top: -26px;">'.$value->or_no_prefix.'</p>
          <p align="right" style="font-family: Helvetica; font-size: 15px; margin-right: 10px; margin-top: -6px; margin-bottom: -3px">'.$value->or_date.'<br></p>
          <p style="font-family: Helvetica; font-size: 15px; margin-left: 43px; margin-bottom: -7px;">Veterans Regional Hospital</p>
          <p style="font-family: Courier; font-size: 20px; margin-left: 43px;  margin-bottom: 10px">'.$value->patient_name.'</p><br><br>';
          break;
        }
        $output .= '<table width="100%">';

        if($payment_count <= 8) {
            foreach ($payment_data as $payment) {
              $sub_total = $payment->amount;
              $description = $payment->product_description;
              $total += $sub_total;
              $output .= '
                <tr style="line-height: 17px;">
                  <td style="font-family: Courier; font-size: 11px; width:255px;">' . $description . '</td>
                  <td style="font-family: Courier; font-size: 20px; margin-right:20px" align="right">' . number_format($sub_total, 2) . '</td>
                </tr>';
            }

            $supplemental_row = (8 - $payment_count);
            $counter = $supplemental_row;
            if($supplemental_row != 0){
                for($counter; $counter > 0; $counter--) {
                    $output .='
                        <tr style="line-height: 17px; color:white;">
                            <td>.</td>
                        </tr>
                    ';
                }
            }
        }
        else {
            foreach ($payment_data as $payment) {
              $amount_paid = $payment->amount_paid;
              $category = $payment->category;
              $total = $amount_paid;
              $output .='
                <tr style="line-height: 17px;">
                  <td style="font-family: Helvetica; font-size: 11px; width:195px;">' . $category . '</td>
                  <td style="font-family: Courier; font-size: 15px; margin-right: 20px;" align="right">' . number_format($amount_paid, 2) . '</td>
                </tr>';

              $supplemental_row = 7;
              $counter = $supplemental_row;
              if($supplemental_row != 0){
                  for($counter; $counter > 0; $counter--) {
                      $output .='
                          <tr style="line-height: 17px; color:white;">
                              <td>.</td>
                          </tr>
                      ';
                  }
              }
              break;
            }
        }

        $decimal_value = Str::substr(($total * 100), -2);
        if($decimal_value == 00){
            $decimal_value = ' and zero cent/100 only';
            // $decimal_value = ' and 00/100 only';
        }
        else if($decimal_value < 02){
            $decimal_value = ' and ' . ucwords($numberTransformer->toWords($decimal_value)) . ' cent/100 only';
            // $decimal_value = ' and ' . $decimal_value . '/100 only';
        }
        else if($decimal_value > 01){
            $decimal_value = ' and ' . ucwords($numberTransformer->toWords($decimal_value)) . ' cents/100 only';
            // $decimal_value = ' and ' . $decimal_value . '/100 only';
        }

            // <p align="left" style="font-family: Times New Roman; font-size: 12px; margin-top: -10px">'.$decimal_value.'</p>

            $new_total = number_format($total, 2, '.', '');

        $output .= '</table>
            <p align="right" style="font-family: Courier; font-size: 20px; font-weight: bold; margin-top: 4px; margin-right: 3px"><b>'. number_format($total, 2) .'</b></p>
            <p style="font-family: Courier; font-size: 13px; margin-top: -6px; margin-left: 100px">'. ucwords($numberTransformer->toWords($new_total)) . $decimal_value .'</p>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p align="right" style="font-family: Helvetica; font-size: 14px; margin-bottom: -15px; margin-top: -10px; margin-right: 13px">TERESITA T. TAGUINOD</p>
            <p align="right" style="font-family: Helvetica; font-size: 10px; margin-right: 20px">Supervising Administrative Officer</p>';

        foreach ($payment_data as $key) {
            $output .='<p align="left" style="font-family: Helvetica; font-size: 14px; margin-top: -6px; margin-left:15px;">'.$key->employee_name.'</p>';
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
    public function edit(Request $request, $id)
    {
        $or_no = $id;
        $current_time = Carbon::now('Asia/Manila');
        // $id = $request->id;
        // $user_id = $request->user_id;

        $payment_details = PaymentDetails::where('or_no_prefix', $or_no)->get();

        $data = array('data' => $payment_details);

        return view('collections.outpatient.edit', compact('data', 'payment_details'));

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
    public function loadData() {
        $outpatient_charges = ViewOutpatientCharge::all();
        return view('collections.outpatient.charges',compact('outpatient_charges'));
    }


    /**
     * Cancel selected out-patient payment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    // public function cancelPayment(Request $request){
    //     $or_no = $request->id;
    //     $user_id = $request->user_id;
    //     $current_time = Carbon::now('Asia/Manila');
    //
    //     Payment::where('preorno', $or_no)
    //     ->update([
    //         'payment_status' => 'Cancelled',
    //         'id' => $user_id,
    //         'updated_at' => $current_time->toDateTimeString()
    //     ]);
    //     return redirect('/collections/outpatient');
    // }


    /**
     * Cancel selected out-patient payment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function cancelPayment(Request $request, $id){
      $or_number = $id;
        // $or_no = $request->id;
        $user_id = $request->user_id;
        $current_time = Carbon::now('Asia/Manila');

        Payment::where('preorno', $or_number)
        ->update([
            'payment_status' => 'Cancelled',
            // 'id' => $user_id,
            'updated_at' => $current_time->toDateTimeString()
        ]);
        return redirect('/collections/outpatient/edit/' . $or_number);
    }

    /**
     * Selected payment will be set to draft .
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function draftPayment(Request $request, $id){
      $or_number = $id;
        // $or_no = $request->id;
        $user_id = $request->user_id;
        $current_time = Carbon::now('Asia/Manila');

        Payment::where('preorno', $or_number)
        ->update([
            'payment_status' => 'Draft',
            // 'id' => $user_id,
            'updated_at' => $current_time->toDateTimeString()
        ]);
        return redirect('/collections/outpatient/edit/' . $or_number);
    }


    /**
     * Mark as paid payment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function markPaid(Request $request, $id){
      $or_number = $id;
        // $or_no = $request->id;
        $user_id = $request->user_id;
        $current_time = Carbon::now('Asia/Manila');

        Payment::where('preorno', $or_number)
        ->update([
            'payment_status' => 'Paid',
            // 'id' => $user_id,
            'updated_at' => $current_time->toDateTimeString()
        ]);
        return redirect('/collections/outpatient/edit/' . $or_number);
    }








    /**
     * Posting of records from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function post(Request $request) {
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
    public function getORNumber(Request $request) {
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
        $charge_slip_number = $request->charge_slip;
        $ids = $request->ids;
        $discount_name = $request->discount;
        $discount_percent = $request->discount;
        $prefix_charge_slip = substr($charge_slip_number, 0, 1);

        if ($prefix_charge_slip == 'P' || $prefix_charge_slip == 'p') {
          $selected_patient_charges = ViewOutpatientCharge::whereIn('order_number', $ids)->get();

        } elseif ($prefix_charge_slip == 'C' || $prefix_charge_slip == 'c') {
          $selected_patient_charges = ViewOutpatientChargeSupply::whereIn('order_number', $ids)->get();

        } else {
          $selected_patient_charges = ViewOutpatientChargeExam::whereIn('order_number', $ids)->get();

        }

        if ($discount_name == 'SENIOR' || $discount_name == 'PWD'){
            $discount_percent = 20;

        } else {
          // code...
        }

        // $discount_percent = $discount_percent / 100;

        foreach ($selected_patient_charges as $value) {
          $category = $value->charge_code;
          $amount = $value->amount;
          $disc_name = $discount_name;
          $disc_percent = $discount_percent;
          $disc_amount = $amount * ( $disc_percent / 100 );

          if ($category == 'DRUME' || $category == 'DRUMP') {
            MedicineCharge::where('docointkey', $value->order_number)->update([
              'disc_percent' => $disc_percent,
              'disc_amount' => $disc_amount,
              'is_pay' => '1',
              'is_discount' => '1',
              'updated_at' => $current_time->toDateTimeString()]);

          } elseif ($category == 'DRUMN') {
            SuppliesCharge::where('docointkey', '=', $value->order_number)->update([
              'disc_percent' => $disc_percent,
              'disc_amount' => $disc_amount,
              'is_pay' => '1',
              'is_discount' => '1',
              'updated_at' => $current_time->toDateTimeString()]);

          } else {
            DoctorsOrder::where('docointkey', $value->order_number)->update([
              'disc_percent' => $disc_percent,
              'disc_amount' => $disc_amount,
              'is_pay' => '1',
              'is_discount' => '1',
              'updated_at' => $current_time->toDateTimeString()]);

          }
        }


        if ($prefix_charge_slip == 'P' || $prefix_charge_slip == 'p') {
          $outpatient_charges = ViewOutpatientCharge::where('charge_slip_number', $charge_slip_number)->get();

        } elseif ($prefix_charge_slip == 'C' || $prefix_charge_slip == 'c') {
          $outpatient_charges = ViewOutpatientChargeSupply::where('charge_slip_number', $charge_slip_number)->get();

        } else {
          $outpatient_charges = ViewOutpatientChargeExam::where('charge_slip_number', $charge_slip_number)->get();
        }

        foreach ($outpatient_charges as $value) {
          $category = $value->charge_code;
          $disc_name = $discount_name;

          if ($category == 'DRUME' || $category == 'DRUMP') {
            MedicineCharge::where('docointkey', $value->order_number)->update([
              'disc_name' => $disc_name,
              'updated_at' => $current_time->toDateTimeString()]);

          } elseif ($category == 'DRUMN') {
            SuppliesCharge::where('docointkey', $value->order_number)->update([
              'disc_name' => $disc_name,
              'updated_at' => $current_time->toDateTimeString()]);

          } else {
            DoctorsOrder::where('docointkey', $value->order_number)->update([
              'disc_name' => $disc_name,
              'updated_at' => $current_time->toDateTimeString()]);

          }

        }

        if ($prefix_charge_slip == 'P' || $prefix_charge_slip == 'p') {
          $data = ViewOutpatientCharge::where('charge_slip_number', $charge_slip_number)->get();

        } elseif ($prefix_charge_slip == 'C' || $prefix_charge_slip == 'c') {
          $data = ViewOutpatientChargeSupply::where('charge_slip_number', $charge_slip_number)->get();

        } else {
          $data = ViewOutpatientChargeExam::where('charge_slip_number', $charge_slip_number)->get();
        }

        $response = array(
          'data' => $data,
          'charge_slip_number' => $charge_slip_number,
          'ids' => $ids,
          'discount_percent' => $discount_percent,
          'discount_name' => $discount_name,
        );
        return response()->json($response);
    }

   /**
     * Apply discount to selected patient charges from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function clearDiscount(Request $request) {
     $current_time = Carbon::now('Asia/Manila');
     $charge_slip_number = $request->charge_slip;
     $new_discount_percent = 0;
     $prefix_charge_slip = substr($charge_slip_number, 0, 1);

     if ($prefix_charge_slip == 'P' || $prefix_charge_slip == 'p') {
       $outpatient_charges = ViewOutpatientCharge::where('charge_slip_number', $charge_slip_number)->get();

     } elseif ($prefix_charge_slip == 'C' || $prefix_charge_slip == 'c') {
       $outpatient_charges = ViewOutpatientChargeSupply::where('charge_slip_number', $charge_slip_number)->get();

     } else {
       $outpatient_charges = ViewOutpatientChargeExam::where('charge_slip_number', $charge_slip_number)->get();
     }

       foreach ($outpatient_charges as $value) {
         $category = $value->charge_code;

         if ($category == 'DRUME' || $category == 'DRUMP') {
           MedicineCharge::where('docointkey', '=', $value->order_number)->update([
             'disc_name' => NULL,
             'disc_percent' => NULL,
             'disc_amount' => NULL,
             'is_pay' => NULL,
             'is_discount' => NULL,
             'updated_at' => $current_time->toDateTimeString()]);
         } elseif ($category == 'DRUMN') {
           // code...
           SuppliesCharge::where('docointkey', '=', $value->order_number)->update([
             'disc_name' => NULL,
             'disc_percent' => NULL,
             'disc_amount' => NULL,
             'is_pay' => NULL,
             'is_discount' => NULL,
             'updated_at' => $current_time->toDateTimeString()]);
         } else {
           // code...
           DoctorsOrder::where('docointkey', '=', $value->order_number)->update([
             'disc_name' => NULL,
             'disc_percent' => NULL,
             'disc_amount' => NULL,
             'is_pay' => NULL,
             'is_discount' => NULL,
             'updated_at' => $current_time->toDateTimeString()]);
         }

        }

        if ($prefix_charge_slip == 'P' || $prefix_charge_slip == 'p') {
          $data = ViewOutpatientCharge::where('charge_slip_number', $charge_slip_number)->get();

        } elseif ($prefix_charge_slip == 'C' || $prefix_charge_slip == 'c') {
          $data = ViewOutpatientChargeSupply::where('charge_slip_number', $charge_slip_number)->get();

        } else {
          $data = ViewOutpatientChargeExam::where('charge_slip_number', $charge_slip_number)->get();
        }

        $response = array(
          'charge_slip' => $charge_slip_number,
          'data' => $data,
          'discount_percent' => $new_discount_percent
        );
        return response()->json($response);
   }


   /**
    * Retrieve list of outpatient payments in JSON form.
    *
    * @return \Illuminate\Http\Response
    */
   public function getOutpatientPaymentData() {
     $payments = Payment::leftjoin('hperson AS pe', 'pe.hpercode', '=', 'hpay.hpercode')
      ->leftjoin('cashier_users AS c', 'c.id', '=', 'hpay.id')
      ->leftjoin('cashier_discount AS cd', 'cd.id', '=', 'hpay.discount_id')
      ->select('hpay.enccode', 'hpay.created_at', 'hpay.preorno', 'hpay.pcchrgcod', 'pe.patlast', 'pe.patfirst', 'pe.patsuffix', 'pe.patmiddle', DB::raw('SUM(hpay.amt) AS amount'), 'c.name', 'hpay.payment_status',
      'hpay.advance_payment', 'cd.discount_name')
      // ->distinct()
      ->whereNull('hpay.advance_payment')
      ->groupBy('hpay.preorno')
      ->orderBy('hpay.created_at', 'DESC')
      ->limit(200)
      ->get();

     // $payments = ViewPayment::select('or_date', 'or_no_prefix', 'patient_name', 'discount', 'amount_paid', 'employee_name', 'status')
     // ->distinct()
     // ->whereNull('advance_payment')
     // ->orderByRaw('created_at DESC')
     // ->get();

     $response = array('data' => $payments);
     return response()->json($response);
   }

   /**
   * A function that will get the IDs of unchecked checkboxes and it will update the total.
   *
   * @return \Illuminate\Http\Response
   */
   public function updateTotal(Request $request) {
     $ids = $request->id;
     $charge_slip_number = $request->charge_slip_number;
     $prefix_charge_slip = substr($charge_slip_number, 0, 1);
     $current_time = Carbon::now('Asia/Manila');

     if ($prefix_charge_slip == 'P' || $prefix_charge_slip == 'p') {
       $outpatient_charges = ViewOutpatientCharge::whereIn('order_number', $ids)->get();

     } elseif ($prefix_charge_slip == 'C' || $prefix_charge_slip == 'c') {
       $outpatient_charges = ViewOutpatientChargeSupply::whereIn('order_number', $ids)->get();

     } else {
       $outpatient_charges = ViewOutpatientChargeExam::whereIn('order_number', $ids)->get();
     }


     foreach ($outpatient_charges as $value) {
       $category = $value->charge_code;
       $order_number = $value->order_number;

       if ($category == 'DRUME' || $category == 'DRUMP') {
         MedicineCharge::where('docointkey', '=', $order_number)->update([
           'is_pay' => 0,
           'updated_at' => $current_time->toDatetimeString()
         ]);
       } elseif ($category == 'DRUMN') {
         SuppliesCharge::where('docointkey', '=', $order_number)->update([
           'is_pay' => 0,
           'updated_at' => $current_time->toDateTimeString()
         ]);
       } else {
         DoctorsOrder::where('docointkey', '=', $order_number)->update([
           'is_pay' => 0,
           'updated_at' => $current_time->toDateTimeString()
         ]);
       }
     }

     if ($prefix_charge_slip == 'P' || $prefix_charge_slip == 'p') {
       $data = ViewOutpatientCharge::where('charge_slip_number', $charge_slip_number)->get();

     } elseif ($prefix_charge_slip == 'C' || $prefix_charge_slip == 'c') {
       $data = ViewOutpatientChargeSupply::where('charge_slip_number', $charge_slip_number)->get();

     } else {
       $data = ViewOutpatientChargeExam::where('charge_slip_number', $charge_slip_number)->get();
     }


     $response = array('data' => $data);
     return response()->json($response);

   }

   // public function getOutpatientCharges($charge_slip) {
   //   $c = $charge_slip;
   //
   // }

   // public function checkORDuplicate(Request $request) {
   //   $_token = $request->_token;
   //   $or_number = $request->or_number;
   //
   //   $getPaymentData = ViewPayment::where('or_no_prefix', $or_number)->count();
   //
   //   $data = $getPaymentData;
   //
   //   $response = array(
   //     'data' => $data,
   //     'or_number' => $or_number
   //   );
   //
   //   return response()->json($response);
   //
   //
   //
   // }

   public function getDiscountPercent(Request $request) {
     $_token = $request->_token;
     $discount_id = $request->discount_id;

     $get_discount_percent = Discount::select('discount_percent')->where('id', $discount_id)->first();

     $data = $get_discount_percent->discount_percent;

     $response = array('data' => $data);

     return response()->json($response);
   }

   public function checkORDuplicate(Request $request) {
     $_token = $request->_token;
     $or_number = $request->or_number;
     $get_or_number_count = Payment::where('preorno', $or_number)->count();
     $get_other_or_count = PaymentOther::where('prefix_or_number', $or_number)->count();
     $total_count = $get_or_number_count + $get_other_or_count;
     // $get_payment_data = ViewPaymentOR::where('preorno', $or_number)->count();

     // $get_payment_data = ViewMasterCollection::where('prefix_or_number', $or_number)->where('payment_status', 'Paid')->count();

     // $getPaymentData = ViewCollection::where('or_number', $or_number)->count();
     // $getPaymentData = ViewOtherCollection::where('prefix_or_number', $or_number)->count();
     $data = $total_count;

     $response = array(
       'total_count' => $total_count,
     );

     return response()->json($response);
   }

   // public function checkORDuplicate(Request $request) {
   //   $_token = $request->_token;
   //   $or_number = $request->or_number;
   //   $arrData = $request->arrData;
   //   $row_count = $request->row_count;
   //
   //   $get_payment_data = ViewPaymentOR::where('preorno', $or_number)->count();
   //
   //   // $get_payment_data = ViewMasterCollection::where('prefix_or_number', $or_number)->where('payment_status', 'Paid')->count();
   //
   //   // $getPaymentData = ViewCollection::where('or_number', $or_number)->count();
   //   // $getPaymentData = ViewOtherCollection::where('prefix_or_number', $or_number)->count();
   //   $data = $get_payment_data;
   //
   //   $response = array(
   //     'data' => $data,
   //     'or_number' => $or_number,
   //     '_token' => $_token,
   //     'arrData' => $arrData,
   //     'row_count' => $row_count,
   //   );
   //
   //   return response()->json($response);
   // }


   /**
    * Store a newly created payment resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function storePayment(Request $request) {
     $_token = $request->_token;
     $array_data = $request->arrData;
     $or_number = $request->or_number;
     $payment_counter = 0;
     $current_time = Carbon::now('Asia/Manila');

     $or_n = substr($or_number, strpos($or_number, "-") + 1);

     foreach($array_data as $item) {
       $data = array(
         'enccode' => $item['encounter_code'],
         'hpercode' => $item['patient_id'],
         'acctno' => $item['account_number'],
         'orno' => $or_n,
         'ordate' => date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $item['or_date']))),
         'prefix_or_number' => $item['or_number'],
         'amt' => $item['sub_total'],
         'curcode' => $item['currency'],
         'paytype' => $item['payment_type'],
         'paycode' => $item['payment_mode'],
         'paystat' => $item['paystat'],
         'paylock' => $item['payment_lock'],
         'updsw' => $item['updsw'],
         'confdl' => $item['confdl'],
         'payctr' => $payment_counter,
         'pcchrgcod' => $item['charge_slip_number'],
         'chrgcode' => $item['charge_code'],
         'itemcode' => $item['product_id'],
         'id' => $item['user_id'],
         'discount_percent' => $item['discount_percent'],
         'discount_computation' => $item['discount_computation'],
         'discount_value' => $item['discount_value'],
         'payment_status' => $item['payment_status'],
         'amount_paid' => $item['amount_paid'],
         'amount_tendered' => $item['amount_tendered'],
         'amount_change' => $item['amount_change'],
         'created_at' => $current_time->toDateTimeString(),
         'is_pay' => $item['is_pay'],
         'is_discount' => $item['is_discount'],
         'quantity' => $item['quantity'],
         'unit_price' => $item['unit_price'],
         'discount_id' => $item['discount_id']
       );

      Payment::insert($data);
      $payment_counter += + 1;
     }

     $response = array(
       'data' => $array_data

     );
     return response()->json($response);
   }


   /**
    * Print pdf official receipt
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function createPrintPdf($id) {
       $or_number = $id;

       // custom paper = array (0,0,width,length)
       $customPaper = array(0,0,273.6,792);
       $pdf = App::make('dompdf.wrapper');
       $pdf->loadHTML($this->dataToHtml($or_number))->setPaper($customPaper);

       return $pdf->download('outpatient.collection.receipt.pdf');

   }


   public function dataToHtml() {
       $numberToWords = new NumberToWords();
       $numberTransformer = $numberToWords->getNumberTransformer('en');
       $payment_count = ViewPayment::where('or_no_prefix', $id)->count();
       $sub_total = 0;
       $total = 0;
       $decimal_value = 0;
       $output = '';
       $officer = 'TERESITA T. TAGUINOD';
       $position = 'Supervising Administrative Officer';

       foreach ($payment_data as $value) {
         $output .= '<html>
         <style>@page {margin-left: 17px; margin-right:27px;}</style></head>
         <body>
         <br>
         <br>
         <br>
         <br>
         <br>
         <p align="center" style="font-family: Helvetica; font-size: 15px; margin-right: -70px; margin-top: -26px;">'.$value->or_no_prefix.'</p>
         <p align="right" style="font-family: Helvetica; font-size: 15px; margin-right: 10px; margin-top: -6px; margin-bottom: -3px">'.$value->or_date.'<br></p>
         <p style="font-family: Helvetica; font-size: 15px; margin-left: 43px; margin-bottom: -7px;">Veterans Regional Hospital</p>
         <p style="font-family: Courier; font-size: 20px; margin-left: 43px;  margin-bottom: 10px">'.$value->patient_name.'</p><br><br>';
         break;

       }
       $output .= '<table width="100%">';

       if($payment_count <= 8) {
           foreach ($payment_data as $payment) {
             $sub_total = $payment->amount;
             $description = $payment->product_description;
             $total += $sub_total;

         $output .= '
           <tr style="line-height: 17px;">
             <td style="font-family: Courier; font-size: 11px; width:255px;">' . $description . '</td>
             <td style="font-family: Courier; font-size: 12px; margin-right:20px" align="right">' . number_format($sub_total, 2) . '</td>
           </tr>';
           }

           $supplemental_row = (8 - $payment_count);
           $counter = $supplemental_row;
           if($supplemental_row != 0){
               for($counter; $counter > 0; $counter--) {
                   $output .='
                       <tr style="line-height: 17px; color:white;">
                           <td>.</td>
                       </tr>
                   ';
               }
           }

       }
       else {
           foreach ($payment_data as $payment) {
             $amount_paid = $payment->amount_paid;
             $category = $payment->category;
             $total = $amount_paid;
             $output .='
               <tr style="line-height: 17px;">
                 <td style="font-family: Helvetica; font-size: 11px; width:195px;">' . $category . '</td>
                 <td style="font-family: Courier; font-size: 15px; margin-right: 20px;" align="right">' . number_format($amount_paid, 2) . '</td>
               </tr>';
             $supplemental_row = 7;
             $counter = $supplemental_row;
             if($supplemental_row != 0){
                 for($counter; $counter > 0; $counter--) {
                     $output .='
                         <tr style="line-height: 17px; color:white;">
                             <td>.</td>
                         </tr>
                     ';
                 }
             }
             break;
           }
       }

       $decimal_value = Str::substr(($total * 100), -2);
       if($decimal_value == 00){
           $decimal_value = ' and Zero Cent/100 only';
           // $decimal_value = ' and 00/100 only';
       }
       else if($decimal_value < 02){
           $decimal_value = ' and ' . ucwords($numberTransformer->toWords($decimal_value)) . ' Cent/100 only';
           // $decimal_value = ' and ' . $decimal_value . '/100 only';
       }
       else if($decimal_value > 01){
           $decimal_value = ' and ' . ucwords($numberTransformer->toWords($decimal_value)) . ' Cents/100 only';
           // $decimal_value = ' and ' . $decimal_value . '/100 only';
       }

           // <p align="left" style="font-family: Times New Roman; font-size: 12px; margin-top: -10px">'.$decimal_value.'</p>

          $new_total = number_format($total, 2, '.', '');

       $output .= '</table>
           <p align="right" style="font-family: Courier; font-size: 20px; font-weight: bold; margin-top: 4px; margin-right: 3px"><b>'. $new_total . '&&&&' . $total . '</b></p>
           <p style="font-family: Courier; font-size: 13px; margin-top: -6px; margin-left: 100px">'.ucwords($numberTransformer->toWords($new_total)) . $decimal_value .'</p>
           <br>
           <br>
           <br>
           <br>
           <br>
           <p align="right" style="font-family: Helvetica; font-size: 14px; margin-bottom: -15px; margin-top: -10px; margin-right: 13px">'. $officer .'</p>
           <p align="right" style="font-family: Helvetica; font-size: 10px; margin-right: 20px">'. $position .'</p>';

       foreach ($payment_data as $key) {
           $output .='<p align="left" style="font-family: Helvetica; font-size: 14px; margin-top: -6px; margin-left:15px;">'.$key->employee_name.'</p>';
           break;
       }
       return $output;

  }












}
