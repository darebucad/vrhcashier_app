@extends ('layouts.master')

@section ('content')
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <!-- <h1 class="h4">Create Collections Inpatient</h1> -->
    <h1 class="h5"><a href="{{ url('collections/inpatient') }}">In-Patient Payment</a> / Create</h1>
    @if (session('status'))
      <div class="alert alert-success" role="alert">
        {{ session('status') }}
      </div>
    @endif
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
		    <button class="btn btn-sm btn-outline-secondary" id ="btn_print" style="display: none;">
					<span data-feather="printer"></span>
					Print Receipt
				</button>
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

  <form id="collections_inpatient">
    @csrf

    <div class="row" style="margin-top: -5px;">
      <!-- New Button -->
      <button class="btn btn-sm btn-danger" id="btn_new" style="display: none; margin-right: 5px;">New</button>

      <!-- Save button -->
      <button type="submit" class="btn btn-sm btn-primary" id="btn_save">Save</button>

      <!-- Cancel button -->
      <p id="button-cancel">or <a class="btn-link" href="{{ url('collections/inpatient') }}">Cancel</a></p>
    </div>

    <br />

    <!-- Inpatient Collection values -->
    <input type="hidden" name="paystat" id="paystat" value="">
    <input type="hidden" name="paylock" id="paylock" value="">
    <input type="hidden" name="updsw" id="updsw" value="">
    <input type="hidden" name="confdl" id="confdl" value="">
    <input type="hidden" name="payctr" id="payctr" value="">
    <input type="hidden" name="status" id="status" value="">
    <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
    <input type="hidden" name="encounter_code" id="encounter_code" value="">
    <input type="hidden" name="patient_id" id="patient_id" value="">
    <input type="hidden" name="account_number" id="account_number" value="">
    <input type="hidden" name="billing_id" value="" id="billing_id">

    <!-- SOA Details Row -->
    <div class="form-group row">
      <label for="soa_number" class="col-md-1 offset-md-1 col-form-label">SOA Details: </label>

      <div class="col-md-3">
        <!-- SOA Number input -->
        <div class="input-group mb-3">
          <input type="text" name="soa_number" id="soa_number" class="form-control form-control-sm" placeholder="SOA Number / Barcode" aria-label="SOA Number / Barcode" aria-describedby="post_data" style="background-color:#99ccff!important;" required autofocus>
          <div class="input-group-append">
            <button class="btn btn-success btn-sm" id="search_soa">Search</button>
          </div>
        </div>
      </div>
    </div>


    <!-- Patient Name Input-->
    <div id="patient_name_field"class="form-group row" style="margin-top: -20px;">
      <div class="input-group">
        <label for="patient_name" class="col-md-1 offset-md-1 col-form-label text-md-left">{{ __('Patient Name') }}</label>
        <div class="col-md-8">
          <input type="text" name="patient_name" id="patient_name" class="form-control" style="background-color:#99ccff!important;" placeholder="Patient Name" required>
        </div>
      </div>
    </div>

    <!-- OR Date / OR Number Input -->
    <div class="form-group row" style="margin-top: -5px;">
      <label for="or_date" class="col-md-1 offset-md-1 col-form-label text-md-left">{{ __('O.R. Date') }}</label>

      <div class="col-md-1">
        <div class="input-group mb-3">
          <input id="or_date" type="text" class="form-control form-control-sm" name="or_date" value="{{ $now = date('m/d/Y') }}" style="background-color:#99ccff!important;" required>
            <!-- <span data-feather="calender"></span> -->
        </div>
      </div>

      <label for="prefix_or_number" class="col-md-1 col-form-label text-md-right">{{ __('O.R. Number') }}</label>

      <div class="col-md-1">
        @if (count($payments) > 0)
          @foreach ($payments as $payment)
            <input type="text" name="prefix_or_number" value="{{ $payment->or_prefix . $payment->next_or_number }}" id="prefix_or_number" class="form-control form-control-sm" required>
            <input type="hidden" name="or_number" value="{{ $payment->next_or_number }}">
          @endforeach
        @else
          <input type="text" name="prefix_or_number" value="{{ '0000001' }}" id="prefix_or_number" class="form-control form-control-sm" required>
          <input type="text" name="or_number" value="{{ '0000001' }}">
        @endif

        @if ($errors->has('or_number'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('or_number') }}</strong>
          </span>
        @endif
      </div>

      <label class="col-md-1 col-form-label text-md-right"> User: </label>
      <label class="col-md-2 col-form-label text-md-left">{{ Auth::user()->name }}</label>
      <!-- <label class="col-md-1 col-form-label text-md-left">Mark Julius G. Bucad</label> -->

      <div class="col-md-1">
        <button type="button" name="update_totals" id="update_totals" class="btn btn-success btn-sm">
          Update Totals
        </button>
      </div>

      <div class="col-md-1">
        <button type="button" name="clear_computation" id="clear_computation" class="btn btn-dark btn-sm">
          Clear Computation
        </button>
      </div>
    </div>


    <!-- Mode/Type of payment Control -->
    <div class="form-group row" style="margin-top: -10px;">
      <label for="payment_mode" class="col-md-2 offset-md-1 col-form-label text-md-left">{{ __('Mode of Payment') }}</label>

      <div class="col-md-3">
        <select id="payment_mode" class="form-control form-control-sm" name="payment_mode">
          <option value=""> </option>
          <option value="C" selected>Cash</option>
          <option value="X">Check</option>
        </select>
      </div>

      <label for="total_amount" class="col-md-2 col-form-label text-md-right">{{ __('Total Amount:') }}</label>

      <div class="col-md-2">
        <input id="total_amount" type="text" class="form-control" name="total_amount" style="background-color:#99ccff!important; font-weight: bold; font-size: 25px;" value="0.00">
        @if ($errors->has('total_amount'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('total_amount') }}</strong>
            </span>
        @endif
      </div>
    </div>

    <!-- Type of payment row -->
    <div class="form-group row" style="margin-top: -10px">
      <label for="payment_type" class="col-md-2 offset-md-1 col-form-label text-md-left">{{ __('Type of Payment') }}</label>

      <div class="col-md-3">
        <select id="payment_type" class="form-control form-control-sm" name="payment_type">
          <option value=""> </option>
          <option value="A">Additional Deposit</option>
          <option value="D">Donation</option>
          <option value="F" selected>Full Payment</option>
          <option value="I">Initial Deposit</option>
          <option value="P">Partial Payment</option>
        </select>
      </div>

      <label for="amount_tendered" class="col-md-2 col-form-label text-md-right">Amount tendered:</label>

      <div class="col-md-2">
        <input id="amount_tendered" type="text" onBlur="computeChange()" class="form-control" name="amount_tendered"  style="background-color:#99ccff!important; font-weight: bold; font-size: 25px;" value="0.00" autofocus>

        @if ($errors->has('amount_tendered'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('amount_tendered') }}</strong>
            </span>
        @endif

      </div>
    </div>

    <!-- Currency Row -->
    <div class="form-group row" style="margin-top: -10px">
      <label for="currency" class="col-md-2 offset-md-1 col-form-label text-md-left">{{ __('Currency') }}</label>

      <div class="col-md-3">
        <select id="currency" class="form-control form-control-sm" name="currency">
          <option value=""> </option>
          <option value="DOLLA">Dollars</option>
          <option value="OTHER">Others</option>
          <option value="PESO" selected>Php</option>
          <option value="YEN">Yen</option>
        </select>
      </div>

      <label for="amount_change" class="col-md-2 col-form-label text-md-right" style="font-size: 13px">{{ __('Change:') }}</label>

      <div class="col-md-2">
        <input id="amount_change" type="text" class="form-control" name="amount_change" style="font-weight: bold; font-size: 25px;" value="0.00" readonly>
        @if ($errors->has('amount_change'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('amount_change') }}</strong>
            </span>
        @endif
      </div>
    </div>

    <div class="form-group row" style="margin-top: -10px;">

      <div class="col-md-2 offset-md-1">
          <label for="advance_payment" class="col-form-label">
              Advance Payment
          </label>
      </div>

      <div class="col-md-2">
          <input type="checkbox" name="advance_payment" id="advance_payment" class="form-check-input" checked>
      </div>



      <label for="ending_balance" class="col-md-1 offset-md-2 col-form-label text-md-right">Ending Balance:</label>
      <div class="col-md-2">
        <input type="text" name="ending_balance" value="0.00" id="ending_balance" class="form-control text-danger" style="font-weight: bold; font-size: 25px;" readonly>
        @if ($errors->has('ending_balance'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('ending_balance') }}</strong>
            </span>
        @endif
      </div>
    </div>

    <!-- <div class="form-group row" style="margin-top: -20px;">
        <div class="col-md-2 offset-md-1">
            <label for="pay_patient" class="col-form-label">
                Pay Patient
            </label>
        </div>
        <div class="col-md-2">
            <input type="checkbox" name="pay_patient" id="pay_patient" class="form-check-input">
        </div>
    </div> -->

