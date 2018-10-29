@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Collections Outpatient</h1>

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

  <div class="alert alert-warning alert-dismissable" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      This charge slip was already paid.
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
        <input id="amount_tendered" onBlur="computeChange()" type="text" class="form-control form-control-sm" name="amount_tendered"  style="background-color:#99ccff!important;" autofocus>

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



@endsection