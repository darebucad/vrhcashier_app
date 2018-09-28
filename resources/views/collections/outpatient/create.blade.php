@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create Collections Outpatient</h1>

    <div class="btn-toolbar mb-2 mb-md-0">

      <div class="btn-group mr-2">
        <input  type="text" name="search_barcode" id="search_barcode" class="form-control form-control-sm"  placeholder="Charge Slip / Barcode" autofocus>
      </div>  

      <div class="btn-group mr-2">
        <button id="post_data" class="btn btn-outline-info pull-right btn-sm">
          Search
        </button>
      </div>

      </div>

    </div>

  <form action="/collections/outpatient/create/payment" method="post" >
    @csrf

    <div class="row" style="margin-top:10px;">

      <button type="submit" class="btn btn-sm btn-primary">Save</button>
        <p id="button-cancel">or <a class="btn-link" href="{{ url('collections/outpatient') }}">Cancel</a></p>

       <div class="col-md-4 offset-md-4">
        <!-- <a href = "{{ url('/collections/outpatient/print/pdf') }}" class="btn btn-sm btn-outline-danger">
          <span data-feather="calendar"></span>
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


    <div id="patient_name_field" class="form-group row">

      <div class="input-group">
        <label for="patient_name" class="col-md-1 col-form-label text-md-left">{{ __('Patient Name') }}</label>

        <div class="col-md-9">
          <input type="text" name="patient_name" id="patient_name" value="" class="form-control form-contorl-sm">
        </div>
      </div>

    </div>

    <!-- OR Date/Number Control -->
    <div class="form-group row">
      <label for="ordate" class="col-md-1 col-form-label text-md-left">{{ __('OR Date') }}</label>
      <div class="col-md-2">
        <div class="input-group">

          <input id="ordate" type="text" class="form-control form-control-sm" name="ordate" value="{{ $now = date('m/d/Y') }}" style="background-color:#99ccff!important;" required autofocus>
          <!-- <div class="input-group-append">
            <i class="far fa-calendar-alt"></i>
          </div> -->
        </div>
      </div>

      <label for="or_number" class="col-md-2 col-form-label text-md-left">{{ __('OR Number') }}</label>

      <div class="col-md-4">


      @foreach ($or_number as $or)
        @if ($loop->first)
          <input id="or_number" type="text" class="form-control form-control-sm" name="or_number" value="{{ $or->orno + 1 }}" style="background-color:#99ccff!important;" required autofocus>
          <input type="hidden" name="or_number_only" value="{{ $or->orno }}">

        @endif
      @endforeach

          
      @if ($errors->has('or_number'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('or_number') }}</strong>
        </span>
      @endif
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


      <label for="currency" class="col-md-1 col-form-label text-md-left">{{ __('Currency') }}</label>
       <div class="col-md-3">
          <select id="currency" class="form-control form-control-sm" name="currency">
            <option value=""> </option>
            <option value="DOLLA">Dollars</option>
            <option value="OTHER">Others</option>
            <option value="PESO" selected>Php</option>
            <option value="YEN">Yen</option>
          </select>
        
      </div>


    </div>

    <!-- Discount details  Currency Control --> 
    <div class="form-group row" style="margin-bottom:1px;">
      <label class="col-md-2 col-form-label text-md-left">{{ __('Discount details | ') }}</label>
      <label for="discount_percent" class="col-md-2 col-form-label text-md-left">{{ __('Discount (%)') }}</label>

      <div class="col-md-3">

        <select  id="discount_percent" class="form-control form-control-sm" name="discount_percent">
          <option value=" " selected> </option>
          <option value="SENIOR">Senior Citizen</option>
          <option value="PWD">PWD</option>
          <option value="100">100% Discount</option>
          <option value="10">10% Discount</option>
          <option value="20">20% Discount</option>
          <option value="25">25% Discount</option>
          <option value="50">50% Discount</option>
          <option value="75">75% Discount</option>
        </select>
      
      </div>

      <div class="col-md-1">
        <button type="button" id="apply_discount_all" class="btn btn-success btn-sm">
          Apply to all
        </button>
      </div>

    </div>


    <!-- Discount computation Control -->
    <div class="form-group row">
      <label for="discount_computation" class="col-md-2 offset-md-2 col-form-label text-md-left">{{ __('Discount Computation') }}</label>
      <div class="col-md-3">
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

      <div class="col-md-1">
        <button type="button" id="apply_discount_selected" class="btn btn-success btn-sm">
          Apply to Selected
        </button>
      </div>
    </div>


    <!-- Amount paid  Amount tendered  Change Control  -->
    <div class="form-group row">
      <label for="amount_paid" class="col-md-1 col-form-label text-md-left">{{ __('Amount Paid') }}</label>

      <div class="col-md-3">
        <input id="amount_paid" type="text" class="form-control form-control-sm" name="amount_paid" style="background-color:#99ccff!important;" autofocus>
        @if ($errors->has('amount_paid'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('amount_paid') }}</strong>
            </span>
        @endif

      </div>


      <label for="amount_tendered" class="col-md-1 col-form-label text-md-left">{{ __('Amount Tendered') }}</label>

      <div class="col-md-3">
        <input id="amount_tendered" type="text" class="form-control form-control-sm" name="amount_tendered"  style="background-color:#99ccff!important;" autofocus>

        @if ($errors->has('amount_tendered'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('amount_tendered') }}</strong>
            </span>
        @endif

      </div>


      <label for="change" class="col-md-1 col-form-label text-md-left">{{ __('Change') }}</label>

      <div class="col-md-3">
        <input id="change" type="text" class="form-control form-control-sm" name="change" style="background-color:#99ccff!important;" autofocus>
        @if ($errors->has('change'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('change') }}</strong>
            </span>
        @endif
      </div>
    </div>

 </form>

