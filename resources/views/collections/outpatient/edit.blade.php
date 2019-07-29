@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

    <h1 class="h5"><a href="{{ url('collections/outpatient') }}">Out-Patient Payment</a> / Edit (Status: {{ $data['data'][0]['payment_status'] }})</h1>

    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">


        @if ($data['data'][0]['payment_status'] === "Cancelled")
        <button class="btn btn-sm btn-outline-secondary" id ="btn_draft" style=@if(Auth::user()->is_admin === '0') display:none @endif>
          <span data-feather="align-justify"></span>
          Set to Draft
        </button>

        @elseif ($data['data'][0]['payment_status'] === "Draft")
        <button class="btn btn-sm btn-outline-primary" id ="btn_paid" style=@if(Auth::user()->is_admin === '0') display:none @endif>
          <span data-feather="check"></span>
          Mark as Paid
        </button>

        @else
        <button class="btn btn-sm btn-outline-secondary" id ="btn_print">
          <span data-feather="printer"></span>
          Print Receipt
        </button>
        <button class="btn btn-sm btn-outline-danger" id ="btn_cancel" style=@if(Auth::user()->is_admin === '0') display:none @endif>
          <span data-feather="x"></span>
          Cancel Payment
        </button>

        @endif

		  </div>
    </div>
  </div>

  <div class="alert alert-success alert-dismissable" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="alert_value">Payment was successfully saved.</span>
  </div>

  <style media="screen" type="text/css">
    .spinner{
      display:none;
      position: fixed; /* Stay in place */
      z-index: 1; /* Sit on top */
      left: 50%;
      top: 50%;
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      overflow: auto; /* Enable scroll if needed */
    }
  </style>

  <div id="spinner" class="spinner">
    <img id="img-spinner" src="{{ asset('ajax-loader.gif') }}" alt="Loading..."/>
    <h6>Please wait ...</h6>
  </div>

  <form id="collections_outpatient">
    @csrf

    <div class="row">
      <!-- New Button -->
      <button class="btn btn-sm btn-danger" id="btn_new" style="display: none; margin-right: 5px;">New</button>

      <!-- Save button -->
      <button class="btn btn-sm btn-danger" id="btn_edit" disabled>Edit</button>

      <!-- Cancel button -->
      <p id="button-cancel">or <a class="btn-link" href="{{ url('collections/outpatient') }}">Cancel</a></p>
    </div>
       <!-- <div class="col-md-4 offset-md-4">
        <a href = "{{ url('/collections/outpatient/print/pdf') }}" class="btn btn-sm btn-outline-danger">

          Print Receipt
        </a>
        <a href="{{ url('/collections/outpatient/print/pdf', ['' => 'P18-001028']) }}" class="btn btn-danger btn-sm">Print Receipt</a>
      </div> -->
    <br />

    <input type="hidden" name="paystat" id="paystat" value="">
    <input type="hidden" name="paylock" id="paylock" value="">
    <input type="hidden" name="updsw" id="updsw" value="">
    <input type="hidden" name="confdl" id="confdl" value="">
    <input type="hidden" name="payctr" id="payctr" value="">
    <input type="hidden" name="status" id="status" value="">
    <input type="hidden" name="user_id" id="user_id" value="">
    <input type="hidden" name="enccode" id="enccode" value="">
    <input type="hidden" name="hpercode" id="hpercode" value="">
    <input type="hidden" name="acctno" id="acctno" value="">
    <input type="hidden" name="pcchrgcod" id="pcchrgcod" value="">
    <input type="hidden" name="discount_percent_value" id="discount_percent_value" value="">
    <input type="hidden" name="charge_code" value="" id="charge_code">
    <input type="hidden" name="total_amount" value="" id="total_amount">
    <!-- <input type="hidden" name="charge_table" value="" id="charge_table"> -->

    <!-- Charge Details Row -->
    <div class="form-group row">
      <label for="search_barcode" class="col-md-1 col-form-label">Charge Details: </label>

      <div class="col-md-3">
        <!-- Search barcode input -->
        <!-- <input  type="text" name="search_barcode" id="search_barcode" class="form-control form-control-sm"  placeholder="Charge Slip / Barcode"  style="background-color:#99ccff!important;" required autofocus> -->
        <div class="input-group mb-3">
        <input type="text" name="search_barcode" id="search_barcode" class="form-control form-control-sm" placeholder="Charge Slip Number / Barcode" aria-label="Charge Slip Number / Barcode"
            aria-describedby="post_data" value="{{ $data['data'][0]['charge_slip_no'] }}" readonly>
        <div class="input-group-append">
        <!-- <button class="btn btn-success btn-sm" id="post_data">Search</button> -->
        </div>
        </div>
      </div>

      <div class="col-md-1">
        <!-- Search barcode button -->
        <!-- <button id="post_data" class="btn btn-outline-info pull-right btn-sm">Search</button> -->
      </div>

    </div>



    <!-- Patient Name Input-->
    <div id="patient_name_field" class="form-group row" style="margin-top: -20px;">
      <div class="input-group">
        <label for="patient_name" class="col-md-1 col-form-label text-md-left">{{ __('Patient Name') }}</label>
        <div class="col-md-9">
          <input type="text" name="patient_name" id="patient_name" value="{{ $data['data'][0]['patient_name'] }}" class="form-control form-control-sm" readonly>
        </div>
      </div>
    </div>

    <!-- OR Date / OR Number Input -->
    <div class="form-group row" style="margin-top: -10px">
      <label for="or_date" class="col-md-1 col-form-label text-md-left">{{ __('OR Date') }}</label>
      <div class="col-md-2">
        <div class="input-group mb-3">
          <input id="or_dates" type="text" class="form-control form-control-sm" name="or_date" value="{{ $data['data'][0]['date'] }}" readonly>
            <!-- <span data-feather="calender"></span> -->
        </div>
      </div>

      <label for="or_number" class="col-md-1 col-form-label text-md-left">{{ __('OR Number') }}</label>

      <div class="col-md-3">

        <input id="or_no" type="text" class="form-control form-control-sm" name="or_number" value="{{ $data['data'][0]['or_no_prefix'] }}" readonly>
        <input type="hidden" name="or_number_only" value="">

        @if ($errors->has('or_number'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('or_number') }}</strong>
          </span>
        @endif
      </div>

      <label class="col-md-1 col-form-label text-md-left"> User: </label>
      <label class="col-md-1 col-form-label text-md-left">{{ $data['data'][0]['employee_name'] }}</label>

      <div class="col-md-1 offset-md-1">
        <button type="button" name="update_totals" id="update_totals" class="btn btn-outline-dark btn-sm" disabled>
          Update Totals
        </button>
      </div>

    </div>


    <!-- Mode/Type of payment Control -->
    <div class="form-group row">
      <label for="payment_mode" class="col-md-2 col-form-label text-md-left">{{ __('Mode of Payment') }}</label>
      <div class="col-md-2">
        <select id="payment_mode" class="form-control form-control-sm" name="payment_mode" readonly disabled>
          <option value="{{ $data['data'][0]['paycode'] }}">{{ $data['data'][0]['payment_mode'] }}</option>
        </select>
      </div>

      <label for="discount_percent" class="col-md-1 col-form-label text-md-left">{{ __('Discount (%)') }}</label>

      <div class="col-md-3">
        <select  id="discount_percent" class="form-control form-control-sm" name="discount_percent" readonly disabled>
          <option value="{{ $data['data'][0]['discount_name'] }}">{{ $data['data'][0]['discount'] }}</option>
        </select>
      </div>


      <label for="amount_paid" class="col-md-2 col-form-label text-md-right">{{ __('Total Amount') }}</label>
      <div class="col-md-2">
        <input id="amount_paid" type="text" class="form-control" name="amount_paid" value="{{ $data['data'][0]['amount_paid'] }}" style="font-weight: bold; font-size: 25px;" readonly>
        @if ($errors->has('amount_paid'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('amount_paid') }}</strong>
            </span>
        @endif
      </div>
    </div>

    <div class="form-group row" style="margin-top: -10px">
      <label for="payment_type" class="col-md-2 col-form-label text-md-left">{{ __('Type of Payment') }}</label>
      <div class="col-md-2">
        <select id="payment_type" class="form-control form-control-sm" name="payment_type" readonly disabled>
          <option value="{{ $data['data'][0]['paytype'] }}">{{ $data['data'][0]['payment_type'] }}</option>
        </select>
      </div>

      <label for="discount_computation" class="col-md-2 col-form-label text-md-left">{{ __('Discount Computation') }}</label>
      <div class="col-md-2">
        <select name="discount_computation" id="discount_computation" class="form-control form-control-sm" readonly disabled>
          <option value="{{ $data['data'][0]['discount_computation'] }}">{{ $data['data'][0]['discount_computation'] }}</option>
        </select>
        @if ($errors->has('discount_computation'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('discount_computation') }}</strong>
            </span>
        @endif
      </div>

      <label for="amount_tendered" class="col-md-2 col-form-label text-md-right">Amount tendered</label>

      <div class="col-md-2">
        <input id="amount_tendered" type="text" class="form-control" name="amount_tendered"  style="font-weight: bold; font-size: 25px;" value="{{ $data['data'][0]['amount_tendered'] }}" readonly>

        @if ($errors->has('amount_tendered'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('amount_tendered') }}</strong>
            </span>
        @endif

      </div>
    </div>

    <!-- Currency Row -->
    <div class="form-group row" style="margin-top: -10px">
      <label for="currency" class="col-md-2 col-form-label text-md-left">{{ __('Currency') }}</label>
      <div class="col-md-2">
        <select id="currency" class="form-control form-control-sm" name="currency" readonly disabled>
          <option value="{{ $data['data'][0]['curcode'] }}">{{ $data['data'][0]['currency'] }}</option>
        </select>
      </div>

      <div class="col-md-1 offset-md-1">
        <button type="button" id="apply_discount_all" class="btn btn-success btn-sm" disabled>
          Apply to all
        </button>
      </div>

      <div class="col-md-1">
        <button type="button" id="apply_discount_selected" class="btn btn-success btn-sm" disabled>
          Apply to Selected
        </button>
      </div>

      <div class="col-md-1">
        <button type="button" name="clear_discount" id="clear_discount" class="btn btn-outline-secondary btn-sm" disabled>
          Clear Discount
        </button>
      </div>

      <label for="amount_change" class="col-md-2 col-form-label text-md-right" style="font-size: 13px">{{ __('Change') }}</label>

      <div class="col-md-2">
        <input id="amount_change" type="text" class="form-control" name="amount_change" style="font-weight: bold; font-size: 25px;" value="{{ $data['data'][0]['amount_change'] }}" readonly>
        @if ($errors->has('amount_change'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('amount_change') }}</strong>
            </span>
        @endif
      </div>
    </div>

