@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
		<h1 class="h2">Create Collections Other</h1>

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

  <!-- <form action="/collections/other/create/payment" method="post" > -->
  <form>

    @csrf

    <div class="row" style="margin-top:10px;">
      <button class="btn btn-sm btn-primary" id="btn_save">Save</button>
        <p id="button-cancel">or <a class="btn-link" href="{{ url('collections/other') }}">Cancel</a></p>
    </div>

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
  			<label class="col-md-1 col-form-label text-md-left">{{ __('Patient Name') }}</label>

  			<div class="col-md-9">
  				<select id="patient_name" class="form-control form-control-sm" required>
  					<option></option>

    					@foreach($patient_names as $name)
    						<option value="{{ $name->patient_name }}">{{ $name->patient_name }}</option>

    					@endforeach

  				</select>
  			</div>
  		</div>
  	</div>



    <!-- OR Date/Number Control -->
    <div class="form-group row">
      <label for="or_date" class="col-md-1 col-form-label text-md-left">{{ __('OR Date') }}</label>

      <div class="col-md-2">
        <div class="input-group">
          <input id="or_date" type="text" class="form-control form-control-sm" name="or_date" value="{{ $now = date('m/d/Y') }}" style="background-color:#99ccff!important;" required autofocus>
          <!-- <div class="input-group-append">
            <i class="far fa-calendar-alt"></i>
          </div> -->
        </div>
      </div>

      <label for="or_number" class="col-md-2 col-form-label text-md-left">{{ __('OR Number') }}</label>

      <div class="col-md-4">

      	@if (count($payments) === 1)
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
    </div>


    <!-- Mode/Type of payment Control -->

    <div class="form-group row" style="margin-bottom:1px;">
  		<label for="payment_mode" class="col-md-2 col-form-label text-md-left">{{ __('Mode of Payment') }}</label>

  		<div class="col-md-3">
  			<select id="payment_mode" class="form-control form-control-sm" name="payment_mode">
  				<option value=" "> </option>
  				<option value="C" selected>Cash</option>
  				<option value="X">Check</option>
  			</select>
  		</div>

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

    </div>

    <div class="form-group row" style="margin-bottom:1px;">
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

		  <label for="discount_computation" class="col-md-2 col-form-label text-md-left">{{ __('Discount Computation') }}</label>

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

    </div>

    <div class="form-group row">
		<label for="currency" class="col-md-2 col-form-label text-md-left">{{ __('Currency') }}</label>
		<div class="col-md-3">
	        <select id="currency" class="form-control form-control-sm" name="currency">
				<option value=""> </option>
				<option value="DOLLA">Dollars</option>
				<option value="OTHER">Others</option>
				<option value="PESO" selected>Php</option>
				<option value="YEN">Yen</option>
	        </select>
		</div>

		<div class="col-md-1 offset-md-2">
        <button type="button" id="apply_discount_all" class="btn btn-success btn-sm">
          Apply to all
        </button>
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
          <th style="width:5%">Pay?</th>
          <th style="width:5%">Disc?</th>
          <th style="width:30%">Description</th>
          <th style="width:10%">QTY</th>
          <th style="width:10%">Unit Cost</th>
          <th style="width:10%">Discount (%)</th>
          <th style="width:10%">Discount Value</th>
          <th style="width:10%">Sub-total</th>
          <th style="width:5%">Action</th>
        </tr>
      </thead>

      <tbody>
      	<tr>
      		<td colspan="9"><a href="#" title="" class="add-rows" id="add_row">Add an item</a></td>
      	</tr>

      </tbody>
    </table>
  </div>
</main>

