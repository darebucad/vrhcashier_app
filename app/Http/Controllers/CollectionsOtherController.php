<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App;
use App\PaymentOther;
use App\Setup;
use App\ViewPaymentORNumber;
use App\ViewDrugsMedicines;
use App\GenericDescription;
use App\ViewProducts;
use App\Patient;
use App\ViewOtherCollection;
use App\Payment;
use App\Category;
use App\Discount;


use DataTables;
use DB;
use PDF;

use NumberToWords\NumberToWords;
use Carbon\Carbon;

use App\ViewPaymentMainOR;
use App\ViewPaymentOR;
use App\ViewCollection;

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
     * Display a listing of the other collection payments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        // $payments = ViewOtherCollection::select('prefix_or_number', 'or_number', 'receipt_date', 'patient_name', 'discount', 'amount_paid', 'name', 'payment_status')
        // ->distinct()
        // ->limit(20)
        // ->groupBy('prefix_or_number')
        // ->orderByRaw('created_at DESC')
        // ->get();
        return view('collections.other.index');
    	// return view('collections.other.index', compact('payments'));
    }


    /**
     * Show the form for creating a new other collection paymentm.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function create($id) {
        // dd($id);
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

        // $payments = ViewPaymentORNumber::select('next_or_number', 'or_prefix')
        //     ->where('id', $user_id)
        //     ->limit(1)
        //     ->count();
        //
        //
        // if ( $payments > 0 ) {
        //     $payments = ViewPaymentORNumber::select('next_or_number', 'or_prefix')
        //         ->limit(1)
        //         ->get();
        // }


        // $payments = ViewPaymentOR::select('next_or_number', 'or_prefix')
        // ->where('id', $user_id)
        // ->limit(1)
        // ->count();


        // if ($payments > 0 ) {
        //   $payments = ViewPaymentOR::select('next_or_number', 'or_prefix', 'created_at')
        //   ->where('id', $user_id)
        //   ->limit(1)
        //   ->orderBy('created_at', 'desc')
        //   ->get();
        //
        // } else {
        //   $payments = ViewPaymentOR::select('next_or_number', 'or_prefix', 'created_at')
        //   ->limit(1)
        //   ->orderBy('created_at', 'desc')
        //   ->get();
        // }


        // $patient_names = Patient::select('hpercode','patient_name')
        //     ->orderBy('patient_name', 'asc')
        //     ->limit(100)
        //     ->get();

        // return view('collections.other.create', compact('payments', 'patient_names'));
        // return view('collections.other.create', compact('payments'));

        $discounts = Discount::all();

        return view('collections.other.create', compact('or_number', 'discounts', 'or_prefix'));
    }

    /**
     * Display all products and services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showProducts(Request $request) {

        $search = $request->term;

        // if (($search == 'patient' || $search == 'id') || ($search == 'PATIENT' || $search == 'ID')) {
        //   $search = 'patient id';
        // }

        if ((STRPOS($search, 'patient') !== false || $search == 'id') || (STRPOS($search, 'PATIENT') !== false || $search == 'ID')) {

          $search = 'patient id';
        }

        $products = ViewProducts::select('item_code AS id', 'description AS text')
        ->where('lower_description', 'LIKE', '%' .  $search . '%')
        ->orWhere('upper_description', 'LIKE', '%' . $search . '%')
        ->orderBy('description', 'ASC')
        ->get();

        $response = array('items'  =>  $products, 'search' => $search);
        return response()->json($response);
    }


    // $search = $request->term;
    // $patients = Patient::where('patient_name', 'LIKE', '%'. ucfirst($search) .'%')
    //   ->limit(20)
    //   ->get();
    //
    // $data = [];
    //
    // foreach ($patients as $key => $value) {
    //   $data[] = ['id'=>$value->hpercode, 'value'=>$value->patient_name ];
    //
    //
    // }
    // return response($data);


    /**
     * Display latest price of selected drugs and medicine.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLatestPrice(Request $request) {
        $item_code = $request->id;
        $row_id = $request->row_id;
        $description = $request->description;
        $pad_length = 13;
        $pad_string = "0";
        $pad_output = str_pad($item_code, $pad_length, $pad_string, STR_PAD_LEFT);

        $drugs = ViewProducts::select('item_code', 'selling_price', 'charge_code', 'charge_table')
                    ->where('description', '=', $description)
                    ->get();

        $response = array(
            'data' => $drugs,
            'id' => $item_code,
            'pad' => $pad_output,
            'row_id' => $row_id,
            'description' => $description

        );
        return response()->json($response);

    }

    /**
     * Store a newly created other collection payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storePayment(Request $request) {
        $json_data = $request->arrData;
        $or_number = $request->or_number;
        $counter = 0;

        $or_n = substr($or_number, strpos($or_number, "-") + 1);

        foreach($json_data as $item) {
            $data = array(
                'prefix_or_number' => $item['prefix_or_number'],
                'or_number' => $or_n,
                'or_date' => date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $item['or_date']))),
                // 'patient_name' => substr(trim($item['patient_name']), 17),
                'patient_name' => ($item['patient_name']),
                'unit_cost' => $item['unit_cost'],
                'quantity' => $item['quantity'],
                'sub_total' => $item['sub_total'],
                'currency_code' => $item['currency_code'],
                'payment_type' => $item['payment_type'],
                'payment_mode' => $item['payment_mode'],
                'payment_counter' => $counter,
                'charge_code' => $item['charge_code'],
                'charge_table' => $item['charge_table'],
                'item_code' => $item['item_code'],
                'id' => $item['user_id'],
                'discount_name' => $item['discount_name'],
                'discount_percent' => $item['discount_percent'],
                'discount_computation' => $item['discount_computation'],
                'discount_value' => $item['discount_value'],
                'payment_status' => 'Paid',
                'amount_paid' => $item['amount_paid'],
                'amount_tendered' => $item['amount_tendered'],
                'amount_change' => $item['amount_change'],
                'created_at' => $item['created_at'],
                'is_pay' => $item['is_pay'],
                'is_discount' => $item['is_discount']
            );
            PaymentOther::insert($data);
            $counter = $counter + 1;

        }

        $response = array(
            'data' => $json_data
        );
        return response()->json($response);

        // $other_collection = ViewOtherCollection::select('prefix_or_number')->where('prefix_or')

        // return $this->printPDF($or_number);
        // return redirect('/collections/other/print/pdf/' . $or_number);
    } // end of function storePayment



    /**
     * Display list of patients.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPatientList(Request $request) {
      $patient_list = Patient::select('hpercode AS id', 'patient_name AS text')
      ->limit(30000)
      ->get();
      $response = array('results' => $patient_list, );
      return response()->json($response);

    }

    /**
     * Display the current list of payment data.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function getPaymentData($id) {
      $or_number = $id;

      $payment_data = PaymentOther::leftjoin('vw_products AS p', 'p.item_code', '=', 'cashier_payment_other.item_code')
      ->leftjoin('hcharge AS c', 'c.chrgcode', '=', 'cashier_payment_other.charge_code')
      ->leftjoin('cashier_users AS cu', 'cu.id', '=', 'cashier_payment_other.id')
      ->select('cashier_payment_other.*', 'p.*', 'c.*', 'cu.name')
      ->where('cashier_payment_other.prefix_or_number', $or_number)
      ->orderBy('cashier_payment_other.payment_counter', 'ASC')
      ->get();

      // $payment_data = ViewOtherCollection::where('prefix_or_number', $id)
      // ->groupBy('description')
      // ->orderByRaw('payment_counter ASC')
      // ->get();

      return $payment_data;
    }

    /**
     * Print Other Collection Official Receipt .
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function printPDF($id) {
      $or_number = $id ;
      $customPaper = array(0, 0, 273.6, 792);
      $pdf = App::make('dompdf.wrapper');
      $pdf->loadHTML($this->convertPaymentDataToHtml($or_number))->setPaper($customPaper);

      return $pdf->download('other.collection.receipt.pdf');
    }

    /**
     * Convert payment data to HTML .
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function convertPaymentDataToHtml($id) {
      $or_number = $id;
      $numberToWords = new NumberToWords();
      $numberTransformer = $numberToWords->getNumberTransformer('en');
      $payment_data = $this->getPaymentData($or_number);
      $payment_count = PaymentOther::where('prefix_or_number', $or_number)->count();
      $setup_data = Setup::select('cashier_officer', 'cashier_designation')->first();
      // $payment_count = ViewOtherCollection::where('prefix_or_number', $or_number)->count();
      $output = '';
      $sub_total = 0;
      $total = 0;
      $decimal_value = 0;
      $supplemental_row = 0;
      $sub_total_value = 0;
      $hospital_name = 'Region II Trauma and Medical Center';
      $patient_name = $payment_data[0]->patient_name;
      $employee_name = $payment_data[0]->name;
      $receipt_date = date_format($payment_data[0]->created_at, 'm/d/Y h:i:s A');
      $cashier_officer = $setup_data->cashier_officer;
      $cashier_designation = $setup_data->cashier_designation;

      foreach ($payment_data as $value) {
        $output .= '<html>
        <head>
        <style>@page { margin-left: 17px; margin-right:27px; }</style>
        </head>
        <body><br><br><br><br><br>
        <p align="center" style="font-family: Helvetica; font-size: 15px; margin-right: -70px; margin-top: -26px;">' . $or_number . '</p>
        <p align="right" style="font-family: Helvetica; font-size: 15px; margin-right: 10px; margin-top: -6px; margin-bottom: -3px">' . $receipt_date . '</p>
        <p style="font-family: Helvetica; font-size: 15px; margin-left: 43px; margin-bottom: -7px;">' . $hospital_name . '</p>
        <p style="font-family: Helvetica; font-size: 15px; margin-left: 43px;  margin-bottom: 10px">' . $patient_name . '</p><br><br>';
        break;
      }

      $output .= '<table width="100%">';

      if($payment_count <= 8) {
        foreach ($payment_data as $payment) {
          $sub_total = $payment->sub_total;
          $discount_value = $payment->discount_value;
          // $sub_total_value = $sub_total - $discount_value;

          $total = $total + $sub_total;

          $output .= '
           <tr style="line-height: 17px">
              <td style="font-family: Helvetica; font-size: 12px; width:60%;"> ' . $payment->description . '</td>
              <td style="font-family: Helvetica; font-size: 12px; margin-right=20px" align="right">'  . number_format($sub_total, 2) . '</td>
          </tr>';

        } // foreach ($payment_data as $payment) {
          $supplemental_row = (8 - $payment_count);
          $counter = $supplemental_row;
          if($supplemental_row != 0) {
            for ($counter; $counter > 0 ; $counter--) {
              $output .='<tr style="line-height: 17px; color:white;">
                          <td>.</td>
                          </tr>';
            }
          }
      } else {
        foreach ($payment_data as $payment) {
          $total = $payment->amount_paid;
          $charge_description = $payment->chrgdesc;

          $output .= '
            <tr style="line-height: 17px">
            <td style="font-family: Helvetica; font-size: 12px; width:60%;"> ' . $charge_description . '</td>
            <td style="font-family: Helvetica; font-size: 12px; margin-right=20px" align="right">'  . number_format($total, 2) . '</td>
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

      $new_total = number_format($total, 2, '.', '');

    $output .= '</table>
            <p align="right" style="font-family: Helvetica; font-size: 18px; margin-top: 4px; margin-right: 3px"><b>'.number_format($total, 2) .'</b></p>
            <p align="left" style="font-family: Helvetica; font-size: 15px; margin-top: -10px; margin-left: 90px">'. ucwords($numberTransformer->toWords($new_total)) . $decimal_value .'</p>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p align="right" style="font-family: Helvetica; font-size: 14px; margin-bottom: -15px; margin-top: -10px; margin-right: 13px">'. $cashier_officer .'</p>
            <p align="right" style="font-family: Helvetica; font-size: 10px; margin-right: 20px">' . $cashier_designation . '</p>
            <p align="left" style="font-family: Helvetica; font-size: 14px; margin-top: -8px; margin-left:15px">' . $employee_name . '</p>';
        //   foreach ($payment_data as $key) {
        //     $output .='<p align="left" style="font-family: Helvetica; font-size: 14px; margin-top: -8px; margin-left:15px">'.$key->name.'</p>';
        //     break;
        // }
      return $output;
    }


    /**
     * Searching of patient record .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
      $search = $request->term;


      // $patients = DB::select('CALL sp_patient_by_name("'.$search.'")');


      $patients = Patient::where('patient_name', 'LIKE', '%'.$search.'%')->get();




      $data = [];

      foreach ($patients as $key => $value) {
        $data[] = ['id'=>$value->patient_id, 'value'=>$value->patient_name ];

      }
      return response($data);
    }


    /**
     * Convert payment data to HTML .
     *
     * @return \Illuminate\Http\Response
     */
     public function getOtherCollectionData() {

       $payments = PaymentOther::leftjoin('cashier_users AS u', 'u.id', '=', 'cashier_payment_other.id')
       ->leftjoin('cashier_discount AS cd', 'cd.id', '=', 'cashier_payment_other.discount_id')
       ->select('cashier_payment_other.payment_id', 'cashier_payment_other.created_at', 'cashier_payment_other.prefix_or_number', 'cashier_payment_other.patient_name', 'cashier_payment_other.charge_slip_number',
       DB::raw('SUM(cashier_payment_other.sub_total) AS total'), 'cashier_payment_other.payment_status', 'u.name', 'cd.discount_name')
       ->whereNull('cashier_payment_other.charge_slip_number')
       ->groupBy('cashier_payment_other.prefix_or_number')
       ->orderBy('cashier_payment_other.created_at', 'DESC')
       ->limit(200)
       ->get();


       // $payments = ViewOtherCollection::select('prefix_or_number', 'or_number', 'receipt_date', 'patient_name', 'discount', 'amount_paid', 'name', 'payment_status')
       // ->distinct()
       // ->whereNull('charge_slip_number')
       // ->orderByRaw('created_at DESC')
       // ->get();

       $response = array('data' => $payments);
       return response()->json($response);
     }


     public function checkORDuplicate(Request $request) {
       $_token = $request->_token;
       $or_number = $request->or_number;
       $get_or_number_count = Payment::where('preorno', $or_number)->count();
       $get_other_or_count = PaymentOther::where('prefix_or_number', $or_number)->count();
       $total_count = $get_or_number_count + $get_other_or_count;
       // $arrData = $request->arrData;
       // $row_count = $request->row_count;

       // $getPaymentData = ViewCollection::where('or_number', $or_number)->count();
       // $getPaymentData = ViewOtherCollection::where('prefix_or_number', $or_number)->count();

       // $data = $getPaymentData;

       // $data = $total_count;

       $response = array(
         'total_count' => $total_count,
         // 'data' => $data,
         // 'or_number' => $or_number,
         // '_token' => $_token,
         // 'arrData' => $arrData,
         // 'row_count' => $row_count,
       );

       return response()->json($response);
     }






     /**
      * Cancel selected other payment.
      *
      * @param \Illuminate\Http\Request $request
      * @return \Illuminate\Http\Response
      */
     public function cancelPayment(Request $request, $id){
       $or_number = $id;
         // $or_no = $request->id;
         $user_id = $request->user_id;
         $current_time = Carbon::now('Asia/Manila');

         PaymentOther::where('prefix_or_number', $or_number)
         ->update([
             'payment_status' => 'Cancelled',
             // 'id' => $user_id,
             'updated_at' => $current_time->toDateTimeString()
         ]);
         return redirect('/collections/other');
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

         PaymentOther::where('prefix_or_number', $or_number)
         ->update([
             'payment_status' => 'Draft',
             // 'id' => $user_id,
             'updated_at' => $current_time->toDateTimeString()
         ]);
         return redirect('/collections/other');
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

         PaymentOther::where('prefix_or_number', $or_number)
         ->update([
             'payment_status' => 'Paid',
             // 'id' => $user_id,
             'updated_at' => $current_time->toDateTimeString()
         ]);
         return redirect('/collections/other');
     }




}
