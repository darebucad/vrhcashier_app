@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create Collections Outpatient</h1>

    <div class="btn-toolbar mb-2 mb-md-0">

        <div class="btn-group mr-2">
          <input  type="text" name="search_barcode" id="search_barcode" class="form-control form-control-sm"  placeholder="Enter Barcode" autofocus>
        </div>  

        <div class="btn-group mr-2">
          <button id="post_data" class="btn btn-outline-info pull-right btn-sm ">
            Search
          </button>
        </div>

      </div>
    </div>


  <form action="/collections/outpatient/create/payment" method="post" >

    @csrf

    <div class="row" style="margin-top:10px;">
      <button type="submit" class="btn btn-xs btn-primary">Save</button>
      <p id="button-cancel">or <a class="btn-link" href="{{ url('collections/outpatient') }}">Cancel</a></p>
    </div>

    <br />
    <br />

    <input type="hidden" name="paystat" value="A">

    <input type="hidden" name="paylock" value="N">

    <input type="hidden" name="updsw" value="N">

    <input type="hidden" name="confdl" value="N">

    <input type="hidden" name="payctr" value="0">

    <input type="hidden" name="status" value="Paid">

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
          <option value="1">100% Discount</option>
          <option value="0.1">10% Discount</option>
          <option value="0.2">20% Discount</option>
          <option value="0.25">25% Discount</option>
          <option value="0.5">50% Discount</option>
          <option value="0.75">75% Discount</option>
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
          <th>Discount (%)</th>
          <th>Discount Value</th>
          <th>Sub-total</th>
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

          alert($('#search_barcode').val());
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
          // alert(data.charge_slip);
          // alert(JSON.stringify(data.data));
          // alert('account_no: ' + data.account_no);
          // alert('new_account_no: ' + data.new_account_no);
          // alert('user_id: ' + data.user_id);
          alert('clicked search barcode');

          $('#discount_percent').val(' ');
          $('#acctno').val(data.new_account_no);

          $.each(data.data, function(i, data){
            $('#patient_name').val(data.patient_name);
            $('#pcchrgcod').val(data.pcchrgcod);
            $('#enccode').val(data.enccode);
            $('#hpercode').val(data.hpercode);

          });

          console.log(data);
    
          var content;
          var total_discount_value = 0;
          var total = 0;

          
          $('#charge-info').empty();
            $.each(data.data, function(i, data){

              if(data.disc_percent == null || data.disc_percent == '') {
                data.disc_percent = 0;
                total_discount_value = 0;
              }

              if(data.disc_amount == null || data.disc_amount == '') {
                data.disc_amount = 0;
                total_discount_value = 0;
              }

              total += Number(data.pcchrgamt)
              
              content = '<tr><td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="' + data.docointkey +'" checked></td>';
              content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="' + data.docointkey + '"></td>';
              content += '<td>' + data.dodate + '</td>';
              content += '<td>' + data.pcchrgcod + '</td>';
              content += '<td>' + data.product_description + '</td>';
              content += '<td align="right">' + data.qtyissued + '</td>';
              content += '<td align="right">' + data.pchrgup + '</td>';
              content += '<td align="right">' + (data.disc_percent).toFixed(2) + '</td>';
              content += '<td align="right">' + (data.disc_amount).toFixed(2) + '</td>';
              content += '<td>' + data.pcchrgamt + '</td>';
              content += '<td>' + data.estatus + '</td></tr>';
              $(content).appendTo('#charge-info');

            });


            var value = parseFloat(total);
          var num_total = $(total).text(value.toFixed(2));

            content = '<tr><td colspan=8 align="right">Totals: </td>';
            content += '<td colspan=1 align="right">' + (total_discount_value).toFixed(2) + '</td>';
            content += '<td colspan=2>' + (total).toFixed(2) + '</td></tr>';
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

      alert("clicked apply discount all");

      $.ajax({
        type: "POST",
        url: "/collections/outpatient/create/post_data",
        data: {_token: CSRF_TOKEN, message:$("#search_barcode").val(), user_id:$("#user_id").val()},
        dataType: 'JSON',
        success: function(data){
          // alert(data.charge_slip);
          // alert(JSON.stringify(data.data));

        
          // $.each(data.data, function(i, data){
          //   $('#patient_name').val(data.patient_name);
          //   $('#pcchrgcod').val(data.pcchrgcod);
          //   $('#enccode').val(data.enccode);
          //   $('#hpercode').val(data.hpercode);
          //   $('#acctno').val(data.acctno);
          // });

          console.log(data);
    
          var content;
          var discount = $('#discount_percent').val();
          var discount_value = 0;
          var sub_total = 0;
          var total_discount_value = 0;
          var total = 0


          if (discount == 'SENIOR'){
            discount = 20;
          } 
          else if (discount == 'PWD'){
            discount = 20;
          }
          else{
            discount = (discount * 100);
          }


          $('#charge-info').empty();
          $.each(data.data, function(i, data){

            total_discount_value += (data.pcchrgamt * (discount/100))
            total += (data.pcchrgamt - (data.pcchrgamt * (discount/100)))
            discount_value = (data.pcchrgamt * (discount/100))
            sub_total = (data.pcchrgamt - (data.pcchrgamt * (discount/100)))


            content = '<tr><td><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="' + data.docointkey +'" checked></td>';
            content += '<td><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="' + data.docointkey + '" checked></td>';
            content += '<td>' + data.dodate + '</td>';
            content += '<td>' + data.pcchrgcod + '</td>';
            content += '<td>' + data.product_description + '</td>';
            content += '<td align="right">' + data.qtyissued + '</td>';
            content += '<td align="right">' + data.pchrgup + '</td>';
            content += '<td align="right">' + (discount).toFixed(2) + '</td>';
            content += '<td align="right">' + (discount_value).toFixed(2) + '</td>';
            content += '<td>' + (sub_total).toFixed(2) + '</td>';
            content += '<td>' + data.estatus + '</td></tr>';
            $(content).appendTo('#charge-info');
          });

              content = '<tr><td colspan=8 align="right">Totals: </td>';
              content += '<td colspan=1 align="right">' + (total_discount_value).toFixed(2) + '</td>';
              content += '<td colspan=2>' + (total).toFixed(2) + '</td></tr>';
              $(content).appendTo('#charge-info');

              $('#amount_paid').val((total).toFixed(2));
            
        }
      }); 
    });
  });
</script>


<script type="text/javascript">
  $(document).ready(function(){
    
    $('#apply_discount_selected').click(function(){


      alert('Clicked apply discount selected');

    });
  });
</script>


<script type="text/javascript">
  $(document).ready(function(){
    $('#update_charges').click(function(){
     
      $('#charge-info').each(function(){
        alert($(this).find('.dateCell').html());
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





