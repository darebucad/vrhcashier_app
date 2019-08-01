<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App;
use App\PaymentOther;
use App\ViewPaymentOR;
use App\WalkinCharge;
use App\Discount;
use App\WalkinDrugs;
use App\WalkinSupply;
use App\WalkinExam;
use App\WalkinMisc;
use App\ViewOtherCollection;
use App\ViewCollection;
use App\ViewWalkinChargeDrugs;
use App\ViewWalkinChargeExam;
use App\ViewWalkinChargeMisc;
use App\ViewWalkinChargeSupply;
use App\Setup;
use App\Payment;
use App\ViewProducts;
use App\Category;


use Carbon\Carbon;
use NumberToWords\NumberToWords;
use DB;


class CollectionsWalkinController extends Controller
{

  /**
   * Prevent unauthorized access to the web pages .
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth');
  }


  /**
   * Display a listing of the walkin collection payments.
   *
   */
  public function index() {
    return view('collections.walkin.index');

  }


  /**
   * Show the form for creating a new Walk-In payment.
   *
   * @param $id
   * @return \Illuminate\Http\Response
   */
  public function create($id)
  {
      $user_id = $id;
      $get_or_prefix = Setup::select('or_prefix')->first();
      $or_prefix = $get_or_prefix->or_prefix;
      $payments_count = ViewPaymentOR::select('orno')->where('id', $user_id)->count();
      // $payments = ViewPaymentOR::select('next_or_number', 'or_prefix')->where('id', $user_id)->limit(1)->count();


      if ($payments_count > 0) {
        $payments = ViewPaymentOR::select('next_or_number')->where('id', $user_id)->orderBy('created_at', 'desc')->first();
        $or_number = $payments->next_or_number;
      } else {
        $or_number = '0000001';
      }
      // if ( $payments > 0 ) {
      //   $payments = ViewPaymentOR::select('next_or_number','or_prefix', 'created_at')
      //   ->where('id', $user_id)
      //   ->limit(1)
      //   ->orderBy('created_at', 'desc')
      //   ->get();
      //
      // }
      // else {
      //   $payments = ViewPaymentOR::select('next_or_number', 'or_prefix', 'created_at')
      //   ->limit(1)
      //   ->orderBy('created_at', 'desc')
      //   ->get();
      //
      // }

      $discounts = Discount::all();

      return view('collections.walkin.create', compact('or_number', 'discounts', 'or_prefix'));

  }

    /**
    * Search for Walk-In patient charges .
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function searchWalkinCharges(Request $request) {
      $charge_slip_number = $request->charge_slip_number;
      $user_id = $request->user_id;
      $or_number = $request->or_number;
      $get_walkin_charges = $this::getWalkinCharges($charge_slip_number);
      $discount = Discount::select('id', 'discount_percent')->get();

      $response = array(
        'data' => $get_walkin_charges,
        'charge_slip_number' => $charge_slip_number,
        'user_id' => $user_id,
        'discount' => $discount,
      );

      return response()->json($response);
    }

    public function getWalkinCharges($cslip) {
      $charge_slip_number = $cslip;
      // $prefix = strpos($charge_slip_number, "-");
      $prefix = substr($charge_slip_number, 0, 2);

      if ($prefix == 'WD' || $prefix == 'wd') {
        $walkin_charges = ViewWalkinChargeDrugs::where('charge_slip_number', $charge_slip_number)->get();

      } elseif ($prefix == 'WS' || $prefix == 'ws') {
        $walkin_charges = ViewWalkinChargeSupply::where('charge_slip_number', $charge_slip_number)->get();

      } elseif ($prefix == 'WM' || $prefix == 'wm') {
        $walkin_charges = ViewWalkinChargeMisc::where('charge_slip_number', $charge_slip_number)->get();

      } else {
        $walkin_charges = ViewWalkinChargeExam::where('charge_slip_number', $charge_slip_number)->get();

      }

      return $walkin_charges;

    }


    /**
    * Get the discount percent by discount id .
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function getDiscountPercent(Request $request) {
      $discount_id = $request->discount_id;
      $discount_percent = Discount::select('discount_percent')->where('id', $discount_id)->first();
      $response = array( 'discount_percent' => $discount_percent->discount_percent );

      return response()->json($response);
    }


    /**
    * Apply discount to all charges .
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function applyDiscountAll(Request $request) {
      $discount_id = $request->discount_id;
      $charge_slip_number = $request->charge_slip_number;
      $discount_percent = $this::getDiscount($discount_id);
      $prefix_charge_slip_number = substr($charge_slip_number, 0, 2);
      $walkin_id = $this::getWalkinData($charge_slip_number)->walkinid;
      $charge_type = $this::getWalkinData($charge_slip_number)->chargetype;
      $current_time = Carbon::now('Asia/Manila');
      $user_id = $request->user_id;
      $_token = $request->_token;

      $data = $this::selectWalkinTable($discount_id, $prefix_charge_slip_number, $walkin_id, $charge_type, $current_time, $charge_slip_number);

      $response = array(
        'data' => $data,
        'discount_percent' => $discount_percent,
        'walkin_id' => $walkin_id,
        'charge_type' => $charge_type,
        'prefix' => $prefix_charge_slip_number,
        'discount_id' => $discount_id,
        'charge_slip_number' => $charge_slip_number,
        'user_id' => $user_id,
        'token' => $_token
      );

      return response()->json($response);
    }

    /**
    * Get discount by discount id .
    *
    * @param $id
    * @return \Illuminate\Http\Response
    */
    public function getDiscount($id) {
      $discount_id = $id;
      $discount = Discount::select('discount_percent')->where('id', $discount_id)->first();
      $discount_percent = $discount->discount_percent;

      return $discount_percent;
    }