</form>

  <div class="table-responsive">
    <table id="invoice_table" class="table table-sm table-striped" style="width: 100%">
      <thead>
        <tr>
          <th>Pay?</th>
          <th>Disc?</th>
          <th>Date</th>
          <th>Charge Slip Ref</th>
          <th>Description</th>
          <th style="text-align: right;">QTY</th>
          <th style="text-align: right;">Unit Cost</th>
          <th style="width:8%; text-align: right;">Discount (%)</th>
          <th style="width:10%; text-align: right;">Discount Value</th>
          <th style="width:10%; text-align: right;">Sub-total</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="charge-info">
        @foreach ($payment_details as $payment)
        <tr id="{{ $payment->payment_counter }}" class="charge_items">
            <td><input type="checkbox" name="pay_checkbox" class="pay_checkbox" value="" {{ $payment->is_pay }} disabled></td>
            <td><input type="checkbox" name="discount_checkbox" class="discount_checkbox" value="" {{ $payment->is_discount }} disabled></td>
            <td>{{ $payment->or_date }}</td>
            <td class="charge_slip_number">{{ $payment->charge_slip_no }}</td>
            <td class="product">{{ $payment->description }}</td>
            <td align="right" class="quantity">{{ $payment->quantity }}</td>
            <td align="right" class="unit_price">{{ $payment->unit_price }}</td>
            <td align="right" style="width:8%" class="discount_percent">{{ $payment->discount_percent }}</td>
            <td align="right" style="width:10%" class="editMe discount_value">{{ $payment->computed_discount }}</td>
            <td align="right" style="width:10%" class="sub_total">{{ $payment->computed_sub_total }}</td>
            <td>Invoiced</td>
        </tr>

        @endforeach

      </tbody>
    </table>
  </div>
