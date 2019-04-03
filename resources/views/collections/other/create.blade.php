@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
		<h1 class="h5"><a href="#">Other Collection</a> / Create</h1>
		<!-- <h1 class="h5"><a href="#">Out-Patient Payment</a> / Create Out-Patient Payment</h1> -->
  		@if (session('status'))
          <div class="alert alert-success" role="alert">
              {{ session('status') }}
          </div>
  		@endif

		<div class="btn-toolbar mb-2 mb-md-0">
		  <div class="btn-group mr-2">
		    <button class="btn btn-sm btn-outline-secondary" id ="btn_print">
					<span data-feather="printer"></span>
					Print Receipt
				</button>
		    <button class="btn btn-sm btn-outline-secondary">Export</button>
		  </div>


		  <!-- <button class="btn btn-sm btn-outline-secondary dropdown-toggle">
		    <span data-feather="calendar"></span>
		    This week
		  </button> -->
		</div>
	</div>


	<div class="alert alert-success alert-dismissable" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      Payment was successfully saved.
  </div>

  <!-- <form action="/collections/other/create/payment" method="post" > -->
  <form id="collections_other">
    @csrf

    <div class="row" style="margin-top:10px;">
      <button class="btn btn-sm btn-primary" id="btn_save">Save</button>
        <p id="button-cancel">or <a class="btn-link" href="{{ url('collections/other') }}">Cancel</a></p>
    </div>

    <br />
		<br>

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
		<input type="hidden" name="charge_code" id="charge_code" value="">
		<input type="hidden" name="charge_table" id="charge_table" value="">
		<input type="hidden" name="product_id" id="product_id" value="">

		<!-- Patient name control-->
  	<div id="patient_name_field" class="form-group row">

  			<label class="col-md-1 col-form-label text-md-left">{{ __('Patient Name') }}</label>

  			<div class="col-md-9">
					<!-- <select class="form-control" name="patient_name" id="patient_name" required>
						<option value=""> </option>
					</select> -->
					<input type="text" name="patient_name" value="" id="patient_name" class="form-control" style="background-color: #99ccff!important;" required>
  			</div>

				<!-- <div class="col-md-2">
					<a class="nav-link disabled" href="#" id="create_new">
						<span data-feather="save"></span>
						Create New <span class="sr-only"></span>
					</a>
				</div> -->

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

			<label class="col-md-1 col-form-label text-md-left">User:</label>
			<label class="col-md-1 col-form-label text-md-left">{{ Auth::user()->name }}</label>

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
  				<option value=" "> </option>
  				<option value="C" selected>Cash</option>
  				<option value="X">Check</option>
  			</select>
  		</div>

  		<label for="discount_percent" class="col-md-1 col-form-label text-md-left">{{ __('Discount (%)') }}</label>

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

			<label for="amount_paid" class="col-md-2 col-form-label text-md-right">{{ __('Total Amount') }}</label>

      <div class="col-md-2">
        <input id="amount_paid" type="text" class="form-control" name="amount_paid" style="background-color:#99ccff!important;font-weight: bold; font-size: 25px;" value="0.00" autofocus>
        @if ($errors->has('amount_paid'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('amount_paid') }}</strong>
            </span>
        @endif
      </div>

    </div>

    <div class="form-group row" style="margin-top: -10px;">
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


			<label for="amount_tendered" class="col-md-2 col-form-label text-md-right">{{ __('Amount Tendered') }}</label>

			<div class="col-md-2">
				<input id="amount_tendered" type="text" class="form-control" name="amount_tendered"  style="background-color:#99ccff!important;font-weight: bold; font-size: 25px;" value="0.00" autofocus>

				@if ($errors->has('amount_tendered'))
						<span class="invalid-feedback" role="alert">
								<strong>{{ $errors->first('amount_tendered') }}</strong>
						</span>
				@endif

			</div>

    </div>

    <div class="form-group row" style="margin-top:-10px;">
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
				<button type="button" name="clear_discounts" id="clear_discounts" class="btn btn-outline-secondary btn-sm">
					Clear Discounts
				</button>
			</div>

			<label for="change" class="col-md-2 col-form-label text-md-right">{{ __('Change') }}</label>

      <div class="col-md-2">
        <input id="change" type="text" class="form-control" name="change" style="font-weight: bold; font-size:25px;" value="0.00" autofocus>
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
          <th style="width:5%">Pay?</th>
          <th style="width:5%">Disc?</th>
          <th style="width:35%">Description</th>
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

				@for ($i=0; $i < 2; $i++)
				<tr>
					<td colspan="9" style="color:white;"> .</td>
				</tr>
				@endfor
				<tr>
					<td colspan="7" align="right">Total: </td>
					<td colspan="1" align="right" id="total_value" style="font-weight:bold; font-size:20px;">0.00</td>
					<td colspan="1" align="left"></td>
				</tr>
      </tbody>
    </table>
  </div>