    /**
    * Get walk-in charges by charge slip number .
    *
    * @param $id
    * @return \Illuminate\Http\Response
    */
    public function getWalkinData($id) {
      $charge_slip_number = $id;
      $walkin_data = WalkinCharge::select('walkinid', 'chargetype')->where('chargeslipno', $charge_slip_number)->first();

      return $walkin_data;
    }

    /**
    * Get all walk-in charges by charge slip numbers .
    *
    * @param $discount_id, $prefix_charge_slip_number, $walkin_id, $charge_type, $current_time, $charge_slip_number
    * @return \Illuminate\Http\Response
    */
    public function selectWalkinTable($d_id, $prefix_cslip, $w_id, $c_type, $c_time, $cslip) {
      if ($prefix_cslip == 'WD' || $prefix_cslip == 'wd') {
        $walkin_charges = WalkinDrugs::where('walkinid', $w_id)->get();

      } elseif ($prefix_cslip == 'WS' || $prefix_cslip == 'ws') {
        $walkin_charges = WalkinSupply::where('walkinid', $w_id)->get();

      } elseif ($prefix_cslip == 'WM' || $prefix_cslip == 'wm') {
        $walkin_charges = WalkinMisc::where('walkinid', $w_id)->get();

      } else {
        $walkin_charges = WalkinExam::where('walkinid', $w_id)->get();

      }

      foreach ($walkin_charges as $value) {
        $discount_id = $d_id;
        $category = $c_type;
        $product_id = '';
        $is_pay = '1';
        $is_discount = '1';
        $updated_at = $c_time;

        if ($category == 'DRUME') {
          $product_id = $value->walkindrugid;
          WalkinDrugs::where('walkindrugid', $product_id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);

        } elseif ($category == 'DRUMN') {
          $product_id = $value->walkinsupplyid;
          WalkinSupply::where('walkinsupplyid', $product_id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);

        } elseif ($category == 'MISC') {
          $product_id = $value->walkinmiscid;
          WalkinMisc::where('walkinmiscid', $product_id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);

        } else {
          $product_id = $value->walkinexamid;
          WalkinExam::where('walkinexamid', $product_id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);
        }
      }

      $data = WalkinCharge::where('chargeslipno', $cslip)->get();

      return $data;
    }

