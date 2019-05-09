@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

    <h1 class="h5"><a href="{{ url('collections/outpatient') }}">Out-Patient Payment</a> / Create Out-Patient Payment</h1>

    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <input  type="text" name="search_barcode" id="search_barcode" class="form-control form-control-sm"  placeholder="Charge Slip / Barcode"  style="background-color:#99ccff!important;" required autofocus>
      </div>

      <div class="btn-group mr-2">
        <button id="post_data" class="btn btn-outline-info pull-right btn-sm">
          Search
        </button>
      </div>

    </div>
  </div>

  <!-- <div class="alert alert-success alert-dismissable" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      This charge slip was already paid.
  </div> -->

  <div class="alert alert-success alert-dismissable" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      Payment was successfully saved.
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
    <h6>Loading...</h6>
  </div>

  <form id="collections_outpatient" action="/collections/outpatient/create/payment" method="post">
    @csrf

    <div class="row" style="margin-top:10px;">
      <button type="submit" class="btn btn-sm btn-primary" id="button_save">Save</button>
        <p id="button-cancel">or <a class="btn-link" href="{{ url('collections/outpatient') }}">Cancel</a></p>

       <div class="col-md-4 offset-md-4">
        <!-- <a href = "{{ url('/collections/outpatient/print/pdf') }}" class="btn btn-sm btn-outline-danger">

          Print Receipt
        </a> -->
        <!-- <a href="{{ url('/collections/outpatient/print/pdf', ['' => 'P18-001028']) }}" class="btn btn-danger btn-sm">Print Receipt</a> -->
      </div>
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
          <input id="ordate" type="text" class="form-control form-control-sm" name="ordate" value="{{ $now = date('m/d/Y') }}" style="background-color:#99ccff!important;" required autofocus>
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
        <button type="button" name="update_total" id="update_total" class="btn btn-outline-dark btn-sm">
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
          <th style="text-align: right;">Unit Cost</th>
          <th style="width:8%; text-align: right;">Discount (%)</th>
          <th style="width:10%; text-align: right;">Discount Value</th>
          <th style="width:10%; text-align: right;">Sub-total</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="charge-info">
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

    $('#post_data').click(function(){
      var charge_slip = $('#search_barcode').val();
      var user_id = $('#user_id').val();
      var content;
      var discount = 0;
      var total_discount_value = 0;
      var total = 0;
      var is_discount = 0;
      var is_paid = 0;
      var is_pay = 0;
      var account_number = 0;

      if (charge_slip == '' || charge_slip == null || charge_slip == 0) {
        alert('Please input charge slip number / barcode .');

      } else {

        $.ajax({
          type: "POST",
          url: "/collections/outpatient/create/post_data",
          data: { _token: CSRF_TOKEN, charge_slip: charge_slip, user_id: user_id },
          dataType: "JSON",
          error: function(jqXHR, textStatus, errorThrown) {
              alert('Please provide a valid charge slip number / barcode');
          },
          success: function(data) {
            console.log(data);
            console.log(data.patient_record);
            console.log(data.charge_slip);

            account_number = data.new_account_number;
            // is_paid = data.is_paid;
            // if (is_paid == '1') {
            //   $('.alert').show();
            //
            // } else {
            $('#acctno').val(account_number);

            // Get list of patient and payment details
            $.each(data.data, function(i, data) {
              $('#patient_name').val(data.patient_name);
              $('#pcchrgcod').val(data.charge_slip_number);
              $('#enccode').val(data.encounter_code);
              $('#hpercode').val(data.patient_id);
              $('#discount_percent').val(data.disc_name);
              $('#paystat').val('A');
              $('#paylock').val('N');
              $('#updsw').val('N');
              $('#confdl').val('N');
              $('#payctr').val('0');
              $('#status').val('Paid');
            });

              $('#charge-info').empty();
              $.each(data.data, function(i, data) {
                discount_name = data.disc_name;
                discount_percent = data.disc_percent;
                discount_amount = data.computed_discount;
                sub_total = Number(data.computed_sub_total);
                amount = Number(data.amount);

                is_discount = data.is_discount;
                is_pay = data.is_pay;

                if (discount_name == null || discount_name == '') {
                  discount_percent = 0.00;
                  discount_amount = 0.00;
                  total_discount_value = 0.00;
                  sub_total = amount;
                }

                if (is_pay == '0') {
                  is_pay = '';
                  sub_total = 0;

                } else {
                  is_pay = 'checked';

                }

                if (is_discount == '1') {
                  is_discount = 'checked';

                } else {
                  is_discount = '';

                }

                total_discount_value += Number(discount_amount);
                total += Number(sub_total);

                content = '<tr id="'+ data.order_number +'"><td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="' + data.order_number +'" '+ is_pay +'></td>';
                content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="' + data.order_number + '" ' + is_discount + '></td>';
                content += '<td>' + data.order_date_formatted + '</td>';
                content += '<td>' + data.charge_slip_number + '</td>';
                content += '<td>' + data.product_description + '</td>';
                content += '<td align="right">' + data.quantity + '</td>';
                content += '<td align="right">' + data.unit_price + '</td>';
                content += '<td align="right" style="width:8%" class="editMe">' + Number(discount_percent).toFixed(2) + '</td>';
                content += '<td align="right" style="width:10%" class="editMe">' + Number(discount_amount).toFixed(2) + '</td>';
                content += '<td align="right" style="width:10%" class="sub_total">' + Number(sub_total).toFixed(2) + '</td>';
                content += '<td>' + data.invoice_status + '</td></tr>';
                $(content).appendTo('#charge-info');
              });

                content = '<tr><td colspan=8 align="right"><strong>Grand Total: </strong></td>';
                content += '<td colspan=1 align="right">' + Number(total_discount_value).toFixed(2) + '</td>';
                content += '<td colspan=1 align="right" style="font-weight:bold;"><strong>' + Number(total).toFixed(2) + '</strong></td></tr>';
                $(content).appendTo('#charge-info');
                $('#amount_paid').val((total).toFixed(2));
            // }

          }
        });
      }
    });
  });