</main>

<!-- <script>
	$(document).ready(function() {
		$("#patient_name").select2({
			placeholder: 'Select a patient name',
			allowClear:true,
			minimumResultsForSearch: 3, // at least 3 characters to start displaying records
			ajax: {
				url: '/collections/other/get_patient_list',
				dataType: 'JSON',
			},
		});
	});
</script> -->

<script type="text/javascript">
	$(document).ready(function() {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var table = $('#invoice_table');
    var newRow = '';
    var current_row = $('#invoice_table tr')
    var rowNum = 0;
		var products = {};

		// Add row event
    $('#add_row').click(function(event) {
      event.preventDefault();
      newRow =
      	'<tr id=' + rowNum + ' class="payment_values">' +
        '<td style="width:5%"><input type="checkbox" name="pay_checkbox" class="pay_checkbox" checked></td>' +
        '<td style="width:5%"><input type="checkbox" name="discount_checkbox" class="discount_checkbox" value=' + rowNum + '></td>' +
        '<td style="width:35%"><select class="products form-control form-control-sm" style="width:100%"><option> </option></select></td>' +
        '<td style="width:10%" align="right"><input type="text" name="quantity[]" class="quantity form-control form-control-sm" style="width:100%; text-align: right" value="1.00"></td>' +
        '<td style="width:10%" align="right"><input type="text" name="unit_cost[]" class="unit_cost form-control form-control-sm"style="width:100%; text-align: right"></td>' +
        '<td style="width:10%" align="right"><input type="text" name="discount_percent[]" class="discount_percent form-control form-control-sm" style="width:100%; text-align: right"></td>' +
        '<td style="width:10%" align="right"><input type="text" name="discount_value[]" class="discount_value form-control form-control-sm" style="width:100%; text-align: right"></td>' +
        '<td style="width:10%"><input type="text" name="sub_total[]" class="sub_total form-control form-control-sm" style="width:100%; text-align: right"></td>' +
        '<td style="width:5%"><a href="#" class="delete-rows" id="delete_row"><span data-feather="trash-2"></span>Delete</a></td>'+
        '</tr>';
				table.prepend(newRow);
				rowNum = rowNum + 1;

				// Fetch data to select2 class products
				$('.products').select2({
					placeholder: 'Select a product/service',
					// minimumInputLength: 1, // at least 1  characters to display records
					allowClear: true,
					ajax: {
						url: '/collections/other/show_products',
						dataType: 'JSON',
						delay: 250,
						data: function (params) {
						 	return {
								term: params.term,
								page: params.page,
								type: 'public',
								success: function(resource){
									console.log(resource);
								}
							};
						},
						processResults: function (data, params) {
							params.page = params.page || 1;
							return {
								results:data.items,
								pagination: {
									more: (params.page * 30) < data.total_count
								}
							};
						},
						cache: true
						// transport: function(params, success, failure) {
						// 	var $request = $.ajax(params);
						//
						// 	$request.then(success);
						// 	$request.fail(failure);
						//
						// 	return $request;
						// }
					},
					// escapeMarkup: function (markup) { return markup; }

				});
    });

		$('tbody').delegate('.products', 'select2:select', function(){
			var id = $(this).val();
			var row_id = $(this).closest('tr').attr('id');
			var price = 0;
			var description = $('tr#'+ row_id + ' .products :selected').text();

			// alert('click table product');

			$.ajax({
				type: "GET",
				url: "/collections/other/get_latest_price",
				data: { _token: CSRF_TOKEN, id: id, row_id: row_id, description: description },
				dataType: "JSON",
				success: function(data) {
					console.log(data);

					row_id = data.row_id;

					$.each(data.data, function(i, data) {
						var item_code = '';
						var price = 0;
						var quantity = 0;
						var sub_total = 0;
						// var amount_paid = 0;
						var charge_code = '';
						var charge_table = '';
						var discount_initial_value = Number(0.00);

						item_code = data.item_code;
						price = Number(data.selling_price);
						quantity = Number($('#invoice_table').find('tr#'+ row_id).find('.quantity').val());

						// *Get the sum of price & quantity
						sub_total = price * quantity;

						// amount_paid = Number($('#amount_paid').val());
						charge_code = data.charge_code;
						charge_table = data.charge_table;

						total_value = Number($('#invoice_table').find('#total_value').text());
						total_value = total_value + price;
						// amount_paid = amount_paid + price;
						// var comma_sub_total = numberWithCommas(Number(sub_total).toFixed(2));

						$('#amount_paid').val(Number(total_value).toFixed(2));

						$('#invoice_table').find('tr#'+ row_id).find('.unit_cost').val(Number(price).toFixed(2));
						$('#invoice_table').find('tr#'+ row_id).find('.discount_percent').val(discount_initial_value.toFixed(2));
						$('#invoice_table').find('tr#'+ row_id).find('.discount_value').val(discount_initial_value.toFixed(2));
						$('#invoice_table').find('tr#' + row_id).find('.sub_total').val(sub_total.toFixed(2));
						$('#invoice_table').find('#total_value').text(Number(total_value).toFixed(2));
						$('#invoice_table').find('tr#' + row_id).find('.pay_checkbox').val(item_code);
						$('#charge_code').val(charge_code);
						$('#charge_table').val(charge_table);

						// alert('class products: ' + item_code);

					}) // each data.data function
				}
			});
		});

		// Click event delete row
    table.on('click', '#delete_row', function() {
			var total_value = 0;
      $(this).closest('tr').remove();

			$('.payment_values').each(function(){
				var current_row = $(this);
				var quantity = Number(current_row.find('.quantity').val());
				var unit_cost = Number(current_row.find('.unit_cost').val());
				var sub_total = Number(quantity * unit_cost);
				total_value = total_value + sub_total;
			});
				total_value = Number(total_value).toFixed(2);
				$('#total_value').text(total_value);
    });

		// Quantity keydown event press enter
		table.on('keydown', '.quantity', function(event) {
			var id = $(this).val();
      var row_id = $(this).closest('tr').attr('id');
			var sub_total_value = 0;
			if(event.which == 13){
				event.preventDefault();
				// alert('pressed enter at quantity');
				// alert(id);
				price = Number($('#invoice_table').find('tr#'+ row_id).find('.unit_cost').val());
				quantity = Number($('#invoice_table').find('tr#'+ row_id).find('.quantity').val());
				sub_total = price * quantity;
				$('#invoice_table').find('tr#' + row_id).find('.discount_percent').val(Number(0.00).toFixed(2));
				$('#invoice_table').find('tr#' + row_id).find('.discount_value').val(Number(0.00).toFixed(2));
				$('#invoice_table').find('tr#' + row_id).find('.sub_total').val(Number(sub_total).toFixed(2));
				$('#invoice_table').find('tr#'+ row_id).find('.quantity').val(Number(quantity).toFixed(2));
				$('.payment_values').each(function(){
					var current_row = $(this);
					sub_total_value =  sub_total_value + Number(current_row.find('.sub_total').val());
				})
				$('#amount_paid').val(Number(sub_total_value).toFixed(2));
				$('#invoice_table').find('#total_value').text(Number(sub_total_value).toFixed(2));
				console.log(sub_total);
			}
		});


		// Unit cost keydown event press enter
		table.on('keydown', '.unit_cost', function(event) {
			var id = $(this).val();
			var row_id = $(this).closest('tr').attr('id');
			var sub_total_value = 0;
			var discount_initial_value = 0.00;

			if (event.which == 13) {
				event.preventDefault();
				// alert('Pressed enter at unit cost column');
				// alert(id);

				price = Number($('#invoice_table').find('tr#' + row_id).find('.unit_cost').val());
				quantity = Number($('#invoice_table').find('tr#' + row_id).find('.quantity').val());
				sub_total = (price * quantity);

				$('#invoice_table').find('tr#' + row_id).find('.sub_total').val(Number(sub_total).toFixed(2));
				$('#invoice_table').find('tr#' + row_id).find('.quantity').val(Number(quantity).toFixed(2));
				$('#invoice_table').find('tr#' + row_id).find('.unit_cost').val(Number(price).toFixed(2));
				$('#invoice_table').find('tr#' + row_id).find('.discount_percent').val(Number(discount_initial_value).toFixed(2));
				$('#invoice_table').find('tr#' + row_id).find('.discount_value').val(Number(discount_initial_value).toFixed(2));

				$('.payment_values').each(function(){
					var current_row = $(this);
					sub_total_value = sub_total_value + Number(current_row.find('.sub_total').val());
				});

				$('#amount_paid').val(Number(sub_total_value).toFixed(2));
				$('#invoice_table').find('#total_value').text(Number(sub_total_value).toFixed(2));

				console.log(sub_total);
			}
		});


		// Keydown event discount percent
		table.on('keydown', '.discount_percent', function(event){
			var id = $(this).val();
			var discount_percent = Number(id) / 100;
			var row_id = $(this).closest('tr').attr('id');
			var discount_value = 0;
			var sub_total = 0;

			if(event.which == 13){
				event.preventDefault();
				// alert('pressed enter at discount percent row');
				// alert(id);

				price = Number($('#invoice_table').find('tr#'+ row_id).find('.unit_cost').val());
				quantity = Number($('#invoice_table').find('tr#'+ row_id).find('.quantity').val());
				sub_total = price * quantity;
				discount_value = sub_total * discount_percent;
				discount_percent = discount_percent * 100

				$('#invoice_table').find('tr#'+ row_id).find('.discount_percent').val(discount_percent.toFixed(2));
				$('#invoice_table').find('tr#'+ row_id).find('.discount_value').val(Number(discount_value).toFixed(2));

				sub_total = sub_total - discount_value;

				$('#invoice_table').find('tr#'+ row_id).find('.sub_total').val(Number(sub_total).toFixed(2));

				sub_total = 0;

				$('.payment_values').each(function(){
					var current_row = $(this);
					sub_total = sub_total + Number(current_row.find('.sub_total').val());
				});

				sub_total = Number(sub_total).toFixed(2);
				// $('#amount_paid').val(sub_total);
				$('#invoice_table').find('#total_value').text(sub_total);

			}
		}); // End of event keydown .discount_percent


		// Discount value keydown event
		table.on('keydown', '.discount_value', function(event){
			var row_id = $(this).closest('tr').attr('id');
			var id = $(this).val();
			var discount_percent = 0;
			var discount_value = id;
			var sub_total = 0;

			if (event.which == 13) {
				event.preventDefault();

				price = Number($('#invoice_table').find('tr#'+ row_id).find('.unit_cost').val());
				quantity = Number($('#invoice_table').find('tr#'+ row_id).find('.quantity').val());
				sub_total = Number(price * quantity);

				$('#invoice_table').find('tr#'+ row_id).find('.discount_value').val(Number(discount_value).toFixed(2));

				sub_total = sub_total - discount_value;
				$('#invoice_table').find('tr#'+ row_id).find('.sub_total').val(Number(sub_total).toFixed(2));

				sub_total = Number(0);
				$('.payment_values').each(function(){
					var current_row = $(this);
					sub_total = sub_total + Number(current_row.find('.sub_total').val());
				});

				sub_total = Number(sub_total).toFixed(2);
				$('#invoice_table').find('#total_value').text(sub_total);
				$('#amount_paid').val(sub_total);
			}
		});

		// Focusout event quantity
		table.on('focusout', '.quantity', function(event) {
			var id = $(this).val();
      var row_id = $(this).closest('tr').attr('id');
			var sub_total_value = 0;
			event.preventDefault();

			price = Number($('#invoice_table').find('tr#'+ row_id).find('.unit_cost').val());
			quantity = Number($('#invoice_table').find('tr#'+ row_id).find('.quantity').val());
			sub_total = price * quantity;

			// discount_percent = Number($('#invoice_table').find('tr#' + row_id).find('.discount_percent').val());
			//
			// if (discount_percent > 0) {
			// 	sub_total = sub_total - (price * quantity) * (discount_percent / 100);
			// }
			$('#invoice_table').find('tr#'+ row_id).find('.quantity').val(Number(quantity).toFixed(2));
			$('#invoice_table').find('tr#' + row_id).find('.discount_percent').val(Number(0.00).toFixed(2));
			$('#invoice_table').find('tr#' + row_id).find('.discount_value').val(Number(0.00).toFixed(2));
			$('#invoice_table').find('tr#' + row_id).find('.sub_total').val(Number(sub_total).toFixed(2));

			$('.payment_values').each(function(){
				var current_row = $(this);
				sub_total_value =  sub_total_value + Number(current_row.find('.sub_total').val());
			})
			$('#invoice_table').find('#total_value').text(sub_total_value.toFixed(2));
			$('#amount_paid').val(sub_total_value.toFixed(2));
			// $('#amount_paid').val(Number(sub_total_value).toFixed(2));
			console.log(sub_total);

		});

    $('#btn_save').click(function(event) {
      event.preventDefault();
      // alert('clicked save button');

	    var date = new Date();
	    var month = date.getMonth() + 1;
	    var day = date.getDate();
			var hour = date.getHours();
			var minute = date.getMinutes();
			var second = date.getSeconds();

      var patient_name_value = $('#patient_name').val();
      var or_date_value = $('#or_date').val();
      var user_id_value = $('#user_id').val();
      var prefix_or_number_value = $('#or_number').val();
      var payment_mode_value = $('#payment_mode').val();
			var discount_name_value = $('#discount_percent').val();
      var currency_value = $('#currency').val();
      var payment_type_value = $('#payment_type').val();
      var discount_computation_value = $('#discount_computation').val();
      // var amount_paid_value = $('#amount_paid').val();
			var amount_paid_value = $('#total_value').text();
      var amount_tendered_value = $('#amount_tendered').val();
      var amount_change_value =  $('#change').val();
			var charge_code_value = $('#charge_code').val();
			var charge_table_value = $('#charge_table').val();
			var discount_percent_value = 0;
			var arrData=[];
			var created_at_value = date.getFullYear() + '-' + (('' + month).length < 2 ? '0' : '') +
					month + '-' + (('' + day).length < 2 ? '0' : '') + day + ' ' + hour + ':' + minute + ':' + second;


			if (discount_name_value == "SENIOR" || discount_name_value == "PWD") {
				discount_percent_value = Number(20).toFixed(2);
			} else {
				discount_percent_value = Number(discount_name_value).toFixed(2);
			}


			if (patient_name_value == ' ' || patient_name_value == null) {
				alert('Please fill up Field: Patient Name');
			} else {

				var row_count = $('#invoice_table tbody tr').length;
				if (row_count <= 2) {
					alert('Please add at least one product/service to proceed');

				} else {
					// loop over each  table row (tr)
		      $('.payment_values').each(function(){
		        var current_row = $(this);
		        var product_id_value = current_row.find('.pay_checkbox').val();
		        var quantity_value = current_row.find('.quantity').val();
		        var unit_cost_value = current_row.find('.unit_cost').val();
		        var discount_percent_table_value = current_row.find('.discount_percent').val();
		        var discount_value_value = current_row.find('.discount_value').val();
		        var sub_total_value = current_row.find('.sub_total').val();
						var is_pay_value = 0;
						var is_discount_value = 0

						if (current_row.find('.pay_checkbox').is(':checked')) {
							is_pay_value = 1;

						}

						if (current_row.find('.discount_checkbox').is(':checked')) {
							is_discount_value = 1;

						}



						  // $('#show').html(this.checked ? this.value : '');
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
						obj.discount_name = discount_name_value;
						obj.discount_percent = discount_percent_table_value;
						obj.discount_computation = discount_computation_value;
						obj.discount_value = discount_value_value;
						obj.amount_paid = amount_paid_value;
						obj.amount_tendered = amount_tendered_value;
						obj.amount_change = amount_change_value;
						obj.created_at = created_at_value;
						obj.charge_code = charge_code_value;
						obj.charge_table = charge_table_value;
						obj.is_pay = is_pay_value;
						obj.is_discount = is_discount_value;

						arrData.push(obj);
		      });

		      // alert(arrData);
		      // console.log(arrData);
		      // console.log(created_at_value);

		      $.ajax({
		        type: "POST",
		        url: "/collections/other/store_payment",
		        data: { _token: CSRF_TOKEN, data: arrData, or_number: prefix_or_number_value },
		        dataType: "JSON",
		        success: function(data){
		          console.log(data);

							 $('.alert').show();

							 $('#btn_print').click();
		        }
		      }); // End of  url: "/collections/other/store_payment",


					// similar behavior as an HTTP redirect
					// window.location.replace("/collections/other");

				} // End of if (row_count < 2) {
			} // End of if (patient_name_value == ' ' || patient_name_value == null) {
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
      // alert(data);
      console.log(data);
    });

		//
		// $('#create_new').click(function(event){
		// 	var id = $(this).val();
		// 	var patient_name = $('#patient_name').val();
		// 	var data = $('#patient_name').select2('data');
		// 	var description = $('select2-container select2-container--default select2-container--open select2-dropdown select2-dropdown--below select2-search select2-search--dropdown input.select2-search__field').val();
		//
		// 	event.preventDefault();
		// 	alert('clicked create new');
		// 	alert(data[0].text);
		// 	alert(data[0].id);
		// 	alert(id);
		// 	alert(description);
		//
		// 	// var newOption = new Option(data[0].text, data[0].id, true, true);
		// 	// $('#patient_name').append(newOption).trigger('change');
		//
		// 	$('#patient_name').val('Select2 Config');
		//
		// });
  }); // $.(document).ready(function(){})
