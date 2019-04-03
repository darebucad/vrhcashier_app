@extends ('layouts.master')


@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h4">Create Collections Inpatient</h1>
    @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <button class="btn btn-sm btn-outline-secondary">Share</button>
        <button class="btn btn-sm btn-outline-secondary">Export</button>
      </div>
      <button class="btn btn-sm btn-outline-secondary dropdown-toggle">
        <span data-feather="calendar"></span>
        This week
      </button>
    </div>
  </div>

  <div class="row" style="margin-top:10px;">
    <button type="submit" class="btn btn-sm btn-primary" id="button_save">Save</button>
    <p id="button-cancel">or <a class="btn-link" href="{{ url('collections/inpatient') }}">Cancel</a></p>
  </div>

  <form id="collections_inpatient">
    @csrf

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
        <label for="patient_name" class="col-md-1 offset-md-1 col-form-label text-md-left">{{ __('Patient Name') }}</label>

        <div class="col-md-8">
          <input type="text" name="patient_name" id="patient_name" value="" class="form-control" style="background-color:#99ccff!important;" required>
        </div>
      </div>
    </div>

    <!-- OR Date / OR Number Input -->
    <div class="form-group row" style="margin-top: -5px;">
      <label for="ordate" class="col-md-1 offset-md-1 col-form-label text-md-left">{{ __('O.R. Date') }}</label>

      <div class="col-md-1">
        <div class="input-group mb-3">
          <input id="ordate" type="text" class="form-control form-control-sm" name="ordate" value="{{ $now = date('m/d/Y') }}" style="background-color:#99ccff!important;" required autofocus>
            <!-- <span data-feather="calender"></span> -->
        </div>
      </div>

      <label for="prefix_or_number" class="col-md-1 col-form-label text-md-right">{{ __('O.R. Number') }}</label>

      <div class="col-md-2">
        @if (count($payments) > 0)
          @foreach ($payments as $payment)
            <input type="text" name="prefix_or_number" value="{{ $payment->or_prefix . '-' . $payment->next_or_number }}" class="form-control form-control-sm" required>
            <input type="hidden" name="or_number" value="{{ $payment->next_or_number }}">
          @endforeach
        @else
          <input type="text" name="prefix_or_number" value="{{ '0000001' }}" class="form-control form-control-sm" required>
          <input type="text" name="or_number" value="{{ '0000001' }}">
        @endif

        @if ($errors->has('or_number'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('or_number') }}</strong>
          </span>
        @endif
      </div>

      <label class="col-md-1 col-form-label text-md-right"> User: </label>
      <label class="col-md-2 col-form-label text-md-left"><u>{{ Auth::user()->name }}</u></label>

      <div class="col-md-1">
        <button type="button" name="update_total" id="update_total" class="btn btn-outline-dark btn-sm">
          Update Totals
        </button>
      </div>

    </div>


    <!-- Mode/Type of payment Control -->
    <div class="form-group row" style="margin-top: -20px;">
      <label for="payment_mode" class="col-md-2 offset-md-1 col-form-label text-md-left">{{ __('Mode of Payment') }}</label>

      <div class="col-md-3">
        <select id="payment_mode" class="form-control form-control-sm" name="payment_mode">
          <option value=""> </option>
          <option value="C" selected>Cash</option>
          <option value="X">Check</option>
        </select>
      </div>

      <!-- <label for="discount_percent" class="col-md-1 col-form-label text-md-left">{{ __('Discount (%)') }}</label>
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
      </div> -->

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

      <!-- <label for="discount_computation" class="col-md-2 col-form-label text-md-left">{{ __('Discount Computation') }}</label>
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
      </div> -->

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
      <!-- <div class="col-md-1 offset-md-1">
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
      </div> -->

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

    <!-- <div class="form-group row">
      <label for="professional_fees" class="col-md-1 offset-md-1 col-form-label">Professional Fees:</label>

      <div class="col-md-3">
        <input type="text" name="professional_fees" value="" id="professional_fees" class="form-control">
      </div>

    </div>

    <div class="form-group row">
      <label for="drugs" class="col-md-1 offset-md-1 col-form-label">Drugs and Medicines:</label>
      <div class="col-md-2">
        <input type="text" name="drugs" value="" id="drugs" class="form-control">
      </div>
    </div> -->
</form>

<div class="form-group row">
  <div class="col-md-2 offset-md-1">
    <h1 class="h5 text-danger">Breakdown of Charges: </h1>

  </div>
</div>

<div class="row" style="margin-top:-10px;">
  <div class="col-md-9 offset-md-1">
    <div class="table-responsive">
      <table id="charge_table" class="table table-sm" style="width: 100%">
        <thead>
          <tr>
            <th>Charge Description</th>
            <th>Amount</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="charge-info">
          <tr>
            <td>Professional Fees</td>
            <td>3,000.00</td>
            <td></td>
          </tr>
          <tr>
            <td>Drugs and Medicines</td>
            <td>12,500.00</td>
            <td></td>
          </tr>
          <tr>
            <td>Radiology</td>
            <td>10,500.00</td>
            <td></td>
          </tr>
          <tr>
            <td>OR/DR Fee</td>
            <td>3,500.00</td>
            <td></td>
          </tr>
          <tr>
            <td>Room and Board</td>
            <td>1,500.00</td>
            <td></td>
          </tr>
          <tr>
            <td>Oxygen</td>
            <td>20,500.00</td>
            <td></td>
          </tr>
          <tr>
            <td>Laboratory</td>
            <td>22,500.00</td>
            <td></td>
          </tr>
          <tr>
            <td>Others</td>
            <td>1,500.00</td>
            <td></td>
          </tr>
          <tr>
            <td>Supplies</td>
            <td>3,500.00</td>
            <td></td>
          </tr>
          <tr>
            <td>Miscellaneous</td>
            <td align="right">500.00</td>
            <td></td>
          </tr>
          <tr>
            <td align="right">Total:</td>
            <td align="right">30,500.00</td>
            <td></td>
          </tr>

        </tbody>
      </table>
    </div>
  </div>
</div>

<br>

<div class="form-group row" style="margin-top:-20px;">
  <label for="remarks" class="col-md-1 offset-md-1 col-form-label">Remarks:</label>

</div>

<div class="form-group row" style="margin-top:-20px;">
  <div class="col-md-9 offset-md-1">
    <textarea name="remarks" rows="3" cols="80" class="form-control form-control-sm" id="remarks"></textarea>
  </div>
</div>

</main>

<script type="text/javascript">
  $(document).ready(function(){

    $('#patient_name').autocomplete({
      source: "{{ url('/collections/inpatient/create/autocomplete-search') }}",
      select: function(key, value){
        var billing_id = value.item.id;


        console.log('billing id: ' + billing_id);
        // console.log('billing id: ' + value.item.id + ' patient_name:'+ value.item.value);

      }
    });




  });

</script>

@endsection