</script>

<!-- Click apply discount all -->
<script type="text/javascript">
  $(document).ready(function(){
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $("#apply_discount_all").click(function(){
      var charge_slip = $('#search_barcode').val();
      var discount = $('#discount_percent').val();
      var content;
      var discount_value = 0;
      var sub_total = 0;
      var total_discount_value = 0;
      var total = 0;
      var discount_percent = 0;
      var discount_name = '';

      if (discount == '' || discount== 0 || discount == null) {
        alert('Please input discount(%) .');

      } else {
        $.ajax({
          type: "POST",
          url: "/collections/outpatient/create/apply_discount_all",
          data: { _token: CSRF_TOKEN, charge_slip: charge_slip, discount: discount },
          dataType: 'JSON',
          success: function(data){
            console.log(data);
            console.log(data.charge_slip);
            console.log(data.discount_name);

            discount_name = data.discount_name;

            if (discount_name == 'SENIOR' || discount_name == 'PWD'){
              discount_percent = Number(20.00).toFixed(2);

            } else {
              discount_percent = Number(discount).toFixed(2);

            }

            $('#charge-info').empty();
            $.each(data.data, function(i, data){
              discount_value = Number(data.computed_discount);
              sub_total = Number(data.computed_sub_total);
              amount = Number(data.amount);
              // discount_value = Number(value.pcchrgamt * (discount/100));
              // sub_total = Number(value.pcchrgamt - (value.pcchrgamt * (discount/100)));
              if (sub_total == null || sub_total == '') {
                sub_total = 0.00;

              } else {

              }
              total_discount_value += discount_value;
              total += sub_total;
              content = '<tr id="'+ data.order_number +'"><td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="' + data.order_number +'" checked></td>';
              content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="' + data.order_number + '" checked></td>';
              content += '<td>' + data.order_date_formatted + '</td>';
              content += '<td>' + data.charge_slip_number + '</td>';
              content += '<td>' + data.product_description + '</td>';
              content += '<td align="right">' + data.quantity + '</td>';
              content += '<td align="right">' + data.unit_price + '</td>';
              content += '<td align="right">' + Number(discount_percent).toFixed(2) + '</td>';
              content += '<td align="right">' + Number(discount_value).toFixed(2) + '</td>';
              content += '<td align="right" class="sub_total">' + Number(sub_total).toFixed(2)+ '</td>';
              content += '<td>' + data.invoice_status + '</td></tr>';
              $(content).appendTo('#charge-info');
            });
            content = '<tr><td colspan=8 align="right"><strong>Grand Total: </strong></td>';
            content += '<td colspan=1 align="right">' + Number(total_discount_value).toFixed(2) + '</td>';
            content += '<td colspan=1 align="right"><strong>' + Number(total).toFixed(2) + '</strong></td></tr>';
            $(content).appendTo('#charge-info');
            $('#amount_paid').val(Number(total).toFixed(2));
          }
        });
      }
    });
  });
