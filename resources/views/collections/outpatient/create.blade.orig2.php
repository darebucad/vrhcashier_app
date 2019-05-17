@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

    <h1 class="h5"><a href="{{ url('collections/outpatient') }}">Out-Patient Payment</a> / Create Out-Patient Payment</h1>

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

  <form id="collections_outpatient">
    @csrf

    <div class="row">
      <!-- New Button -->
      <button class="btn btn-sm btn-danger" id="btn_new" style="display: none; margin-right: 5px;">New</button>

      <!-- Save button -->
      <button class="btn btn-sm btn-primary" id="btn_save">Save</button>

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
    <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
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
        <input type="text" name="search_barcode" id="search_barcode" class="form-control form-control-sm" placeholder="Charge Slip Number / Barcode" aria-label="Charge Slip Number / Barcode" aria-describedby="post_data" style="background-color:#99ccff!important;" required autofocus>
        <div class="input-group-append">
        <button class="btn btn-success btn-sm" id="post_data">Search</button>
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
          <input type="text" name="patient_name" id="patient_name" value="" class="form-control form-control-sm" style="background-color:#99ccff!important;" required>
        </div>
      </div>
    </div>

    <!-- OR Date / OR Number Input -->
    <div class="form-group row" style="margin-top: -10px">
      <label for="or_date" class="col-md-1 col-form-label text-md-left">{{ __('OR Date') }}</label>
      <div class="col-md-2">
        <div class="input-group mb-3">
          <input id="or_date" type="text" class="form-control form-control-sm" name="or_date" value="{{ $now = date('m/d/Y') }}" style="background-color:#99ccff!important;" required>
            <!-- <span data-feather="calender"></span> -->
        </div>
      </div>

      <label for="or_number" class="col-md-1 col-form-label text-md-left">{{ __('OR Number') }}</label>

      <div class="col-md-3">

        <!-- <input type="text" name="" value="{{ $or_prefix }}"> -->
        <input id="or_number" type="text" class="form-control form-control-sm" name="or_number" value="{{ $or_prefix . $or_number }}" style="background-color:#99ccff!important;" required>
        <input type="hidden" name="or_number_only" value="{{ $payment->next_or_number }}">

        <!-- @if (count($payments) > 0)
          @foreach ($payments as $payment)
            <input id="or_number" type="text" class="form-control form-control-sm" name="or_number" value="{{ $payment->or_prefix . $payment->next_or_number }}" style="background-color:#99ccff!important;" required>
            <input type="hidden" name="or_number_only" value="{{ $payment->next_or_number }}">
          @endforeach

        @else
          @foreach ($payments as $payment)
            <input id="or_number" type="text" class="form-control form-control-sm" name="or_number" value="{{ $payment->or_prefix . '0000001' }}" style="background-color:#99ccff!important;" required>
            <input type="hidden" name="or_number_only" value="{{ '0000001' }}">
          @endforeach
        @endif -->




        @if ($errors->has('or_number'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('or_number') }}</strong>
          </span>
        @endif
      </div>

      <label class="col-md-1 col-form-label text-md-left"> User: </label>
      <label class="col-md-1 col-form-label text-md-left"> {{ Auth::user()->name }} </label>

      <div class="col-md-1 offset-md-1">
        <button type="button" name="update_totals" id="update_totals" class="btn btn-outline-dark btn-sm">
          Update Totals
        </button>
      </div>

    </div>


    <!-- Mode/Type of payment Control -->
    <div class="form-group row">
      <label for="payment_mode" class="col-md-2 col-form-label text-md-left">{{ __('Mode of Payment') }}</label>
      <div class="col-md-2">
        <select id="payment_mode" class="form-control form-control-sm" name="payment_mode">
          <option value=""> </option>
          <option value="C" selected>Cash</option>
          <option value="X">Check</option>
        </select>
      </div>

      <label for="discount_percent" class="col-md-1 col-form-label text-md-left">{{ __('Discount (%)') }}</label>

      <div class="col-md-3">
        <select  id="discount_percent" class="form-control form-control-sm" name="discount_percent">
          <option value="" selected> </option>
          @foreach($discounts as $discount)
            <option value="{{ $discount->id }}">{{ $discount->discount_name }}</option>
          @endforeach
        </select>
      </div>

      <!-- <div class="col-md-3">
        <select  id="discount_percent" class="form-control form-control-sm" name="discount_percent">
          <option value=" " selected> </option>
          <option value="PWD">PWD</option>
          <option value="SENIOR">Senior Citizen</option>
          <option value="10">10% Discount</option>
          <option value="20">20% Discount</option>
          <option value="25">25% Discount</option>
          <option value="50">50% Discount</option>
          <option value="75">75% Discount</option>
          <option value="100">100% Discount</option>
        </select>
      </div> -->

      <label for="amount_paid" class="col-md-2 col-form-label text-md-right">{{ __('Total Amount') }}</label>
      <div class="col-md-2">
        <input id="amount_paid" type="text" class="form-control" name="amount_paid" style="background-color:#99ccff!important; font-weight: bold; font-size: 25px;" value="0.00">
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
        <select id="payment_type" class="form-control form-control-sm" name="payment_type">
          <option value=""> </option>
          <option value="A">Additional Deposit</option>
          <option value="D">Donation</option>
          <option value="F" selected>Full Payment</option>
          <option value="I">Initial Deposit</option>
          <option value="P">Partial Payment</option>
        </select>
      </div>

      <label for="discount_computation" class="col-md-2 col-form-label text-md-left">{{ __('Discount Computation') }}</label>
      <div class="col-md-2">
        <select name="discount_computation" id="discount_computation" class="form-control form-control-sm">
          <option value=" "> </option>
          <option value="normal" selected>Normal</option>
          <option value="lessvat">Less VAT</option>
        </select>
        @if ($errors->has('discount_computation'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('discount_computation') }}</strong>
            </span>
        @endif
      </div>

      <label for="amount_tendered" class="col-md-2 col-form-label text-md-right">Amount tendered</label>

      <div class="col-md-2">
        <input id="amount_tendered" type="text" class="form-control" name="amount_tendered"  style="background-color:#99ccff!important; font-weight: bold; font-size: 25px;" value="0.00">

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
        <select id="currency" class="form-control form-control-sm" name="currency">
          <option value=""> </option>
          <option value="DOLLA">Dollars</option>
          <option value="OTHER">Others</option>
          <option value="PESO" selected>Php</option>
          <option value="YEN">Yen</option>
        </select>
      </div>

      <div class="col-md-1 offset-md-1">
        <button type="button" id="apply_discount_all" class="btn btn-success btn-sm">
          Apply to all
        </button>
      </div>

      <div class="col-md-1">
        <button type="button" id="apply_discount_selected" class="btn btn-success btn-sm">
          Apply to Selected
        </button>
      </div>

      <div class="col-md-1">
        <button type="button" name="clear_discount" id="clear_discount" class="btn btn-outline-secondary btn-sm">
          Clear Discount
        </button>
      </div>

      <label for="amount_change" class="col-md-2 col-form-label text-md-right" style="font-size: 13px">{{ __('Change') }}</label>

      <div class="col-md-2">
        <input id="amount_change" type="text" class="form-control" name="amount_change" style="font-weight: bold; font-size: 25px;" value="0.00">
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
        <!-- @for ($i = 0; $i < 4; $i++)
        <tr>
          <td colspan="11" style="color:white;"> .</td>
        </tr>
        @endfor -->
      </tbody>
    </table>
  </div>