<div class="form-group row">
  <button type="button" name="update_charges" id="update_charges" class="btn btn-success btn-sm">
    Update
  </button>
</div>

  <div class="table-responsive">
    <table id="invoice_table" class="table table-sm" style="width: 100%">
      <thead>
        <tr>
          <th>Pay?</th>
          <th>Disc?</th>
          <th>Date</th>
          <th>Charge Slip Ref</th>
          <th>Description</th>
          <th>QTY</th>
          <th>Unit Cost</th>
          <th style="width:8%">Discount (%)</th>
          <th style="width:10%">Discount Value</th>
          <th style="width:10%">Sub-total</th>
          <th>Status</th>
        </tr>
      </thead>
       <tbody id="charge-info">
         
       </tbody>
    </table>
  </div>

</main>



<!-- CSRF TOKEN -->
<script type="text/javascript">
  $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
</script>


<!-- Load Data button -->
<script type="text/javascript">
  $(document).ready(function(){
    $('#load_data').on('click', function(){
      $.ajax({
        type: "GET",
        url: "/collections/outpatient/create/load_data",
        success: function(data){
          // alert($('#search_barcode').val());
          // $('#charge-info').empty().html(data);
          console.log(data);
          }
        }); // end of ajax
       }); 
  });
</script>



<!-- Post Data / Search Barcode button -->
<script type="text/javascript">
  $(document).ready(function(){
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $('#post_data').click(function(){
      $.ajax({
        type: "POST",
        url: "/collections/outpatient/create/post_data",
        data: {_token: CSRF_TOKEN, message:$("#search_barcode").val(), user_id:$("#user_id").val()},
        dataType: 'JSON',
        success: function(data){

          // alert('clicked search barcode');
          $('#acctno').val(data.new_account_no);

          $.each(data.data, function(i, data){
            $('#patient_name').val(data.patient_name);
            $('#pcchrgcod').val(data.pcchrgcod);
            $('#enccode').val(data.enccode);
            $('#hpercode').val(data.hpercode);
            $('#discount_percent').val(data.disc_name);
            $('#paystat').val('A');
            $('#paylock').val('N');
            $('#updsw').val('N');
            $('#confdl').val('N');
            $('#payctr').val('0');
            $('#status').val('For Payment');
          });

          console.log(data);
    
          var content;
          var discount = 0;
          var total_discount_value = 0;
          var total = 0;
          var is_discount = 0;

          $('#charge-info').empty();
            $.each(data.data, function(i, data) {

              discount = data.disc_percent;
              discount_amount = data.disc_amount;
              //discount_amount = data.computed_discount
              sub_total = data.pcchrgamt;
              // sub_total = data.computed_sub_total;
              is_discount = data.is_discount;

              if (discount == null || discount == '') {
                discount = 0.00;
              }
              else if (discount == 'SENIOR' || discount == 'PWD') {
                discount = 20.00;
              }

              if (discount_amount == null || discount_amount == '') {
                discount_amount = 0.00;
                total_discount_value = 0.00;
              }

              if (is_discount == '1') {
                is_discount = 'checked';
              }
              
              total_discount_value += Number(discount_amount);
              total += Number(sub_total);
              
              content = '<tr><td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="' + data.docointkey +'" checked></td>';
              content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="' + data.docointkey + '" ' + is_discount + '></td>';
              content += '<td>' + data.dodate + '</td>';
              content += '<td>' + data.pcchrgcod + '</td>';
              content += '<td>' + data.product_description + '</td>';
              content += '<td align="right">' + data.qtyissued + '</td>';
              content += '<td align="right">' + data.pchrgup + '</td>';
              content += '<td align="right" style="width:8%">' + Number(discount).toFixed(2) + '</td>';
              content += '<td align="right" style="width:10%">' + Number(discount_amount).toFixed(2) + '</td>';
              content += '<td align="right" style="width:10%">' + Number(sub_total).toFixed(2) + '</td>';
              content += '<td>' + data.invoice_status + '</td></tr>';
              $(content).appendTo('#charge-info');

            });

            content = '<tr><td colspan=8 align="right"><strong>Grand Total: </strong></td>';
            content += '<td colspan=1 align="right">' + Number(total_discount_value).toFixed(2) + '</td>';
            content += '<td colspan=1 align="right"><strong>' + Number(total).toFixed(2) + '</strong></td></tr>';
            $(content).appendTo('#charge-info');

            $('#amount_paid').val((total).toFixed(2));

        }
      }); 
    });
  });
