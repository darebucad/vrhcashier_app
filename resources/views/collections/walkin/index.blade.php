@extends ('layouts.master')
@section ('content')
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h5">Index Walk-In Payment</h1>
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
    <a class="btn btn-sm btn-primary" href="{{ route('collections.walkin.create') }}">Create</a>
  </div>

  <br />

  <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">

  <table id="walkin_table" class="table table-sm table-striped table-hover" style="width:100%">
        <thead>
          <tr>
            <!-- Table Header -->
            <th>O.R. Date</th>
            <th>O.R. No.</th>
            <th>Patient Name</th>
            <th>Discount (%)</th>
            <th style="text-align:right">Amount Paid</th>
            <th>Cashier On-duty</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
    </table>



</main>

@endsection