</form>

<div class="form-group row" style="margin-top: -10px;">
  <div class="col-md-2 offset-md-1">
    <h1 class="h5 text-danger">Breakdown of Charges: </h1>
  </div>

</div>

<div class="row" style="margin-top: -20px;">
  <div class="col-md-7 offset-md-1">
    <!-- Table -->
    <div class="table-responsive">
      <table id="charge_table" class="table table-sm table-striped" style="width: 100%">
        <thead>
          <tr>
            <th>Charge Description</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody id="charge-info">

          <tr>
            <td class="text-danger">Professional Fees</td>
            <td><input type="text" name="profees_total" value="0.00" id="profees_total" class="form-control form-control-sm text-danger" style="text-align:right; width: 50%;"></td>
          </tr>

          <tr id="ACCOM" class="charge_items">
            <td class="category">Room and Board</td>
            <td><input type="text" name="room_total" value="0.00" id="room_total" class="form-control form-control-sm charge" style="text-align:right; width: 50%;"></td>
          </tr>

          <tr id="DRUME" class="charge_items">
            <td class="category">Drugs and Medicines</td>
            <td><input type="text" name="drugs_total" value="0.00" id="drugs_total" class="form-control form-control-sm charge" style="text-align:right; width: 50%;"></td>
          </tr>

          <tr id="DRUMN" class="charge_items">
            <td class="category">Medical Supplies</td>
            <td><input type="text" name="supplies_total" value="0.00" id="supplies_total" class="form-control form-control-sm charge" style="text-align:right; width: 50%;"></td>
          </tr>

          <tr id="LABOR" class="charge_items">
            <td  class="category">Laboratory</td>
            <td><input type="text" name="lab_total" value="0.00" id="lab_total" class="form-control form-control-sm charge" style="text-align:right; width: 50%;"></td>
          </tr>

          <tr id="RADIO" class="charge_items">
            <td class="category">Radiology</td>
            <td><input type="text" name="rad_total" value="0.00" id="rad_total" class="form-control form-control-sm charge" style="text-align:right; width: 50%;"></td>
          </tr>

          <tr id="OPERA" class="charge_items">
            <td  class="category">OR/DR Fee</td>
            <td><input type="text" name="ordr_total" value="0.00" id="ordr_total" class="form-control form-control-sm charge" style="text-align:right; width: 50%;"></td>
          </tr>

          <tr id="OXY" class="charge_items">
            <td class="category">Oxygen</td>
            <td><input type="text" name="oxygen_total" value="0.00" id="oxygen_total" class="form-control form-control-sm charge" style="text-align:right; width: 50%;"></td>
          </tr>

          <tr id="OTHER" class="charge_items">
            <td class="category">Others</td>
            <td><input type="text" name="others_total" value="0.00" id="others_total" class="form-control form-control-sm charge" style="text-align:right; width: 50%;"></td>
          </tr>

        </tbody>
      </table>
    </div>
  </div>

  <div class="col-md-2">
    <!-- <div class="row">
        <label for="advance_payment" class="col-form-label col-md-12">
          <input type="checkbox" name="advance_payment" id="advance_payment" class="form-check-input" checked> <strong>Advance Payment</strong>
        </label>
    </div> -->

    <div class="row">
      <label for="total_charges" class="form-control-label">Total Charges:</label>
      <input type="text" name="total_charges" value="0.00" id="total_charges" class="form-control" style="font-weight: bold; font-size: 25px;" readonly>
    </div>

    <div class="row">
      <label for="less_payments" class="form-control-label">Less (Advance Payments):</label>
      <input type="text" name="less_payments" value="0.00" id="less_payments" class="form-control" style="font-weight: bold; font-size: 25px;" readonly>
    </div>

    <div class="row">
      <label for="amount_due" class="col-form-label">Amount Due:</label>
      <input type="text" name="amount_due" value="0.00" id="amount_due" class="form-control text-danger" style="font-weight: bold; font-size: 25px;" readonly>
    </div>

    <div class="row">
      <!-- <div class="form-group row" style="margin-top:-20px;"> -->
        <label for="remarks" class="col-form-label">Remarks:</label>
      <!-- </div> -->

      <!-- <div class="form-group row" style="margin-top:-20px;">
        <div class="col-md-9 offset-md-1"> -->
          <textarea name="remarks" rows="7" cols="80" class="form-control form-control-sm" id="remarks"></textarea>
        <!-- </div> -->
      </div>
    </div>
  </div>
