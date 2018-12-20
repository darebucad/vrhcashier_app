<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\PaymentOther;
use App\Setup;
use App\ViewPaymentORNumber;
use App\ViewDrugsMedicines;
use App\GenericDescription;
use App\ViewProducts;
use App\Patient;


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
        $payments = PaymentOther::select('*')
        ->limit(20)
        ->orderByRaw('created_at DESC')
        ->get();

    	return view('collections.other.index', compact('payments'));

    }


    /**
     * Show the form for creating a new other collection paymentm.
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

        if ( $payments > 1 ) {
            $payments = ViewPaymentORNumber::select('next_or_number', 'or_prefix')
                ->limit(1)
                ->get();
        }

        $patient_names = Patient::select('hpercode','patient_name')
            ->orderBy('patient_name', 'asc')
            ->limit(100)
            ->get();

        return view('collections.other.create', compact('payments', 'patient_names'));
    }



    /**
     * Display all products and services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showProducts(Request $request){
        $drugs = ViewProducts::select('item_code AS id', 'description AS text')->get();

        $response = array(
            'data'  =>  $drugs
        );
        return response()->json($response);
    }


    /**
     * Display latest price of selected drugs and medicine.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLatestPrice(Request $request) {
        $item_code = $request->id;
        $row_id = $request->row_id;
        $pad_length = 13;
        $pad_string = "0";
        $pad_output = str_pad($item_code, $pad_length, $pad_string, STR_PAD_LEFT);

        $drugs = ViewProducts::select('item_code', 'selling_price', 'charge_code', 'charge_table')
                    ->where('item_code', str_pad($item_code, $pad_length, $pad_string, STR_PAD_LEFT))
                    ->get();

        $response = array(
            'data' => $drugs,
            'id' => $item_code,
            'pad' => $pad_output,
            'row_id' => $row_id

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
        $json_data = $request->data;
        $counter = 0;

        foreach($json_data as $item) {
            $data = array(
                'prefix_or_number' => $item['prefix_or_number'],
                'or_number' => substr($item['prefix_or_number'], 2),
                'patient_name' => $item['patient_name'],
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
                'payment_status' => 'Paid',
                'amount_paid' => $item['amount_paid'],
                'amount_tendered' => $item['amount_tendered'],
                'amount_change' => $item['amount_change'],
                'created_at' => $item['created_at']

            );
            PaymentOther::insert($data);
            $counter = $counter + 1;
        }

        $response = array(
            'data' => $json_data
        );
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     //dd($request->all());
    //     $charge_slip = $request->pcchrgcod;
    //     $current_time = Carbon::now('Asia/Manila');

    //     $outpatient_charges_lab = ViewOutpatientChargeLab::where('pcchrgcod', $charge_slip);
    //     $outpatient_charges = ViewOutpatientCharge::where('pcchrgcod', $charge_slip)
    //         ->union($outpatient_charges_lab)
    //         ->get();

    //     foreach ($outpatient_charges as $charge){
    //         $category = $charge->orderfrom;

    //         $data = array(
    //             'enccode'               => $request->enccode,
    //             'hpercode'              => $request->hpercode,
    //             'acctno'                => $request->acctno,
    //             'orno'                  => $request->or_number,
    //             'ordate'                => date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $request->ordate))),
    //             'amt'                   => $charge->pcchrgamt,
    //             'curcode'               => $request->currency,
    //             'paytype'               => $request->payment_type,
    //             'paycode'               => $request->payment_mode,
    //             'paystat'               => $request->paystat,
    //             'paylock'               => $request->paylock,
    //             'updsw'                 => $request->updsw,
    //             'confdl'                => $request->confdl,
    //             'payctr'                => $request->payctr += 1,
    //             'pcchrgcod'             => $charge_slip,
    //             'chrgcode'              => $charge->orderfrom,
    //             'itemcode'              => $charge->product_code,
    //             'id'                    => $request->user_id,
    //             'discount_name'         => $charge->disc_name,
    //             'discount_percent'      => $charge->disc_percent,
    //             'discount_computation'  => $request->discount_computation,
    //             'discount_value'        => $charge->computed_discount,
    //             'status'                => $request->status,
    //             'amount_paid'           => $request->amount_paid,
    //             'amount_tendered'       => $request->amount_tendered,
    //             'change'                => $request->change,
    //             'created_at'            => $current_time->toDateTimeString()

    //         );
    //         Payment::insert($data);
    //     }


        // return redirect('/collections/outpatient/print/pdf', ['' => $charge_slip]);

        // $this->showPDF($charge_slip);

    //     return redirect('/collections/outpatient');
    // }


}