    /**
    * Apply discount to selected charges .
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function applyDiscountSelected(Request $request) {
      $_token = $request->_token;
      $discount_id = $request->discount_id;
      $discount_percent = $this::getDiscount($discount_id);
      $charge_slip_number = $request->charge_slip_number;
      $prefix_charge_slip_number = substr($charge_slip_number, 0, 2);
      $id = $request->id;
      $current_time = Carbon::now('Asia/Manila');
      $walkin_id = $this::getWalkinData($charge_slip_number)->walkinid;
      $charge_type = $this::getWalkinData($charge_slip_number)->chargetype;
      $user_id = $request->user_id;

      $data = $this::searchSelectedWalkinCharges(
        $discount_id,
        $prefix_charge_slip_number,
        $walkin_id,
        $charge_type,
        $current_time,
        $charge_slip_number,
        $id
      );

      $response = array(
        'data' => $data,
        'id' => $id,
        'token' => $_token,
        'charge_slip_number' => $charge_slip_number,
        'user_id' => $user_id
      );

      return response()->json($response);
    }


    /**
    * Get selected walk-in charges by charge slip numbers .
    *
    * @param $discount_id, $prefix_charge_slip_number, $walkin_id, $charge_type, $current_time, $charge_slip_number, $id
    * @return \Illuminate\Http\Response
    */
    public function searchSelectedWalkinCharges($d_id, $prefix_cslip, $w_id, $c_type, $c_time, $cslip, $id) {
      // if ($prefix_cslip == 'WD' || $prefix_cslip == 'wd') {
      //   $selected_walkin_charges = WalkinDrugs::whereIn('walkindrugid', $id)->get();
      //   // $walkin_charges = WalkinDrugs::where('walkinid', $w_id)->get();
      //
      // } else {
      //   // code...
      // }

      // Get selected walkin charges
      $selected_walkin_charges = WalkinCharge::where('chargeslipno', $cslip)->whereIn('id', $id)->get();

      foreach ($selected_walkin_charges as $value) {
        $discount_id = $d_id;
        $category = $c_type;
        $product_id = $value->id;
        $is_pay = '1';
        $is_discount = '1';
        $updated_at = $c_time;

        if ($category == 'DRUME') {
          WalkinDrugs::where('walkindrugid', $product_id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);

        } elseif ($category == 'DRUMN') {
          WalkinSupply::where('walkinsupplyid', $product_id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);

        } elseif ($category == 'MISC') {
          WalkinMisc::where('walkinmiscid', $product_id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);

        } else {
          WalkinExam::where('walkinexamid', $product_id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);

        }
      }

      // Get unselected walkin charges
      $unselected_walkin_charges = WalkinCharge::where('chargeslipno', $cslip)->whereNotIn('id', $id)->get();

      foreach ($unselected_walkin_charges as $value) {
        $discount_id = $d_id;
        $category = $c_type;
        $product_id = $value->id;
        $is_pay = '1';
        $is_discount = '0';
        $updated_at = $c_time;

        if ($category == 'DRUME') {
          WalkinDrugs::where('walkindrugid', $product_id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);
        } elseif ($category == 'DRUMN') {
          WalkinSupply::where('walkinsupplyid', $product_id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);

        } elseif ($category == 'MISC') {
          WalkinMisc::where('walkinmiscid', $product_id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);

        } else {
          WalkinExam::where('walkinexamid', $product_id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);
        }
      }
      $data = WalkinCharge::where('chargeslipno', $cslip)->get();

      return $data;
    }


