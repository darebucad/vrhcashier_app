@extends ('layouts.master')
@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
		<h2 class="h5">Other Collection</h2>

		@if (session('status'))
	    <div class="alert alert-success" role="alert">
	        {{ session('status') }}
	    </div>
		@endif

		<!-- <div class="btn-toolbar mb-2 mb-md-0">
		  <div class="btn-group mr-2">
		    <button class="btn btn-sm btn-outline-secondary">Share</button>
		    <button class="btn btn-sm btn-outline-secondary">Export</button>
		  </div>
		  <button class="btn btn-sm btn-outline-secondary dropdown-toggle">
		    <span data-feather="calendar"></span>
		    	This week
		  </button>
		</div> -->

	</div>

	<div class="row">
    <a class="btn btn-sm btn-primary" href="{{ route('collections.other.create',['' => Auth::user()->id]) }}">Create</a>
  </div>

  <br />

	<style rel="stylsheet" type="text/css" media="screen">
	
	</style>

  <table id="other_collection_table" class="table table-sm table-striped table-hover" style="width:100%">
  	<thead>
  		<tr>
  			<th>O.R. Date</th>
        <th>O.R. No.</th>
        <th>Patient Name</th>
        <th>Discount (%)</th>
        <th style="text-align:right;">Amount Paid</th>
        <th>Cashier On-duty</th>
        <th>Status</th>
        <th>Action</th>
  		</tr>
  	</thead>
  	<!-- <tbody>
		@foreach ($payments as $payment)
			<tr id="{{ $payment->prefix_or_number }}" style="cursor: pointer;" >
				<td>{{ $payment->receipt_date }}</td>
				<td>{{ $payment->prefix_or_number }}</td>
				<td>{{ $payment->patient_name }}</td>
				<td>{{ $payment->discount }}</td>
        <td align="right">{{ $payment->amount_paid }}</td>
				<td>{{ $payment->name }}</td>
				<td>{{ $payment->payment_status }}</td>
				<td>
          <a href="#" class="btn btn-sm btn-outline-danger print"><span data-feather="printer"></span> Print Receipt</a>
          <a href="#" class="btn btn-sm btn-outline-info cancel" style=@if(Auth::user()->isAdmin === '0') display:none @endif><span data-feather="x"></span> Cancel Payment</a>
    		</td>
			</tr>
		@endforeach
  	</tbody> -->
  </table>
</main>

<script type="text/javascript">
	$(document).ready(function(){
		var table = $('#other_collection_table').DataTable({
			"processing": true,
			"ordering": false,
			"lengthMenu": [[50, 500, 2000, -1], [50, 500, 2000, "All"]],
			"pageLength": 50,
			"ajax": {
				"url": "/collections/outpatient/get-other-collection-data",
				"dataSrc": "data"
			},
			"columnDefs": [
				{
					targets: [4],
					className: "text-right"
				}
			],
			"columns": [
				{ "data": "receipt_date" },
				{ "data": "prefix_or_number" },
				{ "data": "patient_name" },
				{ "data": "discount" },
				{ "data": "amount_paid",
					// Include thousands separator to the number
					render: $.fn.dataTable.render.number( ',', '.', 2, '' )
				},
				{ "data": "name" },
				{ "data": "payment_status" },
				{ defaultContent: '<button class="btn btn-sm btn-outline-danger print"><span data-feather="printer"></span> Reprint Receipt</button>' }
			],
			"order": [
				[ 0, "desc"]
			]

		});

		$('#other_collection_table tbody').on('click', '.print', function () {
      var row = $(this).closest('tr');
      var data = table.row( row ).data().prefix_or_number;
      console.log(data);
      window.location.href = '/collections/other/print/pdf/' + data;
    });

	});
</script>

<script type="text/javascript">
	$(document).ready(function(){
		$('.print').click(function(){
			var row_id = $(this).closest('tr').attr('id');
			alert('print receipt clicked');
			alert(row_id);
			window.location.replace('/collections/other/print/pdf/' + row_id);
		});
	});
</script>




@endsection
