@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
		<h1 class="h2">Index Collections Other</h1>

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

	<div class="row">
    <a class="btn btn-sm btn-primary" href="{{ route('collections.other.create',['' => Auth::user()->id]) }}">Create</a>
  </div>

  <br />	

  <table id="other_payment_table" class="table table-sm table-striped table-hover" style="width:100%">
  	<thead>
  		<tr>
  			<th>O.R. Date</th>
        <th>O.R. No.</th>
        <th>Patient Name</th>
        <th>Discount (%)</th>
        <th>Amount Paid</th>
        <th>Cashier On-duty</th>
        <th>Status</th>
        <th>Action</th>
  		</tr>
  	</thead>
  	<tbody>
  		@foreach ($payments as $payment)
  			<tr style="cursor: pointer;">
  				<td>{{ $payment->or_date }}</td>
  				<td>{{ $payment->prefix_or_number }}</td>
  				<td>{{ $payment->lastname }}</td>
  				<td>{{ $payment->discount_name }}</td>
          <td align="right">{{ $payment->amount }}</td>
  				<td>{{ $payment->id }}</td>
  				<td>{{ $payment->status }}</td>
  				<td>
            <a href="{{ route('collections.outpatient.print.pdf', [ '' => $payment->preorno ]) }}" class="btn btn-sm btn-outline-danger">Print Receipt</a> 
            <a href="#" class="btn btn-sm btn-outline-info cancel" id="{{ $payment->preorno }}">Cancel Payment</a>
      		</td>
  			</tr>
  		@endforeach
  	</tbody>
  </table>

</main>
@endsection