    /**
    * Clear the discounts of the charges .
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function clearDiscount(Request $request) {
      $_token = $request->_token;
      $discount_id = $request->discount_id;
      $charge_slip_number = $request->charge_slip_number;
      $user_id = $request->user_id;
      $current_time = Carbon::now('Asia/Manila');
      $prefix_charge_slip_number = substr($charge_slip_number, 0, 2);
      $charge_type = $this::getwalkinData($charge_slip_number)->chargetype;

      $data = $this::clearDiscountWalkinCharges(
        $discount_id,
        $charge_slip_number,
        $charge_type,
        $current_time
      );

      $response = array(
        'data' => $data,
        'token' => $_token,
        'charge_slip_number' => $charge_slip_number,
        'user_id' => $user_id
      );

      return response()->json($response);
    }

    public function clearDiscountWalkinCharges( $d_id, $cslip, $c_type, $c_time ) {
      $discount_id = $d_id;
      $charge_slip_number = $cslip;
      $category = $c_type;
      $updated_at = $c_time;
      $product_id = '';
      $is_pay = '1';
      $is_discount = '0';

      $walkin_charges = WalkinCharge::where('chargeslipno', $charge_slip_number)->get();

      foreach ($walkin_charges as $value) {
        $id = $value->id;

        if ($category == 'DRUME') {
          WalkinDrugs::where('walkindrugid', $id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);

        } elseif ($category == 'DRUMN') {
          WalkinSupply::where('walkinsupplyid', $id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);

        } elseif ($category == 'MISC') {
          WalkinMisc::where('walkinmiscid', $id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);

        } else {
          WalkinExam::where('walkinexamid', $id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);
        }
      }

      $walkin_charges = WalkinCharge::where('chargeslipno', $charge_slip_number)->get();

      return $walkin_charges;
    }


    public function updateTotals(Request $request) {
      $_token = $request->_token;
      $charge_slip_number = $request->charge_slip_number;
      $user_id = $request->user_id;
      $discount_id = $request->discount_id;
      $pay_ids = $request->pay_ids;
      $discount_ids = $request->discount_ids;
      $current_time = Carbon::now('Asia/Manila');
      $charge_type = $this::getWalkinData($charge_slip_number)->chargetype;
      $id = '';
      $is_pay = '0';
      $is_discount = '0';

      $data = $this::updateTotalsWalkinCharges(
        $discount_id,
        $charge_slip_number,
        $charge_type,
        $current_time,
        $pay_ids,
        $discount_ids
      );

      $response = array(
        'data' => $data,
        'pay_ids' => $pay_ids,
        'discount_ids' => $discount_ids,
        'discount_id' => $discount_id,
        'token' => $_token,
        'charge_slip_number' => $charge_slip_number,
        'user_id' => $user_id
      );

      return response()->json($response);
    }


    public function updateTotalsWalkinCharges( $d_id, $cslip, $c_type, $c_time, $pids, $dids ) {
      $discount_id = $d_id;
      $charge_slip_number = $cslip;
      $charge_type = $c_type;
      $updated_at = $c_time;
      $pay_ids = $pids;
      $discount_ids = $dids;
      $walkin_charges = WalkinCharge::where('chargeslipno', $charge_slip_number)->get();

      foreach ($walkin_charges as $key => $value) {
        $id = $walkin_charges[$key]->id;
        $discount_id = $d_id;
        $is_pay = '1';
        $is_discount = '0';
        $updated_at = $c_time;

        if ($pay_ids == null) {
          // code...
        } else {
          $free_walkin_charges = WalkinCharge::where('chargeslipno', $charge_slip_number)->whereIn('id', $pay_ids)->get();
          foreach ($free_walkin_charges as $key => $value) {
            if ($id == $free_walkin_charges[$key]->id) {
              $discount_id = null;
              $is_pay = '0';
              $is_discount = '0';
              break;
            }
          }
        }

        // Discount percentage
        if ($discount_id == null) {
          // code...
        } else {
          // Array of discount checkboxes
          if ($discount_ids == null) {
            $discount_id = $d_id;
            $is_pay = '1';
            $is_discount = '1';

          } else {
            // Array data of discount checkboxes
            $discount_walkin_charges = WalkinCharge::where('chargeslipno', $charge_slip_number)->whereIn('id', $discount_ids)->get();
            foreach ($discount_walkin_charges as $key => $value) {
              if ($id == $discount_walkin_charges[$key]->id) {
                $discount_id = $d_id;
                $is_pay = '1';
                $is_discount = '1';
                break;
              }
            }
          }
        }

        if ($charge_type == 'DRUME') {
          WalkinDrugs::where('walkindrugid', $id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);
        } elseif ($charge_type == 'DRUMN') {
          WalkinSupply::where('walkinsupplyid', $id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);
        } elseif ($charge_type == 'MISC') {
          WalkinMisc::where('walkinmiscid', $id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);
        } else {
          WalkinExam::where('walkinexamid', $id)->update([
            'discount_id' => $discount_id,
            'is_pay' => $is_pay,
            'is_discount' => $is_discount,
            'updated_at' => $updated_at
          ]);
        }
      }
      $data = WalkinCharge::where('chargeslipno', $charge_slip_number)->get();

      return $data;
    }


    /**
    * Save walk-in charges .
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function saveWalkinCharges(Request $request) {
      $_token = $request->_token;
      $array_data = $request->arrData;
      $or_number = $request->or_number;
      $payment_counter = 0;
      $current_time = Carbon::now('Asia/Manila');

      $or_n = substr($or_number, strpos($or_number, "-") + 1);

      foreach ($array_data as $item) {
        $data = array(
          'prefix_or_number' => $item['or_number'],
          'or_number' => $or_n,
          'or_date' => date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $item['or_date']))),
          'patient_id' => $item['patient_id'],
          'patient_name' => $item['patient_name'],
          'unit_cost' => $item['unit_price'],
          'quantity' => $item['quantity'],
          'sub_total' => $item['sub_total'],
          'currency_code' => $item['currency'],
          'payment_type' => $item['payment_type'],
          'payment_mode' => $item['payment_mode'],
          'payment_counter' => $payment_counter,
          'charge_code' => $item['charge_code'],
          'item_code' => $item['product_id'],
          'id' => $item['user_id'],
          'discount_id' => $item['discount_id'],
          'payment_status' => $item['payment_status'],
          'amount_paid' => $item['amount_paid'],
          'amount_tendered' => $item['amount_tendered'],
          'amount_change' => $item['amount_change'],
          'created_at' => $current_time->toDateTimeString(),
          'is_pay' => $item['is_pay'],
          'is_discount' => $item['is_discount'],
          'charge_slip_number' => $item['charge_slip_number']
        );

        PaymentOther::insert($data);
        $payment_counter += + 1;
      }

      $response = array(
        'data' => $data,
      );

      return response()->json($response);
    }

    public function printPDF($id) {
      $or_number = $id;
      $customPaper = array(0, 0, 273.6, 792);
      $pdf = App::make('dompdf.wrapper');
      $pdf->loadHTML($this->convertDataToHtml($or_number))->setPaper($customPaper);

      return $pdf->download('collections.walkin.receipt.pdf');
    }

    private function convertDataToHtml($or_n) {
      $or_number = $or_n;
      $numberToWords = new NumberToWords();
      $numberTransformer = $numberToWords->getNumberTransformer('en');
      $sub_total = 0;
      $total = 0;
      $decimal_value = 0;
      $supplemental_row = 0;
      $sub_total_value = 0;
      $output = '';
      $payment_data = $this->getWalkinPaymentData($or_n);
      $payment_count = PaymentOther::where('prefix_or_number', $or_n)->count();
      $setup_data = Setup::select('cashier_officer', 'cashier_designation')->first();
      // $payment_count = $payment_data->count();
      // $payment_count = ViewOtherCollection::where('prefix_or_number', $or_number)->count();
      // $receipt_date = '';
      // $patient_name = '';
      // $employee_name = '';

// echo date("G:i", strtotime($time));
// or you can try like this also
//
// echo date("H:i", strtotime("04:25 PM"));
//
// echo date_format($date, 'Y-m-d H:i:s');

      $receipt_date = date_format($payment_data[0]->created_at, 'm/d/Y h:i:s A');
      // $receipt_date = $payment_data[0]->receipt_date;
      $patient_name = $payment_data[0]->patient_name;
      $employee_name = $payment_data[0]->name;
      $hospital_name = 'Region II Trauma and Medical Center';
      $cashier_officer_name = $setup_data->cashier_officer;
      $cashier_officer_designation = $setup_data->cashier_designation;
      // $cashier_officer_name = 'TERESITA T. TAGUINOD';
      // $cashier_officer_designation = 'Supervising Administrative Officer';


      $output .= '<html>
        <head>
        <style>@page { margin-left: 17px; margin-right: 27px; }</style>
        </head>
        <body><br><br><br><br><br>
        <p align="center" style="font-family: Helvetica; font-size: 15px; margin-right: -70px; margin-top: -26px;">'. $or_number .'</p>
        <p align="right" style="font-family: Helvetica; font-size: 15px; margin-right: 10px; margin-top: -6px; margin-bottom: -3px">' . $receipt_date . '</p>
        <p style="font-family: Helvetica; font-size: 15px; margin-left: 43px; margin-bottom: -7px;">'. $hospital_name .'</p>
        <p style="font-family: Helvetica; font-size: 15px; margin-left: 43px;  margin-bottom: 10px">' . $patient_name . '</p><br><br>';

      $output .= '<table width="100%">';

      if ($payment_count <= 8) {
        foreach ($payment_data as $payment) {
          $sub_total = $payment->sub_total;
          $total += $sub_total;
          $description = $payment->description;
          $output .= '
            <tr style="line-height: 17px">
              <td style="font-family: Helvetica; font-size: 12px; width:60%;"> ' . $description . '</td>
              <td style="font-family: Helvetica; font-size: 12px; margin-right=20px" align="right">'  . number_format($sub_total, 2) . '</td>
            </tr>';
        }

        $supplemental_row = ( 8 - $payment_count );
        $counter = $supplemental_row;

        if ($supplemental_row != 0) {
          for ($counter; $counter > 0 ; $counter--) {
            $output .= '
              <tr style="line-height: 17px; color:white;">
              <td>.</td>
              </tr>';
          }
        }
      } else {
        foreach ($payment_data as $payment) {
          $sub_total = $payment->amount_paid;
          $total = $sub_total;
          $charge_description = $payment->chrgdesc;
          // $charge_description = $payment->charge_description;
          $output .= '
            <tr style="line-height: 17px;">
              <td style="font-family: Helvetica; font-size: 12px; width:195px;">'. $charge_description .'</td>
              <td style="font-family: Helvetica; font-size: 12px; margin-right: 20px;" align="right">' .number_format($sub_total, 2). '</td>
            </tr>';

          $supplemental_row = 7;
          $counter = $supplemental_row;

          for($counter; $counter > 0; $counter--) {
            $output .='
              <tr style="line-height: 17px; color: white;">
                <td>.</td>
              </tr>
            ';
          }

          break;
        }
      }

      $decimal_value = Str::substr(($total * 100), -2);

      if ($decimal_value == 00) {
        $decimal_value = ' and zero cent/100 only';
        // $decimal_value = ' and 00/100 only';

        // code...
      } elseif ($decimal_value < 02) {
        $decimal_value = ' and ' . ucwords($numberTransformer->toWords($decimal_value)) . ' cent/100 only';
        // $decimal_value = ' and ' . $decimal_value . '/100 only';

        // code...
      } else {
        $decimal_value = ' and ' . ucwords($numberTransformer->toWords($decimal_value)) . ' cents/100 only';
        // $decimal_value = ' and ' . $decimal_value . '/100 only';
      }


      $new_total = number_format($total, 2, '.', '');

      $output .= '</table>';
      $output .= '
        <p align="right" style="font-family: Helvetica; font-size: 18px; margin-top: 4px; margin-right: 3px"><b>'.number_format($total, 2) .'</b></p>
        <p align="left" style="font-family: Helvetica; font-size: 15px; margin-top: -10px; margin-left: 90px">'. ucwords($numberTransformer->toWords($new_total)) . $decimal_value .'</p>
        <br>
        <br>
        <br>
        <br>
        <br>
        <p align="right" style="font-family: Helvetica; font-size: 14px; margin-bottom: -15px; margin-top: -10px; margin-right: 13px">'. $cashier_officer_name .'</p>
        <p align="right" style="font-family: Helvetica; font-size: 10px; margin-right: 20px">'. $cashier_officer_designation .'</p>
        <p align="left" style="font-family: Helvetica; font-size: 14px; margin-top: -8px; margin-left:15px">'. $employee_name .'</p>';


      return $output;
    }

    private function getWalkinPaymentData($or_n) {

      // SELECT cp.*, p.*, c.*
      // FROM cashier_payment_other cp
      // LEFT JOIN vw_products p
      // ON p.item_code = cp.item_code
      // LEFT JOIN hcharge c
      // ON c.chrgcode = cp.charge_code
      // LEFT JOIN cashier_users cu
      // ON cu.id = cp.id
      // WHERE cp.prefix_or_number = 'B-0000089'


      $walkin_payment_data = PaymentOther::leftjoin('vw_products AS p', 'p.item_code', '=', 'cashier_payment_other.item_code')
      ->leftjoin('hcharge AS c', 'c.chrgcode', '=', 'cashier_payment_other.charge_code')
      ->leftjoin('cashier_users AS cu', 'cu.id', '=', 'cashier_payment_other.id')
      ->select('cashier_payment_other.*', 'p.*', 'c.*', 'cu.name')
      ->where('cashier_payment_other.prefix_or_number', $or_n)
      ->orderBy('cashier_payment_other.payment_counter', 'ASC')
      ->get();

      // $walkin_payment_data = ViewOtherCollection::where('prefix_or_number', $or_n)
      // ->groupBy('description')
      // ->orderByRaw('payment_counter ASC')
      // ->get();

      return $walkin_payment_data;
    }

    public function getWalkinPaymentDataIndex() {
//       SELECT p.payment_id,
// p.created_at,
// p.prefix_or_number,
// p.patient_name,
// SUM(p.sub_total) AS total,
// p.payment_status,
// u.name,
// cd.discount_name
//
// FROM cashier_payment_other AS p
// LEFT JOIN cashier_users AS u
// ON u.id = p.id
// LEFT JOIN cashier_discount AS cd
// ON cd.id = p.discount_id
//
// GROUP BY p.prefix_or_number

      $walkin_payments = PaymentOther::leftjoin('cashier_users AS u', 'u.id', '=', 'cashier_payment_other.id')
      ->leftjoin('cashier_discount AS cd', 'cd.id', '=', 'cashier_payment_other.discount_id')
      ->select('cashier_payment_other.payment_id', 'cashier_payment_other.created_at', 'cashier_payment_other.prefix_or_number', 'cashier_payment_other.patient_name', 'cashier_payment_other.charge_slip_number',
      DB::raw('SUM(cashier_payment_other.sub_total) AS total'), 'cashier_payment_other.payment_status', 'u.name', 'cd.discount_name')
      ->whereNotNull('cashier_payment_other.charge_slip_number')
      ->groupBy('cashier_payment_other.prefix_or_number')
      ->orderBy('cashier_payment_other.created_at', 'DESC')
      ->limit(200)
      ->get();

      // $walkin_payments = ViewOtherCollection::select('prefix_or_number', 'or_number', 'receipt_date', 'patient_name', 'discount', 'amount_paid', 'name', 'payment_status', 'charge_slip_number')
      // ->distinct()
      // ->groupBy('prefix_or_number')
      // ->whereNotNull('charge_slip_number')
      // ->orderByRaw('created_at DESC')
      // ->get();

      $response = array('data' => $walkin_payments);

      return response()->json($response);
    }


    public function checkORDuplicate(Request $request) {
      $_token = $request->_token;
      $or_number = $request->or_number;
      $get_or_number_count = Payment::where('preorno', $or_number)->count();
      $get_other_or_count = PaymentOther::where('prefix_or_number', $or_number)->count();
      $total_count = $get_or_number_count + $get_other_or_count;
      // $arrData = $request->arrData;
      // $charge_slip_number = $request->charge_slip_number;

      // $getPaymentData = ViewCollection::where('or_number', $or_number)->count();
      // $getPaymentData = ViewOtherCollection::where('prefix_or_number', $or_number)->count();

      // $data = $getPaymentData;

      $data = $total_count;

      $response = array(
        'total_count' => $total_count,
        // 'data' => $data,
        // 'or_number' => $or_number,
        // '_token' => $_token,
        // 'arrData' => $arrData,
        // 'charge_slip_number' => $charge_slip_number,
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
        return redirect('/collections/walkin');
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
        return redirect('/collections/walkin');
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
        return redirect('/collections/walkin');
    }















}
