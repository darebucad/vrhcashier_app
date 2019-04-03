@extends ('layouts.master')

@section ('content')
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h5"><a href="{{ url('collections/walkin') }}">Walk-In Payment</a> / Create Walk-In Payment</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <input  type="text" name="charge_slip_number" id="charge_slip_number" class="form-control form-control-sm"  placeholder="Charge Slip Number"  style="background-color:#99ccff!important;" required autofocus>
      </div>
      <div class="btn-group mr-2">
        <button id="post_data" class="btn btn-outline-info pull-right btn-sm">
          Search
        </button>
      </div>

      <div class="btn-group mr-2">
        <button class="btn btn-sm btn-outline-secondary" id ="print_button" style="display: none;">
          <span data-feather="printer"></span>
          Print Receipt
        </button>
        <button class="btn btn-sm btn-outline-secondary" id ="export_button" style="display: none;">Export</button>
      </div>

    </div>
  </div>


  <div class="alert alert-success alert-dismissable" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      Payment was successfully saved.
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
      <button class="btn btn-sm btn-primary" id="save_button">Save</button>
      <p id="button-cancel">or <a class="btn-link" href="{{ url('collections/walkin') }}">Cancel</a></p>
    </div>

    <br />
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

    <!-- Patient Name Input-->
    <div id="patient_name_field" class="form-group row">
      <div class="input-group">
        <label for="patient_name" class="col-md-1 col-form-label text-md-left">{{ __('Patient Name') }}</label>
        <div class="col-md-9">
          <input type="text" name="patient_name" id="patient_name" value="" class="form-control form-control-sm" style="background-color:#99ccff!important;" required>
        </div>
      </div>
    </div>

    <!-- OR Date / OR Number Input -->
    <div class="form-group row" style="margin-top: -10px">
      <label for="ordate" class="col-md-1 col-form-label text-md-left">{{ __('OR Date') }}</label>
      <div class="col-md-2">
        <div class="input-group mb-3">
          <input id="or_date" type="text" class="form-control form-control-sm" name="or_date" value="{{ $now = date('m/d/Y') }}" style="background-color:#99ccff!important;" required autofocus>
            <!-- <span data-feather="calender"></span> -->
        </div>
      </div>

      <label for="or_number" class="col-md-1 col-form-label text-md-left">{{ __('OR Number') }}</label>

      <div class="col-md-3">
        @if (count($payments) > 0)
          @foreach ($payments as $payment)
            <input id="or_number" type="text" class="form-control form-control-sm" name="or_number" value="{{ $payment->or_prefix . '-' . $payment->next_or_number }}" style="background-color:#99ccff!important;" required autofocus>
            <input type="hidden" name="or_number_only" value="{{ $payment->next_or_number }}">
          @endforeach

        @else
          <input id="or_number" type="text" class="form-control form-control-sm" name="or_number" value="{{ '0000001' }}" style="background-color:#99ccff!important;" required autofocus>
          <input type="hidden" name="or_number_only" value="{{ '0000001' }}">

        @endif

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
        <input id="amount_paid" type="text" class="form-control" name="amount_paid" style="background-color:#99ccff!important; font-weight: bold; font-size: 25px;" value="0.00" autofocus>
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
        <input id="amount_tendered" type="text"  class="form-control" name="amount_tendered"  style="background-color:#99ccff!important; font-weight: bold; font-size: 25px;" value="0.00" autofocus>

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

      <label for="change" class="col-md-2 col-form-label text-md-right" style="font-size: 13px">{{ __('Change') }}</label>

      <div class="col-md-2">
        <input id="change" type="text" class="form-control" name="change" style="font-weight: bold; font-size: 25px;" value="0.00" autofocus>
        @if ($errors->has('change'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('change') }}</strong>
            </span>
        @endif
      </div>
    </div>
  </form>

  <div class="table-responsive">
    <table id="invoice_table" class="table table-sm" style="width: 100%">
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
        @for ($i = 0; $i < 4; $i++)
        <tr>
          <td colspan="11" style="color:white;"> .</td>
        </tr>
        @endfor
      </tbody>
    </table>
  </div>
</main>

<script type="text/javascript">


</script>