</script>



<script type="text/javascript">

// Apply discount selected
  $(document).ready(function(){
    var CSRF_TOKEN = $('meta[name="csrf_token"]').attr('content');
    $('#apply_discount_selected').click(function(){
      /* declare an checkbox array */
      var id = [];
      var charge_slip = $('#search_barcode').val();
      var discount = $('#discount_percent').val();
      var content;
      var total_discount_value = 0;
      var sub_total = 0;
      var total = 0;
      var is_discount = 0;
      var is_pay = 0;
      var discount_name = '';

      /* look for all checkboxes that have a class 'discount_checkbox' attached to it and check if it was checked */
      $('input[name="discount_checkbox"]:checked').each(function() {
        id.push($(this).val());
      });

      if (discount == '' || discount == 0 || discount == null) {
        alert('Please input discount(%) .');

      } else {

        /* check if there is checkedValues checkboxes, by default the length is 1 as it contains one single comma */
        if (id.length > 0) {
          $.ajax({
          type: "POST",
          url: "/collections/outpatient/create/apply_discount_selected",
          data: { _token: CSRF_TOKEN, charge_slip: charge_slip, ids: id, discount: discount },
          dataType: "JSON",
          success: function(data){
            console.log(data);
            console.log(data.discount_name);

            discount_name = JSON.stringify(data.discount_name);

            // alert(discount_name);

            $('#charge-info').empty();
            $.each(data.data, function(i, data){
              discount_name = data.disc_name;
              discount_percent = data.disc_percent;
              discount_amount = data.computed_discount;
              sub_total = data.computed_sub_total;
              is_discount = data.is_discount;
              amount = data.amount;
              is_pay = data.is_pay;

              if (sub_total == null || sub_total == '') {
                sub_total = amount;

              } else {

              }

              if (is_discount == '1') {
                is_discount = 'checked';

              } else {
                is_discount = '';

              }

              if (is_pay == '0') {
                is_pay = ''
                sub_total = 0;

              } else {
                is_pay = 'checked';

              }

              total_discount_value += Number(discount_amount);
              total += Number(sub_total);
              content = '<tr id="'+ data.order_number +'"><td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="' + data.order_number +'" '+ is_pay +'></td>';
              content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="' + data.order_number + '" ' + is_discount + '></td>';
              content += '<td>' + data.order_date_formatted + '</td>';
              content += '<td>' + data.charge_slip_number + '</td>';
              content += '<td>' + data.product_description + '</td>';
              content += '<td align="right">' + data.quantity + '</td>';
              content += '<td align="right">' + data.unit_price + '</td>';
              content += '<td align="right" style="width:8%">' + Number( discount_percent ).toFixed(2) + '</td>';
              content += '<td align="right" style="width:10%">' + Number( discount_amount ).toFixed(2) + '</td>';
              content += '<td align="right" style="width:10%" class="sub_total">' + Number( sub_total ).toFixed(2) + '</td>';
              content += '<td>' + data.invoice_status + '</td></tr>';
              $(content).appendTo('#charge-info');
            });
            content = '<tr><td colspan=8 align="right"><strong>Grand Total: </strong></td>';
            content += '<td colspan=1 align="right">' + Number( total_discount_value ).toFixed(2) + '</td>';
            content += '<td colspan=1 align="right" ><strong>' + Number( total ).toFixed(2) + '</strong></td></tr>';
            $(content).appendTo('#charge-info');
            $('#amount_paid').val((total).toFixed(2));
          }
        });

        } else {
          alert("Please at least check one of the checkbox");
        }
      }
      // console.dir($checkboxes);
      // alert('Clicked apply discount selected');
      // alert($('#invoice_table:checkbox:checked'));
      // var atLeastOneIsChecked = $('#invoice_table:checkbox:checked').length > 0;
      // alert(atLeastOneIsChecked);
    });
  });