</script>

<!-- keywords.select2({
            tags: true,
            createTag: function (params) {
                var term = $.trim(params.term);
                var count = 0
                var existsVar = false;

                //check if there is any option already
                if($('#keywords option').length > 0){
                    $('#keywords option').each(function(){
                        if ($(this).text().toUpperCase() == term.toUpperCase()) {
                            existsVar = true
                            return false;
                        }else{
                            existsVar = false
                        }
                    });
                    if(existsVar){
                        return null;
                    }
                    return {
                        id: params.term,
                        text: params.term,
                        newTag: true
                    }
                }

                //since select has 0 options, add new without comparing
                else{
                    return {
                        id: params.term,
                        text: params.term,
                        newTag: true
                    }
                }
            },
            maximumInputLength: 20, // only allow terms up to 20 characters long
            closeOnSelect: true
        }) -->



<!-- Start of apply to all discount button -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#apply_discount_all').click(function(event){
			event.preventDefault();
			// alert('clicked apply discount all button');

			var discount_percent = $.trim($('#discount_percent').val());
			var discount_name = discount_percent;

			if (discount_percent === '') {
				discount_percent = 'No discount(%) found';
				// alert(discount_percent);
			} else {

				if (discount_percent === 'SENIOR' || discount_percent ==='PWD') {
					discount_percent = 0.20;

				} else {
					discount_percent = discount_percent / 100;
				}

				var total_value = 0;

				$('input[name="discount_checkbox"]:not(:checked)').each(function(){
					$(this).prop('checked', true);
				});

				$('.payment_values').each(function(){

					var current_row = $(this);
	        var product_id_value = Number(current_row.find('.products').val());
	        var quantity_value = Number(current_row.find('.quantity').val());
	        var unit_cost_value = Number(current_row.find('.unit_cost').val());
	        var discount_percent_value = Number(current_row.find('.discount_percent').val());
	        var sub_total_value = Number(current_row.find('.sub_total').val());
					var discount_value_value = sub_total_value * discount_percent;
					var sub_total_final_value = sub_total_value - discount_value_value;

					discount_percent = discount_percent * 100;

					current_row.find('.discount_percent').val(Number(discount_percent).toFixed(2));
					current_row.find('.discount_value').val(Number(discount_value_value).toFixed(2));
					current_row.find('.sub_total').val(Number(sub_total_final_value).toFixed(2));

					discount_percent = discount_percent / 100;

					total_value = total_value + Number(current_row.find('.sub_total').val());
				});

				// Set new value of total values
				$('#invoice_table').find('#total_value').text(Number(total_value).toFixed(2));
				$('#amount_paid').val(Number(total_value).toFixed(2));

			}
		}); // End of apply to all discount button



		// start of apply to selected discount button
		$('#apply_discount_selected').click(function(event){
			event.preventDefault();
			// alert('clicked apply discount selected button');

			/* declare an checkbox array */
			var id = [];

			var discount_percent = $.trim($('#discount_percent').val()); // discount percent  25
			var total_value = 0;

			$('input[name="discount_checkbox"]:checked').each(function(){
				id.push($(this).val());
			});

			if (discount_percent === '') {
				alert('No Discount(%) found.');
			} else {

				if (discount_percent === 'SENIOR' || discount_percent === 'PWD') {
					discount_percent = 0.20;
				} else {
					discount_percent = discount_percent / 100;
				}

				/* check if there is checkedValues checkboxes, by default the length is 1 as it contains one single comma */
				if (id.length > 0) {

					/* look for all checkboxes and check if it was checked */
					$('input[name="discount_checkbox"]:checked').each(function() {
						var row_id = $(this).val();
						var discount_name = discount_percent * 100; // discount = 25
						var sub_total = Number($('#invoice_table').find('tr#'+ row_id).find('.sub_total').val());
						var discount_value = 0;

						// alert(row_id);
						// alert(sub_total);
						discount_value = sub_total * discount_percent;
						sub_total = sub_total - discount_value;

						$('#invoice_table').find('tr#'+ row_id).find('.discount_value').val(Number(discount_value).toFixed(2));
						$('#invoice_table').find('tr#'+ row_id).find('.discount_percent').val(Number(discount_name).toFixed(2));
						$('#invoice_table').find('tr#'+ row_id).find('.sub_total').val(Number(sub_total).toFixed(2));

					});

					$('.payment_values').each(function(){
						var current_row = $(this);
						var sub_total = Number(current_row.find('.sub_total').val());
						total_value = total_value + sub_total;
					});

					total_value = Number(total_value).toFixed(2);
					$('#total_value').text(total_value);
					$('#amount_paid').val(total_value);

				} else {
					alert("Please select at least check one checkbox");

				} // if there is no selected checkboxes
			} // discount is null
		});

		$('#clear_discounts').click(function(event){
			event.preventDefault();
			var total_value = 0;
			// alert('clear discounts button clicked');
			var q = confirm('Do you want to clear all the discounts made?\nYour changes will be lost if you clear the discounts.');

			if (q == true) {
				$('#discount_percent').val('');

				$('input[name="pay_checkbox"]:not(:checked)').each(function(){
					$(this).prop("checked", true);
				});

				$('input[name="discount_checkbox"]:checked').each(function(){
					$(this).prop("checked", false);
				});


				$('.payment_values').each(function(){
					var current_row = $(this);
					var quantity = Number(current_row.find('.quantity').val());
					var unit_cost = Number(current_row.find('.unit_cost').val());
					var sub_total = Number(quantity * unit_cost);
					var discount_default_value = Number(0.00).toFixed(2);
					total_value = total_value + sub_total;

					current_row.find('.discount_percent').val(discount_default_value);
					current_row.find('.discount_value').val(discount_default_value);
					current_row.find('.sub_total').val(Number(sub_total).toFixed(2));
				});

					total_value = Number(total_value).toFixed(2);
					$('#total_value').text(total_value);
					$('#amount_paid').val(total_value);

			} else {

			}

		}); // end of clear discounts function


		$('#update_totals').click(function(event){
			event.preventDefault()
			// alert('clicked update totals button');

			/* declare an checkbox array */
			var id = [];
			var row_id = 0;

			$('input[name="pay_checkbox"]:not(:checked)').each(function(){
				row_id = $(this).closest('tr').attr('id');

				id.push(row_id);
				// alert(row_id);
			});

			if (id.length > 0) {
				var current_row = $(this);
				var null_value = Number(0.00).toFixed(2);
				var total = Number(0);
				var discount_percent = $('#discount_percent').val();
				var row_id = 0;
				var discount_id = [];
				var sub_total = 0;
				var discount_value = 0;

				$.each(id, function(i, value){
					row_id = value;
					$('#invoice_table').find('tr#' + row_id).find('.discount_percent').val(null_value);
					$('#invoice_table').find('tr#' + row_id).find('.discount_value').val(null_value);
					$('#invoice_table').find('tr#' + row_id).find('.sub_total').val(null_value);
					// alert(row_id);
					});

					if (discount_percent == '' || discount_percent == null || discount_percent == 0) {
						// code...
					}
					else {
						$('input[name="discount_checkbox"]:checked').each(function(){
							discount_id.push($(this).val());

						});

						if (discount_id.length > 0) {
							// var current_row = $(this);
							// alert(current_row);

							$.each(discount_id, function(i, value){
								row_id = value;
								sub_total = $('#invoice_table').find('tr#' + row_id).find('.sub_total').val();

								if (discount_percent == 'SENIOR' || discount_percent == 'PWD') {
									discount_percent = 20;
								}

								discount_value = (sub_total * (discount_percent/100));

								sub_total = sub_total - discount_value;

								$('#invoice_table').find('tr#' + row_id).find('.discount_percent').val(Number(discount_percent).toFixed(2));
								$('#invoice_table').find('tr#' + row_id).find('.discount_value').val(Number(discount_value).toFixed(2));
								$('#invoice_table').find('tr#' + row_id).find('.sub_total').val(Number(sub_total).toFixed(2));

								// alert(row_id);
							});
						}
					}

					$('.payment_values').each(function(){
						var current_row = $(this);
						var sub_total = Number(current_row.find('.sub_total').val());
						total = total + sub_total;
						// alert(total);

					});
					// alert(total);
					total = Number(total).toFixed(2);
					$('#total_value').text(total);
					$('#amount_paid').val(total);



			}
		})

	}); // end of document function
</script>

<script type="text/javascript">
	$(document).ready(function() {
		$('#btn_print').click(function(event) {
			event.preventDefault();
			var prefix_or_number = $('#or_number').val();
			// alert('button print receipt clicked');
			//
			// 	$.ajax({
			// 		type: 'GET',
			// 		url: '/collections/other/print/pdf',
			// 		data: { id: prefix_or_number },
			// 	});
			// $('#btn_save').click();
			window.location.replace("/collections/other/print/pdf/" + prefix_or_number);
		});
	});
</script>

<script type="text/javascript">

	data = [
		'Juan',
		'Thoo',
		'Thrie'
	];

	$('#patient_name').autocomplete({
		source: "{{ url('/collections/other/autocomplete-search') }}",
		// minLength: 2,
		select: function(key, value){
			// console.log(value);
			// alert('id: '+ value.item.id + '; ' + 'value:' + value.item.value);
		}
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
      $('#change').focus();
    }
  });




</script>

<script type="text/javascript">
  $('#collections_other').on('keyup keypress', function(e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
      e.preventDefault();
      return false;
    }
  });
</script>




@endsection