</main>

<script>
  // Binds to the global ajax scope
  $(document).ajaxStart(function() {
    $( "#spinner" ).show();
  });
  $(document).ajaxComplete(function() {
    $( "#spinner" ).hide();
  });

  $(document).ready(function(){
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var charge_array = '';


    $('#search_soa').on('click', function(event){
      event.preventDefault();

      var _token = CSRF_TOKEN;
      var soa_number = $('#soa_number').val();

      if (soa_number == '' || soa_number == 0) {
        alert('Please input SOA Number / Barcode .');

      } else {
        searchSoa(_token, soa_number);

      }
    });

    function searchSoa(_t, soa) {
      $.ajax({
        type: "POST",
        url:"/collections/inpatient/create/search-soa",
        data: { _token: _t, soa_number: soa },
        dataType: "JSON",
        success: function(data) {
          var bill_total = data.bill_total;
          var patient_bill = data.patient_bill;
          var charge_sequence = data.charge_sequence;
          var profees = data.bill_breakdown['profees'];
          var room = data.bill_breakdown['room'];
          var drugs = data.bill_breakdown['drugs'];
          var supplies = data.bill_breakdown['supplies'];
          var lab = data.bill_breakdown['lab'];
          var rad = data.bill_breakdown['rad'];
          var or = data.bill_breakdown['or'];
          var oxygen = data.bill_breakdown['oxygen'];
          var misc = data.bill_breakdown['misc'];

          var billing_id = patient_bill['billing_id'];
          var encounter_code = patient_bill['enccode'];
          var soa_number = patient_bill['billing_num'];
          var patient_id = patient_bill['hpercode'];
          var last_name = patient_bill['patlast'];
          var first_name = patient_bill['patfirst'];
          var middle_name = patient_bill['patmiddle'];
          var suffix_name = patient_bill['patsuffix'];
          var account_number = patient_bill['paacctno'];
          var patient_name = '';

          var profees_total = 0;
          var room_total = 0;
          var drugs_total = 0;
          var supplies_total = 0;
          var lab_total = 0;
          var rad_total = 0;
          var or_total = 0;
          var oxygen_total = 0;
          var others_total = 0;

          var drugs_adv_pay = 0;
          var supplies_adv_pay = 0;
          var lab_adv_pay = 0;
          var rad_adv_pay = 0;
          var or_adv_pay = 0;
          var oxygen_adv_pay = 0;
          var others_adv_pay = 0;

          var total_charges = 0;
          var charge = 0;
          var default_value = 0;
          var advance_payment = 0;
          var amount_due = 0;

          if (suffix_name == null) {
            suffix_name = ''
          } else {
            // suffix_name = suffix_name;
          }

          patient_name = last_name + ', ' + first_name + ' ' + suffix_name  + ' ' + middle_name;
          charge_array = charge_sequence;

          $('#billing_id').val(billing_id);
          $('#encounter_code').val(encounter_code);
          $('#soa_number').val(soa_number);
          $('#patient_id').val(patient_id);
          $('#patient_name').val(patient_name);
          $('#account_number').val(account_number);
          $('#paystat').val('A');
          $('#paylock').val('N');
          $('#updsw').val('N');
          $('#confdl').val('N');
          $('#payctr').val('0');
          $('#status').val('Paid');

          if (profees == null || profees == 0) {
            profees_total = default_value;
          } else {
            profees_total = profees['total'];
          }

          if (room == null || room == 0) {
            room_total = default_value
          } else {
            room_total = room['total'];
          }

          if (drugs == null || drugs == 0) {
            drugs_total = default_value;
          } else {
            drugs_total = drugs['total'];
            drugs_adv_pay = drugs['adv_pay'];
          }

          if (supplies == null || supplies == 0) {
            supplies_total = default_value;
          } else {
            supplies_total = supplies['total'];
            supplies_adv_pay = supplies['adv_pay'];
          }

          if (lab == null || lab == 0) {
            lab_total = default_value;
          } else {
            lab_total = lab['total'];
            lab_adv_pay = lab['adv_pay'];
          }

          if (rad == null || rad == 0) {
            rad_total = default_value;
          } else {
            rad_total = rad['total'];
            rad_adv_pay = rad['adv_pay'];
          }

          if (or == null || or == 0) {
            or_total = default_value;
          } else {
            or_total = or['total'];
            or_adv_pay = or['adv_pay'];
          }

          if (oxygen == null || oxygen == 0) {
            oxygen_total = default_value;
          } else {
            oxygen_total = oxygen['total'];
            oxygen_adv_pay = oxygen['adv_pay'];
          }

          if (misc == null || misc == 0) {
            others_total = default_value;
          } else {
            others_total = misc['total'];
            others_adv_pay = misc['adv_pay'];
          }

          total_charges = Number(room_total)
            + Number(drugs_total)
            + Number(supplies_total)
            + Number(lab_total)
            + Number(rad_total)
            + Number(or_total)
            + Number(oxygen_total)
            + Number(others_total);

          advance_payment = Number(drugs_adv_pay)
            + Number(supplies_adv_pay)
            + Number(lab_adv_pay)
            + Number(rad_adv_pay)
            + Number(or_adv_pay)
            + Number(oxygen_adv_pay)
            + Number(others_adv_pay);

          $('#profees_total').val(formatNumber(profees_total));
          $('#room_total').val(formatNumber(room_total));
          $('#drugs_total').val(formatNumber(drugs_total));
          $('#supplies_total').val(formatNumber(supplies_total));
          $('#lab_total').val(formatNumber(lab_total));
          $('#rad_total').val(formatNumber(rad_total));
          $('#ordr_total').val(formatNumber(or_total));
          $('#oxygen_total').val(formatNumber(oxygen_total));
          $('#others_total').val(formatNumber(others_total));

          amount_due =  Number(total_charges - advance_payment);

          // console.log(total_charges);
          // console.log(advance_payment);
          // console.log(amount_due);

          // console.log(total_charges);
          // console.log(advance_payment);
          // console.log(amount_due);

          $('#total_amount').val(formatNumber(amount_due));
          $('#ending_balance').val(formatNumber(amount_due))
          $('#total_charges').val(formatNumber(total_charges));
          $('#less_payments').val(formatNumber(advance_payment));
          $('#amount_due').val(formatNumber(amount_due));
        }
      });
    }

    function getPatientCharges(_t, b_id, ecode, snumber, pid, an) {
      var billing_id = b_id;
      var encounter_code = ecode;
      var soa_number = snumber;
      var patient_id = pid;
      var account_number = an;

      $('#billing_id').val(billing_id);
      $('#encounter_code').val(encounter_code);
      $('#soa_number').val(soa_number);
      $('#patient_id').val(patient_id);
      $('#account_number').val(account_number);
      $('#paystat').val('A');
      $('#paylock').val('N');
      $('#updsw').val('N');
      $('#confdl').val('N');
      $('#payctr').val('0');
      $('#status').val('Paid');

      $.ajax({
        type: "GET",
        url:"/collections/inpatient/create/get-patient-charges",
        data: { _token: _t, billing_id: b_id },
        dataType: "JSON",
        success: function(data) {
          // console.log(data.data);
          // console.log(data.charge_sequence);

          charge_array = data.charge_sequence;

          console.log(charge_array);
          var drugs_total = 0;
          var lab_total = 0;
          var misc_total = 0;
          var ordr_total = 0;
          var others_total = 0;
          var oxygen_total = 0;
          var profees_total = 0;
          var rad_total = 0;
          var room_total = 0;
          var supplies_total = 0;
          var total_charges = 0;
          var charge = 0;
          var default_value = 0;
          var advance_payment = 0;
          var amount_due = 0;

          $.each(data.data, function(i, value) {
            default_value = Number(0).toFixed(2);
            drugs_total = value.drugs_total;
            lab_total = value.lab_total;
            misc_total = value.misc_total;
            ordr_total = value.ordr_total;
            others_total = value.others_total;
            oxygen_total = value.oxygen_total;
            profees_total = value.profees_total;
            rad_total = value.rad_total;
            room_total = value.room_total;
            supplies_total = value.supplies_total;
            advance_payment = value.advance_payment;

            if (drugs_total == null || drugs_total == 0) {
              drugs_total = default_value;
            }
            if (lab_total == null || lab_total == 0) {
              lab_total = default_value;
            }
            if (misc_total == null || misc_total == 0) {
              misc_total = default_value;
            }
            if (ordr_total == null || ordr_total == 0) {
              ordr_total = default_value;
            }
            if (others_total == null || others_total == 0) {
              others_total = default_value;
            }
            if (oxygen_total == null || oxygen_total == 0) {
              oxygen_total = default_value;
            }
            if (profees_total == null || profees_total == 0) {
              profees_total = default_value;
            }
            if (rad_total == null || rad_total == 0) {
              rad_total = default_value;
            }
            if (room_total == null || room_total == 0 ) {
              room_total = default_value;
            }
            if (supplies_total == null || supplies_total == 0) {
              supplies_total = default_value;
            }
            if (advance_payment == null || advance_payment == 0) {
              advance_payment = default_value;
            }

            $('#drugs_total').val(formatNumber(drugs_total));
            $('#lab_total').val(formatNumber(lab_total));
            $('#misc_total').val(formatNumber(misc_total));
            $('#ordr_total').val(formatNumber(ordr_total));
            $('#others_total').val(formatNumber(others_total));
            $('#oxygen_total').val(formatNumber(oxygen_total));
            $('#profees_total').val(formatNumber(profees_total));
            $('#rad_total').val(formatNumber(rad_total));
            $('#room_total').val(formatNumber(room_total));
            $('#supplies_total').val(formatNumber(supplies_total));
          });

          $('#charge-info > tr').each(function(){
            var nc_value = 0;
            var num_value = 0;

            nc_value = removeComma($(this).find('input').val());
            num_value = Number(nc_value);
            total_charges += num_value;
          });

          // alert(advance_payment);
          amount_due =  total_charges - advance_payment;
          // console.log(total);
          $('#total_amount').val(formatNumber(amount_due.toFixed(2)));
          $('#ending_balance').val(formatNumber(amount_due.toFixed(2)))
          $('#total_charges').val(formatNumber(total_charges.toFixed(2)));
          $('#less_payments').val(formatNumber(Number(advance_payment).toFixed(2)));
          $('#amount_due').val(formatNumber(amount_due.toFixed(2)));
        }
      });
    }


    function formatNumber(num) {
      var n = Number(num).toFixed(2);
      var for_num = n.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');

      return for_num;
    }

    function removeComma(num) {
      var val = "";

      val = num.replace(/,/g, '');

      return val;
    }


    function numDecimals(num) {
      var num_dec = Number(num).toFixed(2);

      return num_dec;
    }


    $('#update_totals').on('click', function(event){
      event.preventDefault();

      var total_amount = removeComma($('#total_amount').val());
      var amount_due = removeComma($('#amount_due').val());
      var dec_amount = Number(total_amount);
      var q = confirm('Are you sure you want to update the totals ?');

      // console.log(total_amount);
      // console.log(amount_due);
      // console.log(dec_amount);

      // if (total_amount < 1000) {
      //   //
      // } else {
      //   total_amount = removeComma($('#total_amount').val());
      // }
      //
      // if (amount_due < 1000) {
      //   //
      // } else {
      //   amount_due = removeComma($('#amount_due').val());
      // }

      // var total_amount = removeComma($('#total_amount').val());
      // var amount_due = removeComma($('#amount_due').val());
      // var pay_patient = 0;

      // if ($('#pay_patient').is(':checked')) {
      //   pay_patient = 1;
      //
      // }

      // console.log(pay_patient);

      if (q == true) {
        $('#total_amount').val(formatNumber(total_amount));

        if (Number(total_amount) > Number(amount_due)) {
          alert('The total amount is greater than total patient charges .');
          $('#total_amount').val(formatNumber(0));
          $('#total_amount').focus();

        } else {
          updateTotals(total_amount, amount_due, dec_amount);
        }
      } else {
        //
      }
    });

    function updateTotals(t_amount, amount_d, de_amount) {
      var nc_value = 0;
      var num_value = 0;
      var total_charges = 0;
      var total_amount = t_amount;
      var amount_due = amount_d;
      var dec_amount = de_amount;
      var new_balance = 0;
      var arrData = [];

      // var drugs_total = $('#drugs_total').val();
      // var lab_total = $('#lab_total').val();
      // var ordr_total = $('#ordr_total').val();
      // var others_total = $('#others_total').val();
      // var oxygen_total = $('#oxygen_total').val();
      // var rad_total = $('#rad_total').val();
      // var room_total = $('#room_total').val();
      // var supplies_total = $('#supplies_total').val();
      //
      // var ending_balance = $('#ending_balance').val();
      // var less_payments = $('#less_payments').val();

      var drugs_total = removeComma($('#drugs_total').val());
      var lab_total = removeComma($('#lab_total').val());
      var ordr_total = removeComma($('#ordr_total').val());
      var others_total = removeComma($('#others_total').val());
      var oxygen_total = removeComma($('#oxygen_total').val());
      var rad_total = removeComma($('#rad_total').val());
      var room_total = removeComma($('#room_total').val());
      var supplies_total = removeComma($('#supplies_total').val());

      var ending_balance = removeComma($('#ending_balance').val());
      var less_payments = removeComma($('#less_payments').val());

      // Loop charge array
      $.each(charge_array, function(i, value){
        var sequence_number = value.sequenceNo;
        var charge_code = value.chrgcode;

          // Table of hospital charges
          $('.charge_items').each(function(){
            var row = $(this);
            var row_id = row.closest('tr').attr('id');
            var charge_value = removeComma(row.closest('tr').find('.charge').val());

            // console.log(row_id);
            if (row_id == charge_code) {
              if (dec_amount > 0) {
                if (charge_value > 0) {
                  if (dec_amount > charge_value) {
                    row.closest('tr').find('.charge').val(formatNumber(charge_value));
                    dec_amount = dec_amount - charge_value;

                  } else {
                      row.closest('tr').find('.charge').val(formatNumber(dec_amount));
                      dec_amount = 0;
                  }
                }
                else {
                  row.closest('tr').find('.charge').val(numDecimals(0));
                }

              } else {
                row.closest('tr').find('.charge').val(numDecimals(0));
              }
            }
          });
      });

      $('.charge_items').each(function(){
        var row = $(this);
        var total = Number(removeComma(row.closest('tr').find('.charge').val()));
        if (total == 0) {
          row.closest('tr').find('.charge').val(numDecimals(0));
        } else {
          // row.closest('tr').find('.charge').val(Number(total.toFixed(2))
        }
        // console.log(row);
      });


      new_balance = ending_balance - total_amount;

      // console.log(total_amount);
      // console.log(ending_balance);
      // console.log(new_balance);

      $('#ending_balance').val(formatNumber(new_balance));

    }


    // Button clear computation
    $('#clear_computation').on('click', function(e){
      var q = confirm('Are you sure you want to clear the computation ?');

      if (q == true) {

        return $('#search_soa').click();
      }
    });


    // Button save
    $('#btn_save').on('click', function(event){
      event.preventDefault();

      var _token = CSRF_TOKEN;
      var patient_name = $('#patient_name').val();
      var or_number = $('#prefix_or_number').val();
      var or_count = 0;

      if (patient_name == '' || patient_name == null) {
        alert('Please input patient details .');

      } else {

        savePayment(_token, or_number );
      }
      // alert('clicked save button ');
    });

    function savePayment(_t, or ) {
      var _token = _t;
      var or_number_value = or;
      var arrData = [];

      var date = new Date();
      var month = date.getMonth() + 1;
      var day = date.getDate();
      var hour = date.getHours();
      var minute = date.getMinutes();
      var second = date.getSeconds();

      var encounter_code_value = $('#encounter_code').val();
      var patient_id_value = $('#patient_id').val();
      var account_number_value = $('#account_number').val();
      var soa_number_value = $('#soa_number').val();
      var paystat_value = $('#paystat').val();
      var payment_lock_value = $('#paylock').val();
      var updsw_value = $('#updsw').val();
      var confdl_value = $('#confdl').val();
      var payment_status_value = $('#status').val();
      var or_date_value = $('#or_date').val();
      var user_id_value = $('#user_id').val();
      var payment_mode_value = $('#payment_mode').val();
      var currency_value = $('#currency').val();
      var payment_type_value = $('#payment_type').val();
      var amount_paid_value = removeComma($('#total_amount').val());
      var amount_tendered_value = removeComma($('#amount_tendered').val());
      var amount_change_value =  removeComma($('#amount_change').val());
      var ending_balance_value = removeComma($('#ending_balance').val());
      var created_at_value = date.getFullYear() + '-' + (('' + month).length < 2 ? '0' : '') + month + '-' + (('' + day).length < 2 ? '0' : '') + day + ' ' + hour + ':' + minute + ':' + second;
      var advance_payment_value = 0;
      var remarks_value = $('#remarks').val();

      if ($('#advance_payment').is(':checked')) { advance_payment_value = 1; }

      // loop over each charge table row (tr)
      $('.charge_items').each(function() {
        var row = $(this);
        var charge_code_value = row.closest('tr').attr('id');
        var category_value = row.closest('tr').find('.category').text();
        var total_value = removeComma(row.closest('tr').find('.charge').val());

        var obj = {};

        if (total_value > 0) {
          // console.log(row_id);
          // console.log(category);
          // console.log(total_value);
          obj.or_number = or_number_value;
          obj.encounter_code = encounter_code_value;
          obj.patient_id = patient_id_value;
          obj.account_number = account_number_value;
          obj.soa_number = soa_number_value;
          obj.paystat = paystat_value;
          obj.payment_lock = payment_lock_value;
          obj.updsw = updsw_value;
          obj.confdl = confdl_value;
          obj.payment_status = payment_status_value;
          obj.or_date = or_date_value;
          obj.user_id = user_id_value;
          obj.payment_mode = payment_mode_value;
          obj.currency = currency_value;
          obj.payment_type = payment_type_value;
          obj.amount_paid = amount_paid_value;
          obj.amount_tendered = amount_tendered_value;
          obj.amount_change = amount_change_value;
          obj.ending_balance = ending_balance_value;
          obj.created_at = created_at_value;
          obj.advance_payment = advance_payment_value;
          obj.remarks = remarks_value

          obj.product_id = charge_code_value;
          obj.category = category_value;
          obj.total = total_value;

          arrData.push(obj);
        }
      });
      // console.log(arrData);

      console.log('passed');

      var q = confirm('Are you sure you want to save this payment ?');
      if (q == true) {
        $.ajax({
          type: "POST",
          url: "/collections/inpatient/create/check-or-duplicate",
          data: { _token: _token, or_number: or_number_value, arrData: arrData },
          dataType: "JSON",
          success: function(data){
            var or_count = data.data;
            var or_number = data.or_number;
            var _token = data._token;
            var arrData = data.arrData;

            if (or_count > 0) {
              // console.log('duplicate OR');
              alert('Please use another OR Number (Duplicate Entry) .');
            } else {

              $.ajax({
                type: "POST",
                url: "/collections/inpatient/create/save-payment",
                data: { _token: _token, arrData: arrData, or_number: or_number },
                dataType: "JSON",
                success: function(data){
                  console.log('saved');
                  console.log(data);

                   $('.alert').show();
                   $('#btn_new').show();
                   $('#btn_print').show();
                   // $('#btn_print').click();
                   $('#btn_save').hide();
                }
              }); // End of  ajax url:"/collections/other/store_payment",

            }
          }
        });
      }

    } //savePayment

    $('#collections_inpatient').on('keypress', function(e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
        e.preventDefault();
        return false;
      }
    });

    $('#total_amount').on('keypress', function(e){
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {

        return $('#update_totals').click();
      }
    });

    $('#soa_number').on('keydown', function(e){
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {

        return $('#search_soa').click();

      }
    });

    $('#btn_print').click(function(event) {
      event.preventDefault();
      var prefix_or_number = $('#prefix_or_number').val();
      window.location.replace("/collections/inpatient/print/pdf/" + prefix_or_number);
    });

    $('#btn_new').click(function(e){
      e.preventDefault();
      var user_id = $('#user_id').val();
      window.location.replace("/collections/inpatient/create/" + user_id);
    });

  });

</script>

@endsection