</script>


<!-- click clear discount button -->
<script type="text/javascript">
  $(document).ready(function(){
    var CSRF_TOKEN =  $('meta[name="csrf-token"]').attr('content');
    $('#clear_discount').click(function(){
      var charge_slip = $('#search_barcode').val();
      var discount = 0;
      var q = confirm('Do you want to clear all the discounts made?\nYour changes will be lost if you clear the discounts.');

     if (q == true) {
       $('#discount_percent').val('');
       $('#amount_paid').val('0.00');
       $('#amount_tendered').val('0.00');
       $('#change').val('0.00');

       $.ajax({
          type: "POST",
          url: "/collections/outpatient/create/clear_discount",
          data: { _token: CSRF_TOKEN, charge_slip: charge_slip },
          dataType: "JSON",
          success: function(data) {
            console.log(data);
            var content;
            var discount = 0;
            var discount_value = 0;
            var sub_total = 0;
            var total_discount_value = 0;
            var total = 0;

            $('#charge-info').empty();
            $.each(data.data, function(i, data){
              discount_value = Number(data.amount * (discount/100)).toFixed(2);
              sub_total = Number(data.amount - (data.amount * (discount/100))).toFixed(2);
              total_discount_value += Number(discount_value);
              total += Number(sub_total);
              content = '<tr id="' + data.order_number + '"><td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="' + data.order_number +'" checked></td>';
              content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="' + data.order_number + '"></td>';
              content += '<td>' + data.order_date_formatted + '</td>';
              content += '<td>' + data.charge_slip_number + '</td>';
              content += '<td>' + data.product_description + '</td>';
              content += '<td align="right">' + data.quantity + '</td>';
              content += '<td align="right">' + data.unit_price + '</td>';
              content += '<td align="right">' + Number(discount).toFixed(2) + '</td>';
              content += '<td align="right">' + Number(discount_value).toFixed(2) + '</td>';
              content += '<td align="right" class="sub_total">' + Number(sub_total).toFixed(2) + '</td>';
              content += '<td>' + data.invoice_status + '</td></tr>';
              $(content).appendTo('#charge-info');
            });
            content = '<tr><td colspan=8 align="right"><strong>Grand Total: </strong></td>';
            content += '<td colspan=1 align="right">' + Number(total_discount_value).toFixed(2) + '</td>';
            content += '<td colspan=1 align="right"><strong>' + Number(total).toFixed(2) + '</strong></td></tr>';
            $(content).appendTo('#charge-info');
            $('#amount_paid').val(Number(total).toFixed(2));
          }
        });
     } else {

     }
    });
  });
</script>


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