</main>

<script type="text/javascript">

$(document).ready(function(){
    var data = $('#or_no').val();


    $('#btn_print').on('click', function () {
      // var row = $(this).closest('tr');
      // var data = table.row( row ).data().or_no_prefix;
      // console.log(data);
      window.location.href = '/collections/outpatient/print/pdf/' + data;
    });


    $('#btn_cancel').on('click', function(){
      // var row = $(this).closest('tr');
      // var or_number = table.row(row).data().or_no_prefix;
      // var user_id = $('#user_id').val();
      var q = confirm('Are you sure you want to cancel this payment ?');

      if (q == true) {
        // console.log(or_number);
        window.location.href = '/collections/outpatient/cancel-payment/' + data;
      }
    });

    $('#btn_draft').on('click', function(){
      // var row = $(this).closest('tr');
      // var or_number = table.row(row).data().or_no_prefix;
      // var user_id = $('#user_id').val();

      // console.log(or_number);
      window.location.href = '/collections/outpatient/draft-payment/' + data;
    });

    $('#btn_paid').on('click', function() {
      // var row = $(this).closest('tr');
      // var or_number = table.row(row).data().or_no_prefix;
      // var user_id = $('#user_id').val();
      var q = confirm('Are you sure you want to mark this payment as paid ?');

      if (q == true) {
        // console.log(or_number);
        window.location.href = '/collections/outpatient/mark-paid/' + data;
      }
    });
});

</script>

@endsection