<script type="text/javascript">
	$(document).ready(function() {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var table = $('#invoice_table');
    var newRow = '';
    var current_row = $('#invoice_table tr')
    var rowNum = 0;

    $('#add_row').click(function(event) {
      event.preventDefault();
      newRow =
      	'<tr id=' + rowNum + ' class="payment_values">' +
        '<td style="width:5%"><input type="checkbox" name="pay_checkbox" id="pay_checkbox" value="" checked></td>' +
        '<td style="width:5%"><input type="checkbox" name="discount_checkbox" id="discount_checkbox" value="" disabled></td>' +
        '<td style="width:45%"><select class="products form-control form-control-sm" style="width:100%"><option> </option></select></td>' +
        '<td style="width:10%" align="right"><input type="text" name="quantity[]" class="quantity form-control form-control-sm" style="width:100%; text-align: right" value="1.00"></td>' +
        '<td style="width:10%" align="right"><input type="text" name="unit_cost[]" class="unit_cost form-control form-control-sm"style="width:100%; text-align: right"></td>' +
        '<td style="width:10%" align="right"><input type="text" name="discount_percent[]" class="discount_percent form-control form-control-sm" style="width:100%; text-align: right"></td>' +
        '<td style="width:10%" align="right"><input type="text" name="discount_value[]" class="discount_value form-control form-control-sm" style="width:100%; text-align: right"></td>' +
        '<td style="width:10%"><input type="text" name="sub_total[]" class="sub_total form-control form-control-sm" style="width:100%; text-align: right"></td>' +
        '<td style="width:5%"><a href="#" class="delete-rows" id="delete_row"><span data-feather="trash-2"></span>Delete</a></td>'+
        '<td style="width:5%"><a href="#" class="select-rows" id="select_row">Select</a></td>'
        '</tr>';

      $.ajax({
        type: "GET",
        url: "/collections/other/show_products",
        data: { _token: CSRF_TOKEN },
        dataType: "JSON",
        success: function(data)  {
          console.log(data);

          $(".products").select2({
            data: data.data,
            placeholder: "Select a product",
            minimumResultsForSearch: 20, // at least 20 results must be displayed
            allowClear:true
          });
        }
      });

      table.prepend(newRow);

      rowNum = rowNum + 1;
    });

    table.on('click', '#delete_row', function() {
      $(this).closest('tr').remove();

    });

		table.on('keydown', '.quantity', function(event){
			var id = $(this).val();
      var row_id = $(this).closest('tr').attr('id');
			var sub_total_value = 0;

			if(event.which == 13){
				event.preventDefault();
				alert('pressed');
				alert(id);

				price = Number($('#invoice_table').find('tr#'+ row_id).find('.unit_cost').val());
				quantity = Number($('#invoice_table').find('tr#'+ row_id).find('.quantity').val());
				sub_total = price * quantity;


				$('#invoice_table').find('tr#' + row_id).find('.sub_total').val(Number(sub_total).toFixed(2));
				$('#invoice_table').find('tr#'+ row_id).find('.quantity').val(Number(quantity).toFixed(2));

				$('.payment_values').each(function(){
					var current_row = $(this);
					sub_total_value =  sub_total_value + Number(current_row.find('.sub_total').val());

				})

				$('#amount_paid').val(Number(sub_total_value).toFixed(2));
		
				console.log(sub_total);
			}
		});


    $('tbody').delegate('.products', 'select2:select', function(){
      var id = $(this).val();
      var row_id = $(this).closest('tr').attr('id');

      $.ajax({
        type: "GET",
        url: "/collections/other/get_latest_price",
        data: { _token: CSRF_TOKEN, id: id, row_id: row_id },
        dataType: "JSON",
        success: function(data){
          var price = "";
          var row_id = data.row_id;

          console.log(data);
          // alert(data.row_id);

          $.each(data.data, function(i, data){
            price = Number(data.selling_price);
            quantity = Number($('#invoice_table').find('tr#'+ row_id).find('.quantity').val());
            sub_total = price * quantity;
						amount_paid = Number($('#amount_paid').val());

            $('#invoice_table').find('tr#'+ row_id).find('.unit_cost').val(Number(price).toFixed(2));
            $('#invoice_table').find('tr#' + row_id).find('.sub_total').val(Number(sub_total).toFixed(2));
							amount_paid = amount_paid + price;
						$('#amount_paid').val(Number(amount_paid).toFixed(2));

          })
        }
      });
    });


    $('#btn_save').click(function(event){
      event.preventDefault();
      alert('button save');

	    var date = new Date();
	    var month = date.getMonth()+1;
	    var day = date.getDate();
			var hour = date.getHours();
			var minute = date.getMinutes();
			var second = date.getSeconds();

      var patient_name_value = $('#patient_name').val();
      var or_date_value = $('#or_date').val();
      var user_id_value = $('#user_id').val();
      var prefix_or_number_value = $('#or_number').val();
      var payment_mode_value = $('#payment_mode').val();
      var discount_percent_value = $('#discount_percent').val();
      var currency_value = $('#currency').val();
      var payment_type_value = $('#payment_type').val();
      var discount_computation_value = $('#discount_computation').val();
      var amount_paid_value = $('#amount_paid').val();
      var amount_tendered_value = $('#amount_tendered').val();
      var amount_change_value =  $('#change').val();

			var created_at_value = date.getFullYear() + '-' +
					(('' + month).length < 2 ? '0' : '') +
					month + '-' + (('' + day).length < 2 ? '0' : '') + day +
					' ' + hour + ':' + minute + ':' + second;

      var arrData=[];

      //loop over each  table row (tr)
      $('.payment_values').each(function(){
        var current_row = $(this);
        var product_id_value = current_row.find('.products').val();
        var quantity_value = current_row.find('.quantity').val();
        var unit_cost_value = current_row.find('.unit_cost').val();
        var discount_percent_value = current_row.find('.discount_percent').val();
        var discount_value_value = current_row.find('.discount_value').val();
        var sub_total_value = current_row.find('.sub_total').val();
        var obj = {};

        obj.prefix_or_number = prefix_or_number_value;
        obj.or_date = or_date_value;
        obj.patient_name = patient_name_value;
        obj.unit_cost = unit_cost_value;
        obj.quantity = quantity_value;
        obj.sub_total = sub_total_value;
        obj.currency_code = currency_value;
        obj.payment_type = payment_type_value;
        obj.payment_mode = payment_mode_value;
        obj.item_code = product_id_value;
        obj.user_id = user_id_value;
        obj.discount_percent = discount_percent_value;
        obj.discount_computation = discount_computation_value;
        obj.discount_value = discount_value_value;
        obj.amount_paid = amount_paid_value;
        obj.amount_tendered = amount_tendered_value;
        obj.amount_change = amount_change_value;
				obj.created_at = created_at_value;
        arrData.push(obj);

      });

      alert(arrData);
      console.log(arrData);
      console.log(created_at_value);

      $.ajax({
        type: "POST",
        url: "/collections/other/store_payment",
        data: { _token: CSRF_TOKEN, data: arrData },
        dataType: "JSON",
        success: function(data){
          alert(data);
          console.log(data);
        }
      });

    });


    $('#invoice_table').on('click', '.select-rows', function(){
      var current_row = $(this).closest('tr');
      var product_id = current_row.find('.products').val();
      var quantity = current_row.find('.quantity').val();
      var unit_cost = current_row.find('.unit_cost').val();
      var discount_percent = current_row.find('.discount_percent').val();
      var discount_value = current_row.find('.discount_value').val();
      var sub_total = current_row.find('.sub_total').val();

      var data = 'product id: ' + product_id + '\n' +
                  'quantity: ' + quantity + '\n' +
                  'unit cost: ' + unit_cost + '\n' +
                  'discount percent: ' + discount_percent + '\n' +
                  'discount value: ' + discount_value + '\n' +
                  'sub total: ' + sub_total + '\n';

      alert(data);
      console.log(data);

    });




  }); // $.(document).ready(function(){})
</script>

<script>
	$(document).ready(function() {
		$("#patient_name").select2({
			placeholder: 'Select a patient name',
			allowClear:true,
		});
	});

</script>

<!-- <script type="text/javascript">
  $(document).ready(function(){
       $('tbody').delegate('.quantity, .unit_cost, .discount_percent, .discount_value, .sub_total', 'keyup', function(){
      alert("test");
    });
  });
</script> -->



<script type="text/javascript">
  $(document).ready(function(){
    // code to read selected table row cell data (values).
    $('#invoice_table').on('click', '#btn_save', function(){
      var current_row = $(this).closest('tr');

    })
  });
</script>

@endsection