<script type="text/javascript">
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

  $('#search_barcode').keydown(function(event){
    if (event.which == 13 ) {
      // alert('pressed enter at search barcode field');
      $('#post_data').click();
    }
  });

  $('#amount_tendered').keydown(function(event){
    // event.preventDefault();
    if (event.which == 13) {
      // alert('pressend enter at amount tendered field');
      computeChange();
      // $('#change').focus();
    }
  });

  $('#button_save').keydown(function(event){
    event.preventDefault();
    if (event.which == 13) {
      // alert('pressed enter at button save');
    }
  });





</script>

<!-- <script type="text/javascript">
$(document).ready(function() {
  var editor = new SimpleTableCellEditor("invoice_table");

  var row_id = $('#invoice_table').closest('tr').attr('id');
  editor.SetEditableClass("editMe");

  price = Number($('#invoice_table').find('tr#'+ row_id).find('.unit_cost').val());

  $("#invoice_table").on("cell:edited", function(event) {
    var id = $(this).val();
    console.log(`'${event.oldValue}' changed to '${event.newValue}'`);
    // alert(id);
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

  });
</script> -->





<!-- Update total button clicked event -->
<script type="text/javascript">
  $(document).ready(function() {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    $('#update_total').click(function(){
      var id = [];
      var charge_slip_number = $('#search_barcode').val();
      var discount_percent = 0;
      var total_discount_value = 0;
      var total = 0;
      var discount = $('#discount_percent').val();
      // var q = confirm('Are you sure you want to update the total amount?');

      // var pay_ids = [];
      // var discount_ids = [];

      $('input[name="pay_checkbox"]:not(:checked)').each(function() {
        id.push($(this).val());

      });

      if (id.length > 0) {
        $.ajax({
          type: "POST",
          url: "/collections/outpatient/update_total",
          data: { _token: CSRF_TOKEN, id: id, charge_slip_number: charge_slip_number },
          dataType: "JSON",
          success: function(data) {
            console.log(data);
            // var sub_total = $('#invoice_table').closest('tr').find('.sub_total').val();
            var content;
            var discount = 0;
            var discount_value = 0;
            var sub_total = 0;
            var total_discount_value = 0;
            var total = 0;
            var is_pay = 0;
            var is_discount = 0;

            $('#charge-info').empty();
            $.each(data.data, function(i, data){
              discount = Number(data.disc_percent);
              discount_value = Number(data.amount * (discount/100)).toFixed(2);
              sub_total = Number(data.amount - (data.amount * (discount/100))).toFixed(2);
              is_pay = data.is_pay;
              is_discount = data.is_discount;

              if (discount == '' || discount == null || discount == 0) {
                discount = Number(0.00);

              }

              if (discount_value == '' || discount_value == null || discount_value == 0) {
                discount_value = Number(0.00);

              }

              if (is_pay == '0') {
                is_pay = '';
                sub_total = 0;

              } else {
                is_pay = 'checked';

              }

              if (is_discount == '1') {
                is_discount = 'checked';

              } else {
                is_discount = '';
              }

              total_discount_value += Number(discount_value);
              total += Number(sub_total);

              content = '<tr id="' + data.order_number + '"><td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="' + data.order_number +'" '+ is_pay +'></td>';
              content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="' + data.order_number + '"'+ is_discount +'></td>';
              content += '<td>' + data.order_date_formatted + '</td>';
              content += '<td>' + data.charge_slip_number + '</td>';
              content += '<td>' + data.product_description + '</td>';
              content += '<td align="right">' + data.quantity + '</td>';
              content += '<td align="right">' + data.unit_price + '</td>';
              content += '<td align="right">' + Number(discount).toFixed(2) + '</td>';
              content += '<td align="right">' + Number(discount_value).toFixed(2) + '</td>';
              content += '<td align="right" class="sub_total">' + Number(sub_total).toFixed(2) + '</td>';
              content += '<td>' + data.invoice_status + '</td></tr>';
              $(content).appendTo('#charge-info');
            });
            content = '<tr><td colspan=8 align="right"><strong>Grand Total: </strong></td>';
            content += '<td colspan=1 align="right">' + Number(total_discount_value).toFixed(2) + '</td>';
            content += '<td colspan=1 align="right"><strong>' + Number(total).toFixed(2) + '</strong></td></tr>';
            $(content).appendTo('#charge-info');
            $('#amount_paid').val(Number(total).toFixed(2));
          }
        });

      } else {

      }

      id = [];

      $('input[name="discount_checkbox"]:checked').each(function(){
        id.push($(this).val());

      });

      discount = $('#discount_percent').val();

      if (discount == '' || discount == 0 || discount == null) {
        // alert('No discount(%) found');

      } else {
        /* check if there is checkedValues checkboxes, by default the length is 1 as it contains one single comma */
        if (id.length > 0) {
          $.ajax({
          type: "POST",
          url: "/collections/outpatient/create/apply_discount_selected",
          data: { _token: CSRF_TOKEN, charge_slip: charge_slip_number, ids: id, discount: discount },
          dataType: "JSON",
          success: function(data){

            console.log(data);
            discount_name = JSON.stringify(data.discount_name);
            // alert(discount_name);
            $('#charge-info').empty();
            $.each(data.data, function(i, data){
              discount_name = data.disc_name;
              discount_percent = data.disc_percent;
              discount_amount = data.computed_discount;
              sub_total = data.computed_sub_total;
              is_discount = data.is_discount;
              amount = data.amount;
              is_pay = data.is_pay;

              if (sub_total == null || sub_total == '') {
                sub_total = amount;
              } else {

              }

              if (is_discount == '1') {
                is_discount = 'checked';
              } else {
                is_discount = '';

              }

              if (is_pay == '0') {
                is_pay = ''
                sub_total = 0;

              } else {
                is_pay = 'checked';

              }

              total_discount_value += Number(discount_amount);
              total += Number(sub_total);
              content = '<tr id="'+ data.order_number +'"><td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="' + data.order_number +'" '+ is_pay +'></td>';
              content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="' + data.order_number + '" ' + is_discount + '></td>';
              content += '<td>' + data.order_date_formatted + '</td>';
              content += '<td>' + data.charge_slip_number + '</td>';
              content += '<td>' + data.product_description + '</td>';
              content += '<td align="right">' + data.quantity + '</td>';
              content += '<td align="right">' + data.unit_price + '</td>';
              content += '<td align="right" style="width:8%">' + Number( discount_percent ).toFixed(2) + '</td>';
              content += '<td align="right" style="width:10%">' + Number( discount_amount ).toFixed(2) + '</td>';
              content += '<td align="right" style="width:10%" class="sub_total">' + Number( sub_total ).toFixed(2) + '</td>';
              content += '<td>' + data.invoice_status + '</td></tr>';
              $(content).appendTo('#charge-info');
            });
            content = '<tr><td colspan=8 align="right"><strong>Grand Total: </strong></td>';
            content += '<td colspan=1 align="right">' + Number( total_discount_value ).toFixed(2) + '</td>';
            content += '<td colspan=1 align="right" ><strong>' + Number( total ).toFixed(2) + '</strong></td></tr>';
            $(content).appendTo('#charge-info');
            $('#amount_paid').val((total).toFixed(2));
          }
        });

        } else {
          // alert("Please at least check one of the checkbox");
        }
      }


      // alert('unchecked pay checkboxes: ' + id);
      // console.log(id);

    });
  });
</script>

<script type="text/javascript">
  $('#collections_outpatient').on('keyup keypress', function(e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
      e.preventDefault();
      return false;
    }
  });

  $('#collections_outpatient').on('submit', function() {
    $('.alert').show();
  });
</script>


@endsection