</main>


<script type="text/javascript">
  // Binds to the global ajax scope
  $( document ).ajaxStart(function() {
    $( "#spinner" ).show();
  });

  $( document ).ajaxComplete(function() {
    $( "#spinner" ).hide();
  });

  // Post Data / Search Barcode button
  $(document).ready(function(){
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var editor = new SimpleTableCellEditor("invoice_table");
    editor.SetEditableClass("editMe");

    $('#search_barcode').on('click', function(){
      $(this).select();

    });


    $('#post_data').click(function(event){
      event.preventDefault();
      var _token = CSRF_TOKEN;
      var charge_slip_number = $('#search_barcode').val();
      var user_id = $('#user_id').val();
      var duplicate = '';

      if (charge_slip_number == '') {
        alert('Please input charge slip number / barcode .');

      } else {

        duplicate = checkDuplicateChargeSlipNumber(charge_slip_number);
        // console.log(duplicate);

        if (duplicate == true) {
          alert('Please input another charge slip number / barcode (Duplicate Entry)');
          // console.log('duplicate charge slip');

        } else {
          // Avoid the same charge slip number .
          searchOutpatientCharges(_token, charge_slip_number, user_id);
        }
      }
    });

    function checkDuplicateChargeSlipNumber(cslip){
      var result = false;
      var row_count = $('#invoice_table tbody tr').length;

      if (row_count > 0) {
        $('.charge_items').each(function(){
          var row = $(this);
          var charge_slip_number = row.closest('tr').find('.charge_slip_number').text();

          if (charge_slip_number == cslip) {
            result = true;
          }

        });
      }
      return result;
    }

    function searchOutpatientCharges( _t, cslip, uid ) {
      var content = '';
      var discount = 0;
      var total_discount_value = 0;
      var total = 0;
      var is_paid = 0;
      var is_pay = 0;
      var is_discount = 0;
      var account_number = 0;
      var order_number = '';
      var order_date = '';
      var charge_slip_number = '';
      var product_code = '';
      var product_description = '';
      var quantity = '';
      var unit_price = '';
      var discount_percent = '';
      var discount_value = '';
      var sub_total = '';
      var invoice_status = '';
      var charge_code = '';

      $.ajax({
        type: "POST",
        url: "/collections/outpatient/create/post_data",
        data: { _token: _t, charge_slip_number: cslip, user_id: uid },
        dataType: "JSON",
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Please provide a valid charge slip number / barcode .');
        },
        success: function(data) {
          // console.log(data);
          // console.log(data.charge_slip_number);

          account_number = data.new_account_number;

          $('#acctno').val(account_number);

          // Get list of patient and payment details
          $.each(data.data, function(i, data) {
            charge_code = data.charge_code;
            $('#patient_name').val(data.patient_name);
            $('#pcchrgcod').val(data.charge_slip_number);
            $('#enccode').val(data.encounter_code);
            $('#hpercode').val(data.patient_id);
            $('#discount_percent').val(data.discount_id);
            $('#paystat').val('A');
            $('#paylock').val('N');
            $('#updsw').val('N');
            $('#confdl').val('N');
            $('#payctr').val('0');
            $('#status').val('Paid');
            $('#charge_code').val(charge_code);

          });
            // $('#charge-info').empty();
            $.each(data.data, function(i, data) {
              order_number = data.order_number;
              order_date = data.order_date;
              charge_slip_number = data.charge_slip_number;
              product_code = data.product_code;
              product_description = data.product_description;
              quantity = data.quantity;
              unit_price = data.unit_price;
              discount_percent = data.discount_percent;
              discount_value = data.discount_value;
              // sub_total = data.amount;
              sub_total = data.sub_total;
              amount = data.amount;
              invoice_status = 'Invoiced';
              is_pay = data.is_pay;
              is_discount = data.is_discount;

              // Discount percent value
              if (discount_percent == null || discount_percent == '' || discount_percent == 0) {
                discount_percent = Number(0);
              }

              // Discount value
              if (discount_value == null || discount_value == '' || discount_percent == 0) {
                discount_value = Number(0);
              }

              // Sub total value
              if (sub_total == 0 && is_pay == null) {
                sub_total = amount;
              } else if (sub_total == 0 && (discount_percent == 100 || is_pay == 0)) {
                // sub_total = sub_total;
              } else {
                // sub_total = sub_total;
              }

              if (is_pay == null || is_pay == 1) { is_pay = 'checked'; }
              else { is_pay = ''; }

              if (is_discount == null || is_discount == 0) {  is_discount = '';  }
              else {  is_discount = 'checked';   }

              content = '<tr id="'+ order_number +'" class="charge_items">';
              content += '<td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" class="pay_checkbox" value="' + order_number +'" '+ is_pay +'></td>';
              content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" class="discount_checkbox" value="' + order_number + '" ' + is_discount + '></td>';
              content += '<td>' + order_date + '</td>';
              content += '<td class="charge_slip_number">' + charge_slip_number + '</td>';
              content += '<td id="'+ product_code +'">' + product_description + '</td>';
              content += '<td align="right" class="quantity">' + quantity + '</td>';
              content += '<td align="right" class="unit_price">' + unit_price + '</td>';
              content += '<td align="right" style="width:8%" class="discount_percent">' + Number(discount_percent).toFixed(2) + '</td>';
              content += '<td align="right" style="width:10%" class="editMe discount_value">' + Number(discount_value).toFixed(2) + '</td>';
              content += '<td align="right" style="width:10%" class="sub_total">' + Number(sub_total).toFixed(2) + '</td>';
              content += '<td>' + invoice_status + '</td>';
              content += '</tr>';
              $(content).appendTo('#charge-info');

              total_discount_value += Number(discount_value);
              total += Number(sub_total);
            });
            // content = '<tr><td colspan=8 align="right"><strong>Grand Total: </strong></td>';
            // content += '<td colspan=1 align="right">' + Number(total_discount_value).toFixed(2) + '</td>';
            // content += '<td colspan=1 align="right" style="font-weight:bold;"><strong>' + Number(total).toFixed(2) + '</strong></td></tr>';
            // $(content).appendTo('#charge-info');
            // $('#amount_paid').val((total).toFixed(2));
            $('#amount_paid').val(formatNumber(calculateTotalAmount().toFixed(2)));

        }
      });

    }  // end of function searchOutPatientCharges


    $('#search_barcode').on('keydown', function(event){
      if (event.which == 13 ) {
        // alert('pressed enter at search barcode field');
        $('#post_data').click();
      }
    });
    // end of search barcode keydown event .


    $('#discount_percent').on('change', function(event) {
      var discount_id = $(this).val();
      var _token = CSRF_TOKEN;
      // alert(discount_id);
      getDiscountPercent(_token, discount_id);

    });


    function getDiscountPercent(_t, did) {
      $.ajax({
        type: "POST",
        url: "/collections/outpatient/create/get-discount-percent",
        data: { _token: _t, discount_id: did },
        dataType: "JSON",
        success: function(data) {
          var discount_percent = data.data;
          $('#discount_percent_value').val(discount_percent);

        }
      });
    }


    // Click apply discount all
    $("#apply_discount_all").click(function(event){
      event.preventDefault();

      var _token = CSRF_TOKEN;
      var charge_slip_number = $('#search_barcode').val();
      var discount_id = $('#discount_percent').val();
      var user_id = $('#user_id').val();
      var discount_percent = Number($('#discount_percent_value').val());

      if (discount_id == '') {
        alert('Please select discount percent(%) .');

      } else {
        applyDiscountAll(discount_percent);
        // applyDiscountAll(_token, charge_slip_number, discount_id, user_id);
      }
    }); // end of apply discount to all button click event .


    function applyDiscountAll(d) {
      var row = '';
      var row_id = '';
      var product_id = '';
      var quantity = 0;
      var unit_price = 0;
      var discount_percent = d;
      var discount_value = 0;
      var sub_total = 0;
      var total = 0;

      $('.charge_items').each(function(){
        row = $(this);
        row_id = $(this).closest('tr').attr('id');
        product_id = $(this).closest('tr').find('td:eq(4)').attr('id');
        quantity = Number($(this).closest('tr').find('.quantity').text());
        unit_price = Number($(this).closest('tr').find('.unit_price').text());
        sub_total = quantity * unit_price;
        discount_percent_value = (discount_percent * 100);
        discount_value = sub_total * discount_percent;
        sub_total = sub_total - discount_value;
        total += sub_total;

        $(this).closest('tr').find('.discount_percent').text(discount_percent_value.toFixed(2));
        $(this).closest('tr').find('.discount_value').text(discount_value.toFixed(2));
        $(this).closest('tr').find('.sub_total').text(sub_total.toFixed(2));
      });

      $('#amount_paid').val(formatNumber(total.toFixed(2)));

      $('input[name="discount_checkbox"]:not(:checked)').each(function(){
        $(this).prop('checked', true);
      });

    }



    // Apply discount selected
    $('#apply_discount_selected').click(function(event){
      event.preventDefault();

      var _token = CSRF_TOKEN;
      var discount_percent = Number($('#discount_percent_value').val());
      /* declare an checkbox array for selected discount checkbox . */
      var discount_ids = [];


      /* look for all checkboxes that have a class 'discount_checkbox' attached to it and check if it was checked */
      $('input[name="discount_checkbox"]:checked').each(function() {
        discount_ids.push($(this).val());

      });

      if (discount_percent == '') {
        alert('Please select discount percent(%) .');

      } else {

        if (discount_ids.length > 0) {
          applyDiscountSelected(discount_percent, discount_ids);

        } else {
          alert("Please check at least one(1) discount checkbox .");

        }
      }

    });

    function applyDiscountSelected(d, dids) {
      // var total_discount_value = 0;
      var is_discount = 0;
      var is_pay = 0;
      var discount_name = '';
      var discount_percent = d;
      var discount_ids = dids;
      var row_id = '';
      var product_id = '';
      var quantity = 0;
      var unit_price = 0;
      var sub_total = 0;
      var discount_value = 0;
      var total = 0;
      var row_id_table = '';

      $.each(discount_ids, function(i, data) {
        // console.log(data);
        row_id = data;
        $('.charge_items').each(function(){
          row_id_table = $(this).closest('tr').attr('id');
          if (row_id_table == row_id) {
            product_id = $(this).closest('tr').find('td:eq(4)').attr('id');
            quantity = Number($(this).closest('tr').find('.quantity').text());
            unit_price = Number($(this).closest('tr').find('.unit_price').text());
            sub_total = quantity * unit_price;
            discount_percent_value = discount_percent * 100;
            discount_value = sub_total * discount_percent;
            sub_total = sub_total - discount_value;
            total += sub_total;

            $(this).closest('tr').find('.discount_percent').text(discount_percent_value.toFixed(2));
            $(this).closest('tr').find('.discount_value').text(discount_value.toFixed(2));
            $(this).closest('tr').find('.sub_total').text(sub_total.toFixed(2));
          }
        });
      });
      $('#amount_paid').val(formatNumber(calculateTotalAmount().toFixed(2)));

    }

    function calculateTotalAmount() {
      var sub_total = 0;
      var total = 0;

      $('.charge_items').each(function(){
        sub_total = Number($(this).closest('tr').find('.sub_total').text());
        total += sub_total;
      });

      $('#total_amount').val(total.toFixed(2));
      return total;
    }


    $('#clear_discount').click(function(event){
      event.preventDefault();
      var q = confirm('Are you sure you want to clear the discount(s) ?');

      if (q == true) {
        clearDiscount();

      } else {

      }

    });

    // click clear discount button
    function clearDiscount(){
      var sub_total = 0;
      var total = 0;
      var blank_value = Number(0).toFixed(2);

      $('input[name="pay_checkbox"]:not(:checked)').each(function(){
        $(this).prop("checked", true);
      });

      $('input[name="discount_checkbox"]:checked').each(function(){
        $(this).prop("checked", false);
      });
      $('.alert').alert('close');
      $('#discount_percent').val('');
      $('#amount_paid').val(blank_value);
      $('#amount_tendered').val(blank_value);
      $('#change').val(blank_value);
      $('#discount_percent_value').val('');

      // $('#charge-info').empty();
      $('.charge_items').each(function(){
        // row_id_table = $(this).closest('tr').attr('id');
        product_id = $(this).closest('tr').find('td:eq(4)').attr('id');
        quantity = Number($(this).closest('tr').find('.quantity').text());
        unit_price = Number($(this).closest('tr').find('.unit_price').text());
        sub_total = quantity * unit_price;
        discount_percent_value = blank_value;
        discount_value = blank_value;
        total += sub_total;
        // console.log(quantity);
        // console.log(unit_price);
        // console.log(sub_total);
        // console.log(discount_percent_value);
        // console.log(discount_percent);
        $(this).closest('tr').find('.discount_percent').text(discount_percent_value);
        $(this).closest('tr').find('.discount_value').text(discount_value);
        $(this).closest('tr').find('.sub_total').text(sub_total.toFixed(2));

      });

      $('#amount_paid').val(formatNumber(total.toFixed(2)));
    }


    // Checkbox status checker
    $('input[type="checkbox"]').click(function(){
      if($(this).is(":checked")){
        alert("Checkbox is checked.");
      } else{
        alert("Checkbox is unchecked.");
      }
    });

    // Update total button clicked event
    $('#update_totals').click(function(event){
      event.preventDefault();

      var discount_percent = $('#discount_percent_value').val();
      var pay_ids = [];
      var discount_ids = [];
      var pay2_ids = [];
      // var total_discount_value = 0;
      var total = 0;
      var q = confirm('Are you sure you want to update the total charges ?');

      // Free products / services .
      // $('input[name="pay_checkbox"]:not(:checked)').each(function() {
      //   pay_ids.push($(this).val());
      // });

      // Paid products / services .
      // $('input[name="pay_checkbox"]:checked').each(function(){
      //   pay2_ids.push($(this).val());
      // });

      // Discounted products / services .
      // $('input[name="discount_checkbox"]:checked').each(function(){
      //   var discount_id = $(this).val();
      //   $.each(pay2_ids, function(i, data) {
      //     var id = data;
      //
      //     if (id == discount_id) {
      //       discount_ids.push(discount_id);
      //     }
      //   });
      // });

      if (q == true) {
        updateTotals(discount_percent, pay_ids, pay2_ids, discount_ids);
        // console.log(pay_ids);
        // console.log(pay2_ids);
        // console.log(discount_ids);
      } else {

      }

      // alert('unchecked pay checkboxes: ' + id);
      // console.log(id);
    });


    function updateTotals(d, pids, p2ids, dids) {
      // 0.25 / 0.75 values
      var discount = Number(d);
      var pay_ids = pids;
      var pay2_ids = p2ids;
      var discount_ids = dids;
      var quantity = 0;
      var unit_price = 0;
      var discount_percent = 0;
      var discount_value = 0;
      var sub_total = 0;
      var row = '';
      var counter = 10;

      $('.charge_items').each(function(){
        var is_pay = 0;
        var is_discount = 0;

        var disc = discount;

        row = $(this);

        if (row.closest('tr').find('.pay_checkbox').is(':checked')) { is_pay = 1; }
        if (row.closest('tr').find('.discount_checkbox').is(':checked')) { is_discount = 1; }
        quantity = Number(row.closest('tr').find('.quantity').text());
        unit_price = Number(row.closest('tr').find('.unit_price').text());
        discount_value = Number(row.closest('tr').find('.discount_value').text());
        sub_total = quantity * unit_price;

        if (is_pay == 0) {
          discount_percent = 0;
          discount_value = 0;
          sub_total = 0;

        } else {

          if (discount_value == 0) {

            if (is_discount == 1) {

              // console.log(disc);
              if (disc == 0) {


                $('#alert_value').text('Please select discount percent(%) .');
                $('.alert').show();

                console.log('enter discount_percent');

              } else {
                discount_percent = disc * 100;
                discount_value = sub_total * disc;
                sub_total = sub_total - discount_value;

              }

            } else {
              discount_percent = 0;
              discount_value = 0;

            }


          } else {
            if (discount_value > sub_total) {

              $('#alert_value').text('Discount value is greater than sub total value .');
              $('.alert').show();

              discount_percent = 0;
              discount_value = 0;

            } else {
              discount_percent = 0;
              sub_total = sub_total - discount_value;
            }
          }
        }


        // discount_percent_value = 0;
        // discount_value = 0;
        // sub_total = quantity * unit_price;
        // discount_value = sub_total * discount_percent;
        // sub_total = sub_total - discount_value;


        // console.log(counter);
        // console.log(is_pay);
        // console.log(is_discount);
        // console.log(quantity);
        // console.log(unit_price);
        // console.log(discount_percent);
        // console.log(discount_value);
        // console.log(sub_total);



        $(this).closest('tr').find('.discount_percent').text(discount_percent.toFixed(2));
        $(this).closest('tr').find('.discount_value').text(discount_value.toFixed(2));
        $(this).closest('tr').find('.sub_total').text(sub_total.toFixed(2));

      });


      // REVISED

      // Check if there is free products and discount products .
      // if ((pay_ids.length > 0 || pay_ids.length < 1) && discount_ids.length > 0) {
      //
      //   if (discount_percent == '' || discount_percent == 0) {
      //     alert('Please select discount percent(%) .');
      //
      //   } else {
      //
      //     if (pay_ids.length > 0) {
      //       // free products
      //       $.each(pay_ids, function(i, data) {
      //         var row_id = data;
      //
      //         $('.charge_items').each(function(){
      //           var row_id_table = $(this).closest('tr').attr('id');
      //
      //           if (row_id_table == row_id) {
      //             discount_percent_value = zero_value;
      //             discount_value = zero_value;
      //             sub_total = zero_value;
      //
      //             $(this).closest('tr').find('.discount_percent').text(discount_percent_value);
      //             $(this).closest('tr').find('.discount_value').text(discount_value);
      //             $(this).closest('tr').find('.sub_total').text(sub_total);
      //           }
      //         });
      //       });
      //     }
      //
      //     // Discounted products
      //     $.each(discount_ids, function(i, data) {
      //       var row_id = data;
      //
      //       $('.charge_items').each(function(){
      //         var row_id_table = $(this).closest('tr').attr('id');
      //
      //         if (row_id_table == row_id) {
      //           quantity = Number($(this).closest('tr').find('.quantity').text());
      //           unit_price = Number($(this).closest('tr').find('.unit_price').text());
      //           discount_percent_value = discount_percent * 100;
      //           sub_total = quantity * unit_price;
      //           discount_value = sub_total * discount_percent;
      //           sub_total = sub_total - discount_value;
      //           // console.log(discount_percent_value);
      //           // console.log(discount_value);
      //
      //           $(this).closest('tr').find('.discount_percent').text(discount_percent_value.toFixed(2));
      //           $(this).closest('tr').find('.discount_value').text(discount_value.toFixed(2));
      //           $(this).closest('tr').find('.sub_total').text(sub_total.toFixed(2));
      //         }
      //       });
      //     });
      //   }
      //
      // } else if (pay_ids.length > 0 && discount_ids.length < 1) {
      //
      //   // free products
      //   $.each(pay_ids, function(i, data) {
      //     var row_id = data;
      //
      //     $('.charge_items').each(function(){
      //       var row_id_table = $(this).closest('tr').attr('id');
      //
      //       if (row_id_table == row_id) {
      //         discount_percent_value = zero_value;
      //         discount_value = zero_value;
      //         sub_total = zero_value;
      //
      //         $(this).closest('tr').find('.discount_percent').text(discount_percent_value);
      //         $(this).closest('tr').find('.discount_value').text(discount_value);
      //         $(this).closest('tr').find('.sub_total').text(sub_total);
      //       }
      //     });
      //   });
      //
      //   // Discounted products
      //   $.each(pay2_ids, function(i, data) {
      //     var row_id = data;
      //
      //     $('.charge_items').each(function(){
      //       var row_id_table = $(this).closest('tr').attr('id');
      //
      //       if (row_id_table == row_id) {
      //         quantity = Number($(this).closest('tr').find('.quantity').text());
      //         unit_price = Number($(this).closest('tr').find('.unit_price').text());
      //         discount_percent_value = Number(0);
      //         discount_value = Number($(this).closest('tr').find('.discount_value').text());
      //         sub_total = quantity * unit_price;
      //
      //         if (discount_value < 1) {
      //
      //         } else if (discount_value > sub_total) {
      //           // alert('Discount value is greater than sub total value .');
      //           // console.log('discount value greater than sub total .');
      //
      //           // $('.discount_sub').show();
      //           $('#alert_value').text('Discount value is greater than sub total value .');
      //           $('.alert').show();
      //
      //           discount_value = 0;
      //
      //         } else {
      //           // discount_value = discount_value;
      //         }
      //
      //         sub_total = sub_total - discount_value;
      //
      //         $(this).closest('tr').find('.discount_percent').text(Number(discount_percent_value).toFixed(2));
      //         $(this).closest('tr').find('.discount_value').text(Number(discount_value).toFixed(2));
      //         $(this).closest('tr').find('.sub_total').text(Number(sub_total).toFixed(2));
      //
      //       }
      //
      //       else {
      //
      //       }
      //     });
      //   });
      //
      // } else {
      //
      //   // Discounted products
      //   $('.charge_items').each(function(){
      //     var row_id_table = $(this).closest('tr').attr('id');
      //     quantity = Number($(this).closest('tr').find('.quantity').text());
      //     unit_price = Number($(this).closest('tr').find('.unit_price').text());
      //     discount_percent_value = Number(0);
      //     discount_value = Number($(this).closest('tr').find('.discount_value').text());
      //     sub_total = quantity * unit_price;
      //
      //     if (discount_value < 1) {
      //
      //     } else if (discount_value > sub_total) {
      //       // alert('Discount value is greater than sub total value .');
      //       // console.log('discount value greater than sub total .');
      //       // $('.discount_sub').show();
      //       $('#alert_value').text('Discount value is greater than sub total value .');
      //       $('.alert').show();
      //       discount_value = 0;
      //
      //
      //     } else {
      //       // discount_value = discount_value;
      //
      //     }
      //
      //     sub_total = sub_total - discount_value;
      //
      //
      //     $(this).closest('tr').find('.discount_percent').text(Number(discount_percent_value).toFixed(2));
      //     $(this).closest('tr').find('.discount_value').text(Number(discount_value).toFixed(2));
      //     $(this).closest('tr').find('.sub_total').text(Number(sub_total).toFixed(2));
      //
      //   });
      // }


      // REVISED

      $('#amount_paid').val(formatNumber(calculateTotalAmount().toFixed(2)));

    }

    function getTableData(ids, dp, dv, st) {
      var pay_ids = ids;
      var row_id = '';
      var row_id_charges = '';

      // free products
      $.each(pay_ids, function(i, data) {
        row_id = data;

        $('.charge_items').each(function(){
          row_id_charges = $(this).closest('tr').attr('id');

          if (row_id_charges == row_id) {
            quantity = Number($(this).closest('tr').find('.quantity').text());
            unit_price = Number($(this).closest('tr').find('.unit_price').text());

            discount_percent_value = dp;
            discount_value = dv;
            sub_total = st;

            $(this).closest('tr').find('.discount_percent').text(discount_percent_value);
            $(this).closest('tr').find('.discount_value').text(discount_value);
            $(this).closest('tr').find('.sub_total').text(sub_total);
          }
        });
      });
    }

    $("#invoice_table").on("cell:edited", function(event) {
      var row_id = $('#invoice_table').closest('tr').attr('id');
      var id = $(this).val();
      quantity = $('#invoice_table').find('tr#'+ row_id).find('.quantity').text();
      price = $('#invoice_table').find('tr#'+ row_id).find('.unit_price').text();
      sub_total = $('#invoice_table').find('tr#'+ row_id).find('.sub_total').text();

      // console.log(`'${event.oldValue}' changed to '${event.newValue}'`);
    });

    $("#invoice_table").on("cell:onEditEnter", function (element, oldValue) {
      // on edit mode
    });

    $("#invoice_table").on("cell:onEditExit", function (element, oldValue) {
      // on edit mode
    });

    $("#invoice_table").on("cell:onEditEntered", function (element) {
      // exit edit mode
    });

    $("#invoice_table").on("cell:onEditExited", function (element, oldValue, newValue, isApplied) {
      // exit edit mode
    });


    function computeChange(){
      var amount_paid = removeComma(Number($('#amount_paid').val()));
      var amount_tendered = Number($('#amount_tendered').val());
      var compute = 0;

      if (amount_paid > amount_tendered) {
        alert('Amount tendered must be greater than the total amount.');
        $('#amount_change').val(Number(0.00).toFixed(2));
      } else {
        compute = amount_tendered - amount_paid;
        $('#amount_tendered').val(Number(amount_tendered).toFixed(2));
        $('#amount_change').val(Number(compute).toFixed(2));
      }
    }

    $('#amount_tendered').keydown(function(event){
      // event.preventDefault();
      if (event.which == 13) {
        // alert('pressend enter at amount tendered field');
        computeChange();
        // $('#change').focus();
      }
    });



    // Save button
    $('#btn_save').on('click', function(e) {
      e.preventDefault();

      var _token = CSRF_TOKEN;
      var charge_slip_number = $('#search_barcode').val();
      var row_count = $('#invoice_table tbody tr').length;
      var or_number = $('#or_number').val();
      var patient_name = $('#patient_name').val();
      var q = '';

      // Check the value of charge slip number/barcode .
      if (charge_slip_number == '') {
        alert('Please input charge slip number / barcode .');
        // console.log('please input charge slip number / barcode .');

      } else {
        // Check the value of patient name input .
        if (patient_name == '') {
          alert('Please input valid charge slip number / barcode .');
          // console.log('please input valid charge slip number / barcode .');

        } else {
          // Save payment function
          savePayment(_token, charge_slip_number, row_count, or_number);
        }
      }
    });

    function savePayment(_t, cslip, rc, or ) {
      var _token = _t;
      // var charge_slip_number_value = cslip;
      var row_count = rc;
      var or_number_value = or;
      var arrData=[];
      var date = new Date();
	    var month = date.getMonth() + 1;
	    var day = date.getDate();
			var hour = date.getHours();
			var minute = date.getMinutes();
			var second = date.getSeconds();
      var encounter_code_value = $('#enccode').val();
      var patient_id_value = $('#hpercode').val();
      var account_number_value = $('#acctno').val();
      var paystat_value = $('#paystat').val();
      var payment_lock_value = $('#paylock').val();
      var updsw_value = $('#updsw').val();
      var confdl_value = $('#confdl').val();
      var payment_status_value = 'Paid';
      var or_date_value = $('#or_date').val();
      var user_id_value = $('#user_id').val();
      var payment_mode_value = $('#payment_mode').val();
			var discount_id_value = $('#discount_percent').val();
      var currency_value = $('#currency').val();
      var payment_type_value = $('#payment_type').val();
      var discount_computation_value = $('#discount_computation').val();
      var amount_paid_value = removeComma($('#amount_paid').val());
      var amount_tendered_value = Number($('#amount_tendered').val());
      var amount_change_value =  Number($('#amount_change').val());
			var charge_code_value = $('#charge_code').val();
			var discount_percent_value = 0;
			var created_at_value = date.getFullYear() + '-' + (('' + month).length < 2 ? '0' : '') + month + '-' + (('' + day).length < 2 ? '0' : '') + day + ' ' + hour + ':' + minute + ':' + second;

      // loop over each charge table row (tr)
      $('.charge_items').each(function() {
        var current_row = $(this);
        var charge_slip_number_value = current_row.closest('tr').find('.charge_slip_number').text();
        var product_id_value = current_row.closest('tr').find('td:eq(4)').attr('id');
        var quantity_value = Number(current_row.closest('tr').find('.quantity').text());
        var unit_price_value = Number(current_row.closest('tr').find('.unit_price').text());
        var discount_percent_value = Number(current_row.closest('tr').find('.discount_percent').text());
        var discount_value_value = Number(current_row.closest('tr').find('.discount_value').text());
        var sub_total_value = Number(current_row.closest('tr').find('.sub_total').text());
        var is_pay_value = 0;
        var is_discount_value = 0;
        var obj = {};

        if (current_row.closest('tr').find('.pay_checkbox').is(':checked')) { is_pay_value = 1; }
        if (current_row.closest('tr').find('.discount_checkbox').is(':checked')) { is_discount_value = 1; }

        obj.charge_slip_number = charge_slip_number_value;
        obj.encounter_code = encounter_code_value;
        obj.patient_id = patient_id_value;
        obj.account_number = account_number_value;
        obj.paystat = paystat_value;
        obj.payment_lock = payment_lock_value;
        obj.updsw = updsw_value;
        obj.confdl = confdl_value;
        obj.or_number = or_number_value;
        obj.or_date = or_date_value;
        obj.currency = currency_value;
        obj.payment_type = payment_type_value;
        obj.payment_mode = payment_mode_value;
        obj.user_id = user_id_value;
        obj.discount_computation = discount_computation_value;
        obj.amount_paid = amount_paid_value;
        obj.amount_tendered = amount_tendered_value;
        obj.amount_change = amount_change_value;
        obj.created_at = created_at_value;
        obj.charge_code = charge_code_value;
        obj.payment_status = payment_status_value;

        obj.product_id = product_id_value;
        obj.quantity = quantity_value;
        obj.unit_price = unit_price_value;
        obj.sub_total = sub_total_value;
        obj.discount_id = discount_id_value;
        obj.discount_percent = discount_percent_value;
        obj.discount_value = discount_value_value;
        obj.is_pay = is_pay_value;
        obj.is_discount = is_discount_value;

        arrData.push(obj);
      });
      // console.log(arrData);

      var q = confirm('Are you sure you want to save this payment ?');
      if (q == true) {
        $.ajax({
          type: "POST",
          url: "/collections/outpatient/create/check-or-duplicate",
          data: { _token: _token, or_number: or_number_value, arrData: arrData, row_count: row_count },
          dataType: "JSON",
          success: function(data){
            var or_count = data.data;
            var or_number = data.or_number;
            var _token = data._token;
            var arrData = data.arrData;
            var row_count = data.row_count;

            // console.log(or_count);

            if (or_count > 0) {
              // console.log('duplicate OR');
              alert('Please use another OR Number (Duplicate Entry) .');

            } else {
              // console.log('new OR');

              if (row_count < 1) {
                // console.log('add products');
                alert('Please add at least one product/service to proceed .');

              } else {

                  $.ajax({
                    type: "POST",
                    url: "/collections/outpatient/create/store-payment",
                    data: { _token: _token, arrData: arrData, or_number: or_number },
                    dataType: "JSON",
                    success: function(data){
                      // console.log('saved');
                      console.log(data);
                      console.log(data.or_number);

                       $('.alert').show();
                       $('#btn_new').show();
                       $('#btn_print').show();
                       // $('#btn_print').click();
                       $('#btn_save').hide();
                    }
                  }); // End of  ajax url:"/collections/other/store_payment",
              } // End of if (row_count < 2) {
            }
          }
        });
      }

    } // function save payment


    $('#btn_print').click(function(event) {

      event.preventDefault();
      // var prefix_or_number = $('#or_number').val();
      // window.location.replace("/collections/outpatient/print/pdf/" + prefix_or_number);

      console.log(arrData);



    });


    $('#btn_new').click(function(e){
      e.preventDefault();
      var user_id = $('#user_id').val();
      window.location.replace("/collections/outpatient/create/" + user_id);
    });

    // $('#btn_save').on('keydown', function(e){
    //   // e.preventDefault();
    //     var keyCode = e.keyCode || e.which;
    //
    //     if (keyCode === 13) {
    //       console.log('pressed enter');
    //       e.preventDefault();
    //       return false;
    //     }
    //
    // });


    // $('#collections_outpatient').on('submit', function() {
    //   $('.alert').show();
    //
    // });


    // $('#collections_outpatient').on('keypress', function(e) {
    //   var keyCode = e.keyCode || e.which;
    //
    //   if (keyCode === 13) {
    //     console.log('pressed enter');
    //     e.preventDefault();
    //     return false;
    //   }
    // });


    // $('#collections_outpatient').keydown(function(event){
    //   if (event.which == 13) {
    //     // alert('pressed enter at button save');
    //     console.log('pressed enter');
    //       event.preventDefault();
    //   }
    // });

    $('#collections_outpatient').on('keypress', function(e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
        e.preventDefault();
        return false;
      }
    });

    function formatNumber(num) {
      var for_num = num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');

      return for_num;
    }

    function removeComma(num) {
      var rem_comma = num.replace(/,/g, '');

      return rem_comma;
    }


  });
</script>

<!-- First ever json jquery function of antoy . -->
<script>
  $(document).ready(function(){

    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $("#postbutton").click(function(){
      $.ajax({
          /* the route pointing to the post function */
          url: '/collections/outpatient/create/postajax',
          type: 'POST',

          /* send the csrf-token and the input to the controller */
          data: {_token: CSRF_TOKEN, message:$(".getinfo").val()},

          dataType: 'JSON',
          /* remind that 'data' is the response of the AjaxController */
          success: function (data) {
              $(".writeinfo").append(data.msg);
          }
      });
    });
  });
</script>



@endsection
