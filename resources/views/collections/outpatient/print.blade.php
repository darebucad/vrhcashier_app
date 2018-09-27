@extends ('layouts.master')

@section ('content')

	<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
	  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	    <h1 class="h2">Print Collections Outpatient</h1>
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

	  @csrf
	  <div class="row">

	  		<a class="btn btn-sm btn-primary" href="{{ url('/collections/outpatient/print/pdf', ['' => '']) }}">Print PDF</a>
	    
	  </div>

	  <br />

		<h4 align="center">Payment Details</h4>
	 	<table class="table table-borderless">
			<thead>
				<tr>
					<th>Description</th>
					<th>Account Code</th>
					<th>Amount</th>
				</tr>
			</thead>

			<tbody>
			@foreach ($payments as $payment)
				<tr>
					<td>{{ $payment->enccode }}</td>
					<td>{{ $payment->or_no_prefix }}</td>
					<td>{{ $payment->or_date }}</td>
					<td>{{ $payment->amount_paid }}</td>
					<td><a class="btn btn-sm btn-primary" href="{{ route('collections.outpatient.print.pdf', ['' => $payment->or_no_prefix ]) }}">Print PDF</a></td>
				</tr>
			@endforeach
			</tbody>
		</table>

	</main>

@endsection