</script>



<!-- Click apply discount all -->
<script type="text/javascript">
  $(document).ready(function(){
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $("#apply_discount_all").click(function(){
      $.ajax({
        type: "POST",
        url: "/collections/outpatient/create/apply_discount_all",
        data: {_token: CSRF_TOKEN, message:$("#search_barcode").val(), discount:$("#discount_percent").val()},
        dataType: 'JSON',
        success: function(data){

          // alert(JSON.stringify(data.data));
          console.log(data);
          var content;
          var discount = $('#discount_percent').val(); //get discount percent
          var discount_value = 0;
          var sub_total = 0;
          var total_discount_value = 0;
          var total = 0;


          if (discount == null || discount == '') {
            discount = 0.00;
          }
          else if (discount == 'SENIOR' || discount == 'PWD'){
            discount = 20.00;
          } 

          $('#charge-info').empty();
          $.each(data.data, function(i, value){

            // discount_value = Number(value.pcchrgamt * (discount/100));
            discount_value = Number(value.computed_discount);
            // sub_total = Number(value.pcchrgamt - (value.pcchrgamt * (discount/100)));
            sub_total = Number(value.computed_sub_total);

            if (sub_total == null || sub_total == '') {
              sub_total = Number(value.pcchrgamt);

            }
            total_discount_value += discount_value;
            total += sub_total;

            content = '<tr><td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="' + value.docointkey +'" checked></td>';
            content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="' + value.docointkey + '" checked></td>';
            content += '<td>' + value.dodate + '</td>';
            content += '<td>' + value.pcchrgcod + '</td>';
            content += '<td>' + value.product_description + '</td>';
            content += '<td align="right">' + value.qtyissued + '</td>';
            content += '<td align="right">' + value.pchrgup + '</td>';
            content += '<td align="right">' + Number(discount).toFixed(2) + '</td>';
            content += '<td align="right">' + Number(discount_value).toFixed(2) + '</td>';
            content += '<td align="right" >' + Number(sub_total).toFixed(2)+ '</td>';
            content += '<td>' + value.invoice_status + '</td></tr>';
            $(content).appendTo('#charge-info');

          });

          content = '<tr><td colspan=8 align="right"><strong>Grand Total: </strong></td>';
          content += '<td colspan=1 align="right">' + Number(total_discount_value).toFixed(2) + '</td>';
          content += '<td colspan=1 align="right"><strong>' + Number(total).toFixed(2) + '</strong></td></tr>';
          $(content).appendTo('#charge-info');

          $('#amount_paid').val(Number(total).toFixed(2));
            
        }
      }); 
    });
  });
</script>

