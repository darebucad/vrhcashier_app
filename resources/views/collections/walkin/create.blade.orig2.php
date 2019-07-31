@extends ('layouts.master')

@section ('content')
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

    <h1 class="h5"><a href="{{ url('collections/walkin') }}">Walk-In Payment</a> / Create Walk-In Payment</h1>

    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <button class="btn btn-sm btn-outline-secondary" id ="print_button" style="display: none;">
          <span data-feather="printer"></span>
          Print Receipt
        </button>
        <!-- <button class="btn btn-sm btn-outline-secondary" id ="export_button" style="display: none;">Export</button> -->
      </div>

    </div>
  </div>


  <div class="alert alert-success alert-dismissable" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <span id="alert_value">Payment was successfully saved.</span>
  </div>

<!-- <div class="row text-primary">
  <h5>Ongoing development</h5>
</div> -->

  <!-- <div class="alert alert-success alert-dismissable" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      This charge slip was already paid.
  </div> -->

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
    <h6>Loading...</h6>
  </div>

  <form id="collections_walkin">
    @csrf

    <div class="row" style="margin-top:10px;">
      <!-- New button -->
      <button class="btn btn-sm btn-danger" id="btn_new" style="display: none; margin-right: 5px;">New</button>

      <!-- Save button -->
      <button class="btn btn-sm btn-primary" id="save_button">Save</button>
      <p id="button-cancel">or <a class="btn-link" href="{{ url('collections/walkin') }}">Cancel</a></p>
    </div>

    <br />
    <!-- <br /> -->

    <input type="hidden" name="paystat" id="paystat" value="">
    <input type="hidden" name="paylock" id="paylock" value="">
    <input type="hidden" name="updsw" id="updsw" value="">
    <input type="hidden" name="confdl" id="confdl" value="">
    <input type="hidden" name="payctr" id="payctr" value="">
    <input type="hidden" name="status" id="status" value="">
    <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
    <input type="hidden" name="patient_id" id="patient_id" value="">
    <!-- <input type="hidden" name="charge_slip_number" id="charge_slip_number" value=""> -->
    <input type="hidden" name="discount_array" value="" id="discount_array">
    <input type="hidden" name="charge_code" value="" id="charge_code">


    <!-- Charge Details Row -->
    <div class="form-group row">
      <label for="charge_slip_number" class="col-md-1 col-form-label">Charge Details: </label>
      <div class="col-md-3">
        <!-- Search barcode input -->
        <div class="input-group mb-3">
          <input type="text" name="charge_slip_number" id="charge_slip_number" class="form-control form-control-sm" placeholder="Charge Slip Number / Barcode" aria-label="Charge Slip Number / Barcode" aria-describedby="post_data" style="background-color:#99ccff!important;" required autofocus>
          <div class="input-group-append">
          <button class="btn btn-success btn-sm" id="post_data">Search</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Patient Name Input-->
    <div id="patient_name_field" class="form-group row" style="margin-top: -20px;">
      <div class="input-group">
        <label for="patient_name" class="col-md-1 col-form-label text-md-left">{{ __('Patient Name') }}</label>
        <div class="col-md-9">
          <input type="text" name="patient_name" id="patient_name" value="" class="form-control form-control-sm" style="background-color:#99ccff!important;" readonly>
        </div>
      </div>
    </div>

    <!-- OR Date / OR Number Input -->
    <div class="form-group row" style="margin-top: -10px">
      <label for="ordate" class="col-md-1 col-form-label text-md-left">{{ __('OR Date') }}</label>
      <div class="col-md-2">
        <div class="input-group mb-3">
          <input id="or_date" type="text" class="form-control form-control-sm" name="or_date" value="{{ $now = date('m/d/Y') }}" style="background-color:#99ccff!important;" required >
            <!-- <span data-feather="calender"></span> -->
        </div>
      </div>

      <label for="or_number" class="col-md-1 col-form-label text-md-left">{{ __('OR Number') }}</label>

      <div class="col-md-3">
        <div class="input-group mb-3">
          <input id="or_number" type="text" class="form-control form-control-sm is-valid" name="or_number" value=""  required>
          <div class="input-group-append">
            <button class="btn btn-danger btn-sm" id="btn_check_duplicate">Check Availability</button>
          </div>
          <div class="invalid-feedback">
            Please provide a valid OR Number.
          </div>
          @if ($errors->has('or_number'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('or_number') }}</strong>
            </span>
          @endif
        </div>
        <input type="hidden" name="or_number_only" value="">
      </div>

      <!-- <div class="col-md-3">
        @if (count($payments) > 0)
          @foreach ($payments as $payment)
            <input id="or_number" type="text" class="form-control form-control-sm" name="or_number" value="{{ $payment->or_prefix . $payment->next_or_number }}" style="background-color:#99ccff!important;" required >
            <input type="hidden" name="or_number_only" value="{{ $payment->next_or_number }}">
          @endforeach

        @else
          @foreach ($payments as $payment)
            <input id="or_number" type="text" class="form-control form-control-sm" name="or_number" value="{{ $payment->or_prefix . '0000001' }}" style="background-color:#99ccff!important;" required >
            <input type="hidden" name="or_number_only" value="{{ '0000001' }}">
          @endforeach

        @endif

        @if ($errors->has('or_number'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('or_number') }}</strong>
          </span>
        @endif
      </div> -->

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
            @foreach ($discounts as $discount)
              <option value="{{ $discount->id }}">{{ $discount->discount_name }}</option>
            @endforeach
            <!-- <option value="SENIOR">Senior Citizen</option>
            <option value="10">10% Discount</option>
            <option value="20">20% Discount</option>
            <option value="25">25% Discount</option>
            <option value="50">50% Discount</option>
            <option value="75">75% Discount</option>
            <option value="100">100% Discount</option> -->
          </select>
      </div>

      <label for="amount_paid" class="col-md-2 col-form-label text-md-right">{{ __('Total Amount') }}</label>
      <div class="col-md-2">
        <input id="amount_paid" type="text" class="form-control" name="amount_paid" style="background-color:#99ccff!important; font-weight: bold; font-size: 25px;" value="0.00" >
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
        <input id="amount_tendered" type="text"  class="form-control" name="amount_tendered"  style="background-color:#99ccff!important; font-weight: bold; font-size: 25px;" value="0.00" >

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
        <button type="button" name="clear_discount" id="clear_discount" class="btn btn-outline-dark btn-sm">
          Clear Discount
        </button>
      </div>

      <label for="amount_change" class="col-md-2 col-form-label text-md-right" style="font-size: 13px">{{ __('Change') }}</label>

      <div class="col-md-2">
        <input id="amount_change" type="text" class="form-control" name="amount_change" style="font-weight: bold; font-size: 25px;" value="0.00" >
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
          <th style="text-align: right;">Unit Price</th>
          <th style="width:8%; text-align: right;">Discount (%)</th>
          <th style="width:10%; text-align: right;">Discount Value</th>
          <th style="width:10%; text-align: right;">Sub-total</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="invoice_data">
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
  $(document).ajaxStart(function(){
    $('#spinner').show();
  });

  $(document).ajaxComplete(function(){
    $('#spinner').hide();
  });


  $(document).ready(function() {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var editor = new SimpleTableCellEditor("invoice_table");
    editor.SetEditableClass("editMe");
    var d_array = '';

    // Select charge slip number content
    $('#charge_slip_number').on('click', function(){
      $(this).select();
    });

    // Search for walk-in charges by its charge slip number .
    $('#post_data').click(function(e) {
      e.preventDefault();

      var _token = CSRF_TOKEN;
      var charge_slip_number = $('#charge_slip_number').val();
      var user_id = $('#user_id').val();
      var duplicate = '';
      var or_number = $('#or_number').val();

      if (charge_slip_number == '') {
        alert('Please input charge slip number .');

      } else {

        duplicate = checkDuplicateChargeSlipNumber(charge_slip_number);
        if (duplicate == true) {
          alert('Please input another charge slip number / barcode (Duplicate Entry)');

        } else {
          // A function that will search and get walk-in charges .
          searchWalkinCharges(_token, charge_slip_number, user_id, or_number);

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

    function searchWalkinCharges( _token, cnumber, uid, orn ) {
      var discount = 0;
      var content = '';
      var date_created = '';
      var charge_slip_number = '';
      var product_id = '';
      var product_description = '';
      var quantity = 0;
      var unit_price = 0;
      var discount_percent = 0;
      var discount_value = 0;
      var sub_total = 0;
      var status = '';
      var row_number = 0;
      var id = 0;
      var is_pay = '';
      var is_discount = '';
      var charge_code = '';
      var or_count = 0;

      $.ajax({
        type: "POST",
        url: "/collections/walkin/create/search-walkin-charges",
        data: { _token: _token, charge_slip_number: cnumber, user_id: uid, or_number: orn  },
        dataType: "JSON",
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Please provide a valid charge slip number / barcode .');
        },
        success: function(data) {
          // var total_discount_value = 0;
          // var total_amount = Number(0);
          discount = data.discount;

          d_array = discount;
          // console.log(data);
          // console.log(data.charge_slip_number);
          // console.log(data.user_id);
          // console.log(data.patient_name);
          // console.log(data.discount_id);
          // console.log(discount);
          // console.log(d_array);
          $('#discount_array').val(discount);

          // $('#invoice_data').empty();
          $.each(data.data, function(i, value) {
            id = value.id;
            date_created = value.date_created_formatted;
            charge_slip_number = value.charge_slip_number;
            product_id = value.product_id;
            product_description = value.product_description;
            quantity = Number(value.quantity);
            unit_price = Number(value.unit_price);
            sub_total = Number(value.amount);
            discount_percent = Number(value.discount_percent);
            discount_value = Number(value.discount_value);
            is_pay = value.is_pay;
            is_discount = value.is_discount;
            status = 'Invoiced';
            patient_id = value.patient_id;
            patient_name = value.patient_name;
            charge_code = value.charge_code;

            if (is_pay == null || is_pay == 1) { is_pay = 'checked'; }
            else { is_pay = ''; }

            if (is_discount == null || is_discount == 0) {  is_discount = '';  }
            else {  is_discount = 'checked';   }

            $('#patient_id').val(patient_id);
            $('#patient_name').val(patient_name);
            $('#charge_code').val(charge_code);
            content = '<tr id="'+ id +'" class="charge_items">';
            content += '<td><input type="checkbox" name="pay_checkbox" id="'+ id +'" class="pay_checkbox" value="'+ id +'"'+ is_pay +'></td>';
            content += '<td><input type="checkbox" name="discount_checkbox" id="'+ id +'" class="discount_checkbox" value="'+ id +'"'+  is_discount +'></td>';
            content += '<td>'+ date_created +'</td>';
            content += '<td class="charge_slip_number">'+ charge_slip_number +'</td>';
            content += '<td id="'+ product_id +'">'+ product_description +'</td>';
            content += '<td align="right" class="quantity">'+ quantity.toFixed(2) +'</td>';
            content += '<td align="right" class="unit_price">'+ unit_price.toFixed(2) +'</td>';
            content += '<td align="right" class="discount_percent">'+ discount_percent.toFixed(2) +'</td>';
            content += '<td align="right" class="discount_value editMe">'+ discount_value.toFixed(2) +'</td>';
            content += '<td align="right" class="sub_total">'+ sub_total.toFixed(2) +'</td>';
            content += '<td>'+ status +'</td>';
            content += '</tr>';

            $(content).appendTo('#invoice_data');
          });
          // content = '<tr>';
          // content += '<td colspan="8" align="right">Totals: </td>';
          // content += '<td align="right"><strong>'+ total_discount_value.toFixed(2) +'</strong></td>';
          // content += '<td align="right"><strong>'+ total_amount.toFixed(2) +'</strong></td>';
          // content += '</tr>';
          // $(content).appendTo('#invoice_data');
          // alert(total_amount);
          // $('#amount_paid').val(total_amount.toFixed(2));

          $('#amount_paid').val(calculateTotalAmount().toFixed(2));
        }
      });
    }

    function calculateTotalAmount() {
      var sub_total = 0;
      var total = 0;

      $('.charge_items').each(function(){
        sub_total = Number($(this).closest('tr').find('.sub_total').text());
        total += sub_total;
      });

      // $('#total_amount').val(total.toFixed(2));
      return total;
    }

    $('#charge_slip_number').on('keydown', function(event) {
      if (event.which == 13) {
        $('#post_data').click();
      }
    });


    $('#apply_discount_all').on('click', function(event){
      event.preventDefault();

      var _token = CSRF_TOKEN;
      var discount_id = $('#discount_percent').val();
      var discount = d_array;
      var discount_percent = getDiscountPercent(discount, discount_id);
      // var charge_slip_number = $('#charge_slip_number').val();
      // var user_id = $('#user_id').val();
      // var discount_array = $('#discount_array').val();

      // console.log(discount_array);
      // console.log(discount_id);
      // console.log(d_array);
      if (discount_id == '') {
        alert('Please select a discount percent(%) .');

      } else {
        applyDiscountAll(discount_percent);
      }
    });

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
      // console.log(discount_percent);

      $('input[name="discount_checkbox"]:not(:checked)').each(function(){
        $(this).prop('checked', true);
      });

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

      $('#amount_paid').val(calculateTotalAmount().toFixed(2));

    }

    function getDiscountPercent(d_a, d_id){
      var id = 0;
      var percent = 0;
      var discount_percent = 0;

      $.each(d_a, function(i, value){
        id = value.id;
        percent = value.discount_percent;

        if (id == d_id) {
          discount_percent = percent;
        }
      });
      return discount_percent;
    }

    // function getDiscountPercent( token, d_id ) {
    //   $.ajax({
    //     type: "POST",
    //     url: "/collections/walkin/create/get-discount-percent",
    //     data: { _token: token, discount_id: d_id },
    //     dataType: "JSON",
    //     success: successCallBack,
    //     error: function() {
    //       return 0;
    //     }
    //   });
    // }
    //
    // function successCallBack(returnData) {
    //   var discount_percent = Number(returnData.discount_percent);
    //   console.log(discount_percent);
    //   alert(discount_percent);
    //   return discount_percent;
    // }




    $('#apply_discount_selected').on('click', function(event) {
      event.preventDefault();

      var _token = CSRF_TOKEN;
      var discount = d_array;
      var discount_id = $('#discount_percent').val();
      var discount_percent = getDiscountPercent(discount, discount_id);
      var discount_ids = [];

      $('input[name="discount_checkbox"]:checked').each(function(){
        discount_ids.push($(this).val());
      });

      if (discount_id == '') {
        alert('Please select a discount percent(%) .');

      } else {
        if (discount_ids.length > 0) {
          applyDiscountSelected( discount_percent, discount_ids );

        } else {
          alert('Please select at least one(1) discount checkbox .');

        }
      }
    });

    function applyDiscountSelected(d, dids) {
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
      $('#amount_paid').val(calculateTotalAmount().toFixed(2));


      // $.ajax({
      //   type: "POST",
      //   url: "/collections/walkin/create/apply-discount-selected",
      //   data: { _token: _t, discount_id: d_id, charge_slip_number: cslip, id: id, user_id: u_id },
      //   dataType: "JSON",
      //   success: function(data){
      //     _token = data._token;
      //     charge_slip_number = data.charge_slip_number;
      //     user_id = data.user_id;
      //     // console.log(data);
      //     // console.log(data.id);
      //     // console.log(data.token);
      //     // console.log(data.charge_slip_number);
      //     searchWalkinCharges( _token, charge_slip_number, user_id );
      //   }
      // });
    }

    $('#clear_discount').on('click', function(event) {
      event.preventDefault();

      var q = confirm('Are you sure you want to clear the discount(s) ?');
      if (q == true) {
        clearDiscount();
      }

    });

    function clearDiscount() {
      var sub_total = 0;
      var blank_value = Number(0).toFixed(2);

      $('input[name="pay_checkbox"]:not(:checked)').each(function(){
        $(this).prop("checked", true);
      });

      $('input[name="discount_checkbox"]:checked').each(function(){
        $(this).prop("checked", false);
      });

      $('.alert').hide();
      $('#discount_percent').val('');
      $('#amount_paid').val(blank_value);
      $('#amount_tendered').val(blank_value);
      $('#amount_change').val(blank_value);

      $('.charge_items').each(function(){
        product_id = $(this).closest('tr').find('td:eq(4)').attr('id');
        quantity = Number($(this).closest('tr').find('.quantity').text());
        unit_price = Number($(this).closest('tr').find('.unit_price').text());
        sub_total = quantity * unit_price;
        discount_percent_value = blank_value;
        discount_value = blank_value;

        $(this).closest('tr').find('.discount_percent').text(discount_percent_value);
        $(this).closest('tr').find('.discount_value').text(discount_value);
        $(this).closest('tr').find('.sub_total').text(sub_total.toFixed(2));
      });

      $('#amount_paid').val(calculateTotalAmount().toFixed(2));
    }

    $('#update_totals').on('click', function(event) {
      event.preventDefault();

      var discount = d_array;
      var discount_id = $('#discount_percent').val();
      var discount_percent = getDiscountPercent(discount, discount_id);
      var q = confirm('Are you sure you want to update the total charges ?');

      var row_id = 0;
      var product_id = '';

      if (q == true) {
        console.log(discount_percent);
        updateTotals( discount_percent );
      }
    });

    function updateTotals(d) {
      var discount = Number(d);
      var quantity = 0;
      var unit_price = 0;
      var discount_percent = 0;
      var discount_value = 0;
      var sub_total = 0;

      $('.charge_items').each(function(){
        var is_pay = 0;
        var is_discount = 0;
        var disc = discount;
        var row = $(this);

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

            } //check if discount checkbox is checked
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
          } // check if the value of  discount value is zero
        } // check if free product

        $(this).closest('tr').find('.discount_percent').text(discount_percent.toFixed(2));
        $(this).closest('tr').find('.discount_value').text(discount_value.toFixed(2));
        $(this).closest('tr').find('.sub_total').text(sub_total.toFixed(2));
      });

      $('#amount_paid').val(calculateTotalAmount().toFixed(2));
    } // Update Totals




    $('#save_button').on('click', function(event) {
      event.preventDefault();

      var _token = CSRF_TOKEN;
      var or_number = $('#or_number').val();
      var charge_slip_number = $('#charge_slip_number').val();
      var row_count = $('#invoice_table tbody tr').length;

      // Check the value of charge slip number/barcode .
      if (charge_slip_number == '') {
        alert('Please input charge slip number / barcode .');

      } else {
        // Check the value of patient name input .
        if (patient_name == '') {
          alert('Please input valid charge slip number / barcode .');

        } else {
          // Save payment function
          saveWalkinCharges(_token, or_number, row_count );
        }
      }

      // var _token = CSRF_TOKEN;
      // var date = new Date();
      // var month = date.getMonth() + 1;
      // var day = date.getDate();
      // var hour = date.getHours();
      // var minute = date.getMinutes();
      // var second = date.getSeconds();
      //
      // var charge_slip_number_value = $('#charge_slip_number').val();
      // var patient_name_value = $('#patient_name').val();
      // var or_date_value = $('#or_date').val();
      // var user_id_value = $('#user_id').val();
      // var prefix_or_number_value = $('#or_number').val();
      // var payment_mode_value = $('#payment_mode').val();
      // var discount_id_value = $('#discount_percent').val();
      // var currency_value = $('#currency').val();
      // var payment_type_value = $('#payment_type').val();
      // var discount_computation_value = $('#discount_computation').val();
      // var amount_paid_value = $('#amount_paid').val();
      // var amount_tendered_value = $('#amount_tendered').val();
      // var amount_change_value = $('#change').val();
      // // var charge_code_value = $('#charge_code').val();
      // // var charge_table_value = $('#charge_table').val();
      // var created_at_value = date.getFullYear() + '-' + (('' + month).length < 2 ? '0' : '') +
			// 		month + '-' + (('' + day).length < 2 ? '0' : '') + day + ' ' + hour + ':' + minute + ':' + second;
      // var row_count = 0;
      // var obj = {};
      // var arrData = [];
      //
      //
      // if (charge_slip_number_value == '' || charge_slip_number_value == null) {
      //   alert('Please input charge slip number .');
      //   $('#charge_slip_number').focus();
      //
      // } else if (patient_name_value == '' || patient_name_value == null) {
      //   alert('Please fill out patient name .');
      //   $('#patient_name').focus();
      //
      // } else {
      //   var q = confirm('Are you sure you want to save this payment?');
      //
      //   row_count = $('#invoice_table tbody tr').length;
      //
      //   if (row_count > 0) {
      //     if (q == true) {
      //       for (var i = 0; i < row_count - 1; i++) {
      //         obj.charge_slip_number = charge_slip_number_value;
      //         obj.patient_name = patient_name_value;
      //         obj.or_date = or_date_value;
      //         obj.user_id = user_id_value;
      //         obj.prefix_or_number = prefix_or_number_value;
      //         obj.payment_mode = payment_mode_value;
      //         obj.discount_id = discount_id_value;
      //         obj.currency = currency_value;
      //         obj.payment_type = payment_type_value;
      //         obj.discount_computation = discount_computation_value;
      //         obj.amount_paid = amount_paid_value;
      //         obj.amount_tendered = amount_tendered_value;
      //         obj.amount_change = amount_change_value;
      //         // obj.charge_code = charge_code_value;
      //         // obj.charge_table = charge_table_value;
      //         obj.created_at = created_at_value;
      //
      //         arrData.push(obj);
      //       }
      //
      //       saveWalkinCharges(_token, arrData, prefix_or_number_value, charge_slip_number_value);
      //     }
      //
      //   } else {
      //     alert('No walk-in charges found .');
      //   }
      // }

    });

    function saveWalkinCharges(_t, or, rc) {
      var _token = _t;
      var or_number_value = or;
      var row_count = rc;
      var arrData=[];
      var date = new Date();
      var month = date.getMonth() + 1;
      var day = date.getDate();
      var hour = date.getHours();
      var minute = date.getMinutes();
      var second = date.getSeconds();

      var patient_id_value = $('#patient_id').val();
      var patient_name_value = $('#patient_name').val();
      var payment_status_value = 'Paid';
      var or_date_value = $('#or_date').val();
      var user_id_value = $('#user_id').val();
      var payment_mode_value = $('#payment_mode').val();
      var discount_id_value = $('#discount_percent').val();
      var currency_value = $('#currency').val();
      var payment_type_value = $('#payment_type').val();
      var discount_computation_value = $('#discount_computation').val();
      var amount_paid_value = Number($('#amount_paid').val());
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
        obj.patient_id = patient_id_value;
        obj.patient_name = patient_name_value;
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

      var q = confirm('Are you sure you want to save this payment ?');
      if (q == true) {
        $.ajax({
          type: "POST",
          url: "/collections/walkin/create/check-or-duplicate",
          data: { _token: _t, arrData: arrData, or_number: or_number_value },
          dataType: "JSON",
          success: function(data) {
            var _token = data._token;
            var or_count = data.data;
            var or_number = data.or_number;
            var arrData = data.arrData;
            if (or_count > 0) {
              // console.log('duplicate OR');
              alert('Please use another OR Number (Duplicate Entry) .');

            } else {
              $.ajax({
                type: "POST",
                url: "/collections/walkin/create/save-walkin-charges",
                data: { _token: _token, arrData: arrData, or_number: or_number_value },
                dataType: "JSON",
                success: function(data){
                  $('#alert_value').val('Payment was successfully saved.');
                   $('.alert').show();
                   $('#btn_new').show();
                   $('#print_button').show();
                   $('#print_button').click();
                   $('#save_button').hide();
                }
              }); // End of  ajax url:"/collections/other/store_payment",
            }
          }
        });
      } // end of confirmation
    }

    $('#print_button').on('click', function(event){
      event.preventDefault();

      // alert('clicked print button');
      var prefix_or_number = $('#or_number').val();
      window.location.replace("/collections/walkin/create/print-pdf/" + prefix_or_number);

    });

    function computeChange(){

      var amount_paid = Number($('#amount_paid').val());
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
        $('#amount_change').focus();
      }
    });

    $('#collections_walkin').on('keypress', function(e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
        e.preventDefault();
        return false;
      }
    });

    $('#btn_new').click(function(e){
      e.preventDefault();

      var user_id = $('#user_id').val();

      window.location.replace("/collections/walkin/create/" + user_id);
    });


  });
</script>

@endsection
