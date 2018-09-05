@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create Collections Outpatient</h1>
    <div class="btn-toolbar mb-2 mb-md-0">

        <form action="{{ route('collections.outpatient.create.show') }}" method="post">
            @csrf
            <div class="btn-group mr-2">
              <input  type="text" name="search_barcode" id="search_barcode" class="form-control form-control-sm"  placeholder="Enter Barcode" autofocus>
            </div>  
            <div class="btn-group mr-2">
              <button type="submit" class="btn btn-sm btn-outline-dark">
                Search
              </button>
            </div>
        </form>

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

    <input type="hidden" name="confdl" value="N">
    <input type="hidden" name="payctr" value="1">

    <!-- OR Date/Number Control -->
    <div class="form-group row">
      <label for="ordate" class="col-md-1 col-form-label text-md-left">{{ __('OR Date') }}</label>
      <div class="col-md-2">
        <div class="input-group">
          <input id="ordate" type="text" class="form-control form-control-sm" name="ordate" required autofocus>
          <!-- <div class="input-group-append">
            <i class="far fa-calendar-alt"></i>
          </div> -->
        </div>
      </div>

      <label for="or_number" class="col-md-2 col-form-label text-md-left">{{ __('OR Number') }}</label>

      <div class="col-md-4">
          <input id="or_number" type="text" class="form-control form-control-sm" name="or_number" required autofocus>
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
      <div class="col-md-3">
        <select id="payment_mode" class="form-control form-control-sm" name="payment_mode">
          <option value=""> </option>
          <option value="C" selected>Cash</option>
          <option value="X">Check</option>
        </select>
     
      </div>

      <label for="payment_type" class="col-md-2 col-form-label text-md-left">{{ __('Type of Payment') }}</label>

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
    </div>

    <!-- Discount details  Currency Control --> 
    <div class="form-group row">
      <label class="col-md-2 col-form-label text-md-left">{{ __('Discount details | ') }}</label>
      <label for="discount_percent" class="col-md-1 col-form-label text-md-left">{{ __('Discount (%)') }}</label>

      <div class="col-md-3">

        <select  id="discount_percent" class="form-control form-control-sm" name="discount_percent">
          <option value=" " selected> </option>
          <option value="SENIOR">Senior Citizen</option>
          <option value="PWD">PWD</option>
          <option value="0.10">10% Discount</option>
          <option value="0.20">20% Discount</option>
          <option value="0.25">25% Discount</option>
          <option value="0.5">50% Discount</option>
          <option value="1">100% Discount</option>
        </select>
      
  

      </div>

      <label for="currency" class="col-md-1 col-form-label text-md-left">{{ __('Currency') }}</label>
       <div class="col-md-4">
          <select id="currency" class="form-control form-control-sm" name="currency">
            <option value=""> </option>
            <option value="DOLLA">Dollars</option>
            <option value="OTHER">Others</option>
            <option value="PESO" selected>Php</option>
            <option value="YEN">Yen</option>
          </select>
        
      </div>
    </div>


    <!-- Discount computation Control -->
    <div class="form-group row">
      <label for="discount_computation" class="col-md-1 offset-md-2 col-form-label text-md-left">{{ __('Discount Computation') }}</label>
      <div class="col-md-3">
        <input id="discount_computation" type="text" class="form-control form-control-sm" name="discount_computation" autofocus>
        @if ($errors->has('discount_computation'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('discount_computation') }}</strong>
            </span>
        @endif
      </div>
    </div>


    <!-- Amount paid  Amount tendered  Change Control  -->
    <div class="form-group row">
      <label for="amount_paid" class="col-md-1 col-form-label text-md-left">{{ __('Amount Paid') }}</label>

      <div class="col-md-3">
        <input id="amount_paid" type="text" class="form-control form-control-sm" name="amount_paid" autofocus>
        @if ($errors->has('amount_paid'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('amount_paid') }}</strong>
            </span>
        @endif

      </div>


      <label for="amount_tendered" class="col-md-1 col-form-label text-md-left">{{ __('Amount Tendered') }}</label>

      <div class="col-md-3">
        <input id="amount_tendered" type="text" class="form-control form-control-sm" name="amount_tendered"  autofocus>

        @if ($errors->has('amount_tendered'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('amount_tendered') }}</strong>
            </span>
        @endif

      </div>


      <label for="change" class="col-md-1 col-form-label text-md-left">{{ __('Change') }}</label>

      <div class="col-md-3">
        <input id="change" type="text" class="form-control form-control-sm" name="change" autofocus>
        @if ($errors->has('change'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('change') }}</strong>
            </span>
        @endif
      </div>
    </div>


    <div class="table-responsive">
    <h3 align="center">Total Data : <span id="total_records"></span></h3>
    <table id="invoice_table" class="table table-striped table-bordered" style="width: 100%">
      <thead>
        <tr>
          <th>Pay?</th>
          <th>Disc?</th>
          <th>Date</th>
          <th>Charge Slip Ref</th>
          <th>Description</th>
          <th>QTY</th>
          <th>Unit Cost</th>
          <th>Discount %</th>
          <th>Discount Value</th>
          <th>Sub-total</th>
          <th>Status</th>
          <th>Invoice Type</th>
        </tr>
      </thead>
       <tr>
         
       </tr>
  </table>

  </div>

  </form>

  

</main>


<script type="text/javascript">

    $(document).ready(function() {

      // var oTable = $('#invoice_table').DataTable({
      //   dom: "<'row'<'col-xs-12'<'col-xs-6'l><'col-xs-6'p>>r>"+
      //       "<'row'<'col-xs-12't>>"+
      //       "<'row'<'col-xs-12'<'col-xs-6'i><'col-xs-6'p>>>",
      //   processing: true,
      //   serverSide: true,
      //   ajax: {
      //       url: '/collections/outpatient/getCustomFilterData',
      //       data: function (d) {
      //           d.search_barcode = $('input[name=search_barcode]').val();
      //       }
      //   },
      //   columns: [
      //           { data: 'dodate', name: 'dodate' },
      //           { data: 'pcchrgcod', name: 'pcchrgcod' },
      //           { data: 'drug_name', name: 'drug_name' },
      //           { data: 'qtyissued', name: 'qtyissued' },
      //           { data: 'pcchrgamt', name: 'pcchrgamt' }
      //       ]
      // });

  
$("#btn1").click(function(){
        alert("Text: " + $("#search_barcode").text());
    });


      //   $('#search_form').on('submit', function(e) {

      //     alert("Text: " + $("#search_barcode").text());
        

      // });

    });

    
    
  </script>



@endsection