<!-- apply discount selected -->
<script type="text/javascript">
  $(document).ready(function(){
    var CSRF_TOKEN = $('meta[name="csrf_token"]').attr('content');
    $('#apply_discount_selected').click(function(){

      /* declare an checkbox array */
      var id = [];
  
      /* look for all checkboxes that have a class 'chk' attached to it and check if it was checked */
      $('input[name="discount_checkbox"]:checked').each(function() {
        id.push($(this).val());
      });
  
      /* check if there is checkedValues checkboxes, by default the length is 1 as it contains one single comma */
      if (id.length > 0) { 
        $.ajax({
        type: "POST",
        url: "/collections/outpatient/create/apply_discount_selected",
        data: { _token: CSRF_TOKEN, charge_slip: $('#search_barcode').val(), ids: id, discount: $('#discount_percent').val() },
        dataType: "JSON",
        success: function(data){

          alert(data.charge_slip);
          alert(data.ids);
          alert(data.discount_percent);
          alert(JSON.stringify(data.data));

          console.log(data);

          var content;
          var discount = 0;
          var total_discount_value = 0;
          var sub_total = 0;
          var total = 0;
          var is_discount = 0;
          
          $('#charge-info').empty();
            $.each(data.data, function(i, data){

              discount = data.disc_percent;
              discount_amount = data.computed_discount;
              sub_total = data.computed_sub_total;
              is_discount = data.is_discount;

              if (discount_amount == null || discount_amount == '' || discount_amount == 0.00) {
                // discount_amount = 0.00;
                // total_discount_value = 0.00;
              }

              if (sub_total == null || sub_total == '' || sub_total == 0.00) {
                sub_total = Number(data.pcchrgamt);
              
              }


              if (discount == 'SENIOR' || discount == 'PWD') {
                discount = 20.00;
              }

              if (is_discount == '1') {
                is_discount = 'checked';
              }
              else{
                is_discount = '';
              }
              
              total_discount_value += Number(discount_amount);
              total += Number(sub_total);
              
              content = '<tr><td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="' + data.docointkey +'" checked></td>';
              content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="' + data.docointkey + '" ' + is_discount + '></td>';
              content += '<td>' + data.dodate + '</td>';
              content += '<td>' + data.pcchrgcod + '</td>';
              content += '<td>' + data.product_description + '</td>';
              content += '<td align="right">' + data.qtyissued + '</td>';
              content += '<td align="right">' + data.pchrgup + '</td>';
              content += '<td align="right" style="width:8%">' + Number(discount).toFixed(2) + '</td>';
              content += '<td align="right" style="width:10%">' + Number(discount_amount).toFixed(2) + '</td>';
              content += '<td align="right" style="width:10%">' + Number( sub_total ).toFixed(2) + '</td>';
              content += '<td>' + data.invoice_status + '</td></tr>';
              $(content).appendTo('#charge-info');

            });

            content = '<tr><td colspan=8 align="right"><strong>Grand Total: </strong></td>';
            content += '<td colspan=1 align="right">' + Number(total_discount_value).toFixed(2) + '</td>';
            content += '<td colspan=1 align="right" ><strong>' + Number(total).toFixed(2) + '</strong></td></tr>';
            $(content).appendTo('#charge-info');

            $('#amount_paid').val((total).toFixed(2));
        }
      });
      }
      else {
        alert("Please at least check one of the checkbox"); 
      }

      // console.dir($checkboxes);
      // alert('Clicked apply discount selected');
      // alert($('#invoice_table:checkbox:checked'));
      // var atLeastOneIsChecked = $('#invoice_table:checkbox:checked').length > 0;
      // alert(atLeastOneIsChecked);
    });
  });
</script>


<!-- click update button -->
<script type="text/javascript">
  $(document).ready(function(){
    var CSRF_TOKEN =  $('meta[name="csrf-token"]').attr('content');
    
    $('#update_charges').click(function(){
     // alert("clicked update button");
     $('#discount_percent').val('');
     $('#amount_paid').val('');
     $.ajax({
        type: "POST",
        url: "/collections/outpatient/create/update_data",
        data: {_token: CSRF_TOKEN, message:$("#search_barcode").val(), discount:'0'},
        dataType: "JSON",
        success: function(data) {
          // alert(data.discount_percent);

          console.log(data);

          var content;
          var discount = 0;
          var discount_value = 0;
          var sub_total = 0;
          var total_discount_value = 0;
          var total = 0;

          $('#charge-info').empty();
          $.each(data.data, function(i, value){
            
            discount_value = Number(value.pcchrgamt * (discount/100)).toFixed(2);
            sub_total = Number(value.pcchrgamt - (value.pcchrgamt * (discount/100))).toFixed(2);
            total_discount_value += Number(discount_value);
            total += Number(sub_total);

            content = '<tr><td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="' + value.docointkey +'" checked></td>';
            content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="' + value.docointkey + '"></td>';
            content += '<td>' + value.dodate + '</td>';
            content += '<td>' + value.pcchrgcod + '</td>';
            content += '<td>' + value.product_description + '</td>';
            content += '<td align="right">' + value.qtyissued + '</td>';
            content += '<td align="right">' + value.pchrgup + '</td>';
            content += '<td align="right">' + Number(discount).toFixed(2) + '</td>';
            content += '<td align="right">' + Number(discount_value).toFixed(2) + '</td>';
            content += '<td align="right">' + Number(sub_total).toFixed(2) + '</td>';
            content += '<td>' + value.estatus + '</td></tr>';
            $(content).appendTo('#charge-info');

          });
          content = '<tr><td colspan=8 align="right"><strong>Grand Total: </strong></td>';
          content += '<td colspan=1 align="right">' + Number(total_discount_value).toFixed(2) + '</td>';
          content += '<td colspan=1 align="right"><strong>' + Number(total).toFixed(2) + '</strong></td></tr>';
          $(content).appendTo('#charge-info');

          $('#amount_paid').val(Number(total).toFixed(2));

        }
      });
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



@endsection





