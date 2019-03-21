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
    </div>
  </div>


  <div class="alert alert-success alert-dismissable" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      This charge slip was already paid.
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

  <form id="collections_walkin">
    @csrf

    <div class="row" style="margin-top:10px;">
      <button class="btn btn-sm btn-primary" id="button_save">Save</button>
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
        <button type="button" name="update_totals" id="update_total" class="btn btn-outline-dark btn-sm">
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
  $(document).ready(function(){
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    $('#post_data').click(function(){
      // alert('clicked search button');
      var charge_slip_number = $('#charge_slip_number').val();
      var user_id = $('#user_id').val();


      if (charge_slip_number == '') {
        alert('charge slip number is empty');

      } else {
        // alert('go to next step');

        // A function that will search and get walk-in charges .
        searchWalkinCharges(CSRF_TOKEN, charge_slip_number, user_id);

      }
    });

    $('#collections_walkin').on('keydown', function(event){
      event.preventDefault()

    });

    function searchWalkinCharges( token, cnumber, uid ) {
      $.ajax({
        type: "POST",
        url: "/collections/walkin/create/search-walkin-charges",
        data: { _token: token, charge_slip_number: cnumber, user_id: uid  },
        dataType: "JSON",
        success: function(data) {
          console.log(data);
          console.log(data.charge_slip_number);
          console.log(data.user_id);
          console.log(data.patient_name);

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
          var total_amount = 0


          $('#patient_name').val(patient_name);

          $('#invoice_data').empty();
          $.each(data.data, function(i, value) {
            date_created = value.date_created;
            charge_slip_number = value.chargeslipno;
            product_code = value.itemcode;
            product_description = value.description;
            quantity = Number(value.qty);
            unit_price = Number(value.price);
            discount_percent = Number(0);
            discount_value = Number(0);
            sub_total = Number(value.total);
            status = 'Invoiced';

            total_discount_value += total_discount_value + discount_value;
            total_amount += total_amount + sub_total;
            content = '<tr>';
            content += '<td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="" checked></td>';
            content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value=""></td>';
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
          });
          content = '<tr>';
          content += '<td colspan="8" align="right">Totals: </td>';
          content += '<td align="right"><strong>'+ total_discount_value.toFixed(2) +'</strong></td>';
          content += '<td align="right"><strong>'+ total_amount.toFixed(2) +'</strong></td>';
          content += '</tr>';
          $(content).appendTo('#invoice_data');

          $('#amount_paid').val(total_amount.toFixed(2));






          // $('#invoice_data').empty();


        }
      });
    }

  });



</script>

@endsection