<script type="text/javascript">
  $(document).ajaxStart(function(){
    $('#spinner').show();
  });

  $(document).ajaxComplete(function(){
    $('#spinner').hide();
  });

  $(document).ready(function() {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    // Search for walk-in charges by its charge slip number .
    $('#post_data').click(function() {
      var charge_slip_number = $('#charge_slip_number').val();
      var user_id = $('#user_id').val();
      var _token = CSRF_TOKEN;

      if (charge_slip_number == '') {
        alert('Please input charge slip number .');

      } else {
        // A function that will search and get walk-in charges .
        searchWalkinCharges(_token, charge_slip_number, user_id);
      }
    });

    $('#charge_slip_number').on('keydown', function(event) {
      if (event.which == 13) {
        $('#post_data').click();

      }
    });

    function searchWalkinCharges( _token, cnumber, uid ) {
      $.ajax({
        type: "POST",
        url: "/collections/walkin/create/search-walkin-charges",
        data: { _token: _token, charge_slip_number: cnumber, user_id: uid  },
        dataType: "JSON",
        success: function(data) {
          // console.log(data);
          // console.log(data.charge_slip_number);
          // console.log(data.user_id);
          // console.log(data.patient_name);
          // console.log(data.discount_id);

          var patient_name = data.patient_name.patient;
          var content = '';
          var date_created = '';
          var charge_slip_number = '';
          var product_code = '';
          var product_description = '';
          var quantity = 0;
          var unit_price = 0;
          var discount_percent = 0;
          var discount_value = 0;
          var sub_total = 0;
          var status = '';
          var total_discount_value = 0;
          var total_amount = Number(0);
          var row_number = 0;
          var id = 0;
          var is_pay = '';
          var is_discount = '';
          // var discount_id = data.discount_id;

          $('#patient_name').val(patient_name);
          // $('#discount_percent').val(discount_id);

          $('#invoice_data').empty();
          $.each(data.data, function(i, value) {
            // alert(total_amount);
            date_created = value.date_created;
            charge_slip_number = value.chargeslipno;
            product_code = value.itemcode;
            product_description = value.description;
            quantity = Number(value.qty);
            unit_price = Number(value.price);
            discount_percent = Number(value.discount_percent);
            discount_value = Number(value.discount_value);
            total = Number(value.total);
            sub_total = Number(value.sub_total);
            is_pay = value.is_pay;
            is_discount = value.is_discount;
            status = 'Invoiced';
            id = value.id;

            // Discount percent value
            if (discount_percent == null || discount_percent == '' || discount_percent == 0) {
              discount_percent = Number(0);
            }

            // Discount value
            if (discount_value == null || discount_value == '' || discount_percent == 0) {
              discount_value = Number(0);
            }

            // alert(sub_total);

            // Sub total value
            if (sub_total == 0 && (discount_percent == 100 || is_pay == 0)) {

            } else if (sub_total == 0) {
              sub_total = total;

            } else {
              // sub_total = Number(value.sub_total);
            }

            if (is_pay == null || is_pay == 1) {
              is_pay = 'checked';

            } else {
              is_pay = '';
            }

            if (is_discount == null || is_discount == 0) {
              is_discount = '';

            } else {
              is_discount = 'checked';
            }

            total_discount_value += discount_value;
            total_amount += sub_total;

            content = '<tr id="'+ row_number +'">';
            content += '<td><input type="checkbox" name="pay_checkbox" id="'+ id +'" class="pay_checkbox" value="'+ row_number +'"'+ is_pay +'></td>';
            content += '<td><input type="checkbox" name="discount_checkbox" id="'+ id +'" class="discount_checkbox" value="'+ row_number +'"'+  is_discount +'></td>';
            content += '<td>'+ date_created +'</td>';
            content += '<td>'+ charge_slip_number +'</td>';
            content += '<td>'+ product_description +'</td>';
            content += '<td align="right">'+ quantity.toFixed(2) +'</td>';
            content += '<td align="right">'+ unit_price.toFixed(2) +'</td>';
            content += '<td align="right">'+ discount_percent.toFixed(2) +'</td>';
            content += '<td align="right">'+ discount_value.toFixed(2) +'</td>';
            content += '<td align="right">'+ sub_total.toFixed(2) +'</td>';
            content += '<td>'+ status +'</td>';
            content += '</tr>';
            $(content).appendTo('#invoice_data');

            row_number = row_number + 1;
          });
          content = '<tr>';
          content += '<td colspan="8" align="right">Totals: </td>';
          content += '<td align="right"><strong>'+ total_discount_value.toFixed(2) +'</strong></td>';
          content += '<td align="right"><strong>'+ total_amount.toFixed(2) +'</strong></td>';
          content += '</tr>';
          $(content).appendTo('#invoice_data');

          // alert(total_amount);

          $('#amount_paid').val(total_amount.toFixed(2));

        }
      });
    }

    $('#apply_discount_all').on('click', function(event){
      event.preventDefault();

      var _token = CSRF_TOKEN;
      var discount_id = $('#discount_percent').val();
      var charge_slip_number = $('#charge_slip_number').val();
      var user_id = $('#user_id').val();

      if (discount_id == '') {
        alert('Please select a discount percent .');

      } else {
        applyDiscountAll(_token, discount_id, charge_slip_number, user_id);
      }
    });

    function applyDiscountAll(_t, d_id, cnumber, u_id) {
      $.ajax({
        type: "POST",
        url: "/collections/walkin/create/apply-discount-all",
        data: { _token: _t, discount_id: d_id, charge_slip_number: cnumber, user_id: u_id },
        dataType: "JSON",
        success: function(data) {
          // console.log(data);
          // console.log(data.discount_id);
          // console.log(data.charge_slip_number);
          // console.log(data.user_id);
          // console.log(data.token);

          discount_id = data.discount_id;
          charge_slip_number = data.charge_slip_number;
          user_id = data.user_id;
          _token = data.token;

          searchWalkinCharges( _token, charge_slip_number, user_id );
        }
      });
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
      var discount_id = $('#discount_percent').val();
      var charge_slip_number = $('#charge_slip_number').val();
      var user_id = $('#user_id').val();
      var id = [];
      var row_id = 0;
      var product_id = '';

      $('input[name="discount_checkbox"]:checked').each(function(){
        row_id = $(this).val();
        product_id = $('#invoice_table').find('tr#' + row_id).find('.discount_checkbox').attr('id');
        id.push(product_id);
      });


      if (discount_id == '') {
        alert('Please select a discount percent .');
      } else {
        if (id.length > 0) {
          applyDiscountSelected(  _token, discount_id, charge_slip_number, id, user_id );

        } else {
          alert('Please select at least one(1) discount checkbox .');
        }
      }
    });

    function applyDiscountSelected(_t, d_id, cslip, id, u_id) {
      $.ajax({
        type: "POST",
        url: "/collections/walkin/create/apply-discount-selected",
        data: { _token: _t, discount_id: d_id, charge_slip_number: cslip, id: id, user_id: u_id },
        dataType: "JSON",
        success: function(data){
          _token = data._token;
          charge_slip_number = data.charge_slip_number;
          user_id = data.user_id;
          // console.log(data);
          // console.log(data.id);
          // console.log(data.token);
          // console.log(data.charge_slip_number);
          searchWalkinCharges( _token, charge_slip_number, user_id );
        }
      });
    }

    $('#clear_discount').on('click', function(event) {
      event.preventDefault();
      var _token = CSRF_TOKEN;
      var charge_slip_number = $('#charge_slip_number').val();
      var user_id = $('#user_id').val();
      var discount_id = null;
      var q = confirm('Do you want to clear the discount(s)?');
      var discount_percent = '';
      var default_value = '0.00';

      if (q == true) {
        clearDiscount( _token, discount_id, charge_slip_number, user_id );

        $('#discount_percent').val(discount_percent);
        $('#amount_paid').val(default_value);
        $('#amount_tendered').val(default_value);
        $('#change').val(default_value);

      }
    });

    function clearDiscount(_t, d_id, cslip, u_id) {
      $.ajax({
        type: "POST",
        url: "/collections/walkin/create/clear-discount",
        data: { _token: _t, discount_id: d_id, charge_slip_number: cslip, user_id: u_id },
        dataType: "JSON",
        success: function(data) {
          // console.log(data);
          // console.log(data.token);
          // console.log(data.charge_slip_number);
          // console.log(data.user_id);

          _token = data.token;
          charge_slip_number = data.charge_slip_number;
          user_id = data.user_id;

          searchWalkinCharges(_token, charge_slip_number, user_id);
        }
      });
    }

    $('#update_totals').on('click', function(event) {
      event.preventDefault();

      var _token = CSRF_TOKEN;
      var charge_slip_number = $('#charge_slip_number').val();
      var user_id = $('#user_id').val();
      var discount_id = $('#discount_percent').val();
      var q = confirm('Are you sure you want to update the total amount?');

      var q = true;
      var pay_ids = [];
      var discount_ids = [];
      var row_id = 0;
      var product_id = '';

      if (q == true) {
        $('input[name="pay_checkbox"]:not(:checked)').each(function(){
          row_id = $(this).val();
          product_id = $('#invoice_table').find('tr#' + row_id).find('.pay_checkbox').attr('id');
          pay_ids.push(product_id);
        });

        $('input[name="discount_checkbox"]:checked').each(function(){
          row_id = $(this).val();
          product_id = $('#invoice_table').find('tr#' + row_id).find('.discount_checkbox').attr('id');
          discount_ids.push(product_id);
        });

        updateTotals( _token, charge_slip_number, user_id, discount_id, pay_ids, discount_ids );
      }
    });

    function updateTotals(_t, cslip, u_id, d_id, pids, dids) {

      $.ajax({
        type: "POST",
        url: "/collections/walkin/create/update-totals",
        data: { _token: _t, charge_slip_number: cslip, user_id: u_id, discount_id: d_id, pay_ids: pids, discount_ids: dids },
        dataType: "JSON",
        success: function(data) {
          // console.log(data);

          _token = data.token;
          charge_slip_number = data.charge_slip_number;
          user_id = data.user_id;

          searchWalkinCharges(_token,charge_slip_number,user_id);

        }
      });
    }


    $('#save_button').on('click', function(event) {
      event.preventDefault();
      // alert('clicked save button');

      var _token = CSRF_TOKEN;
      var date = new Date();
      var month = date.getMonth() + 1;
      var day = date.getDate();
      var hour = date.getHours();
      var minute = date.getMinutes();
      var second = date.getSeconds();

      var charge_slip_number_value = $('#charge_slip_number').val();
      var patient_name_value = $('#patient_name').val();
      var or_date_value = $('#or_date').val();
      var user_id_value = $('#user_id').val();
      var prefix_or_number_value = $('#or_number').val();
      var payment_mode_value = $('#payment_mode').val();
      var discount_id_value = $('#discount_percent').val();
      var currency_value = $('#currency').val();
      var payment_type_value = $('#payment_type').val();
      var discount_computation_value = $('#discount_computation').val();
      var amount_paid_value = $('#amount_paid').val();
      var amount_tendered_value = $('#amount_tendered').val();
      var amount_change_value = $('#change').val();
      // var charge_code_value = $('#charge_code').val();
      // var charge_table_value = $('#charge_table').val();
      var created_at_value = date.getFullYear() + '-' + (('' + month).length < 2 ? '0' : '') +
					month + '-' + (('' + day).length < 2 ? '0' : '') + day + ' ' + hour + ':' + minute + ':' + second;
      var row_count = 0;


      var obj = {};

      var arrData = [];



      if (charge_slip_number_value == '' || charge_slip_number_value == null) {
        alert('Please input charge slip number .');

      } else if (patient_name_value == '' || patient_name_value == null) {
        alert('Please fill out patient name .');

      } else {
        var q = confirm('Are you sure you want to save this payment?');

        row_count = $('#invoice_table tbody tr').length;

        if (row_count > 0) {
          if (q == true) {
            for (var i = 0; i < row_count - 1; i++) {
              obj.charge_slip_number = charge_slip_number_value;
              obj.patient_name = patient_name_value;
              obj.or_date = or_date_value;
              obj.user_id = user_id_value;
              obj.prefix_or_number = prefix_or_number_value;
              obj.payment_mode = payment_mode_value;
              obj.discount_id = discount_id_value;
              obj.currency = currency_value;
              obj.payment_type = payment_type_value;
              obj.discount_computation = discount_computation_value;
              obj.amount_paid = amount_paid_value;
              obj.amount_tendered = amount_tendered_value;
              obj.amount_change = amount_change_value;
              // obj.charge_code = charge_code_value;
              // obj.charge_table = charge_table_value;
              obj.created_at = created_at_value;

              arrData.push(obj);
            }
            saveWalkinCharges(_token, arrData, prefix_or_number_value, charge_slip_number_value);
          }

        } else {
          alert('No walk-in charges found .');

        }
      }
    });

    function saveWalkinCharges(_t, d, or_n, cslip) {
      $.ajax({
        type: "POST",
        url: "/collections/walkin/create/save-walkin-charges",
        data: { _token: _t, data: d, or_number: or_n, charge_slip_number: cslip },
        dataType: "JSON",
        success: function(data) {
          console.log(data);
          console.log(data.is_pay);

          $('.alert').show();

          $('#charge_slip_number').hide();
          $('#post_data').hide();

          $('#print_button').show();
          $('#export_button').show();

          $('#print_button').click();

        }
      });
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
        $('#change').val(Number(0.00).toFixed(2));
      } else {
        compute = amount_tendered - amount_paid;
        $('#amount_tendered').val(Number(amount_tendered).toFixed(2));
        $('#change').val(Number(compute).toFixed(2));
      }
    }

    $('#amount_tendered').keydown(function(event){
      // event.preventDefault();
      if (event.which == 13) {
        // alert('pressend enter at amount tendered field');
        computeChange();
        $('#change').focus();
      }
    });

    $('#collections_walkin').on('keyup keypress', function(e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
        e.preventDefault();
        return false;
      }
    });


  });
</script>

@endsection
