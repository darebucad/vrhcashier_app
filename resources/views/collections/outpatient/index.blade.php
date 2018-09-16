@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Index Collections Outpatient</h1>
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
    <a class="btn btn-sm btn-primary" href="{{ route('collections.outpatient.create',['' => Auth::user()->id]) }}">Create</a>
  </div>

  <br />

  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

  <table id="outpatient_payment_table" class="table table-sm table-bordered " style="width:100%">
    <thead>
      <!-- Table Row -->
      <tr>
        <!-- Table Header -->
        <th>O.R. Date</th>
        <th>O.R. No.</th>
        <th>Patient Number</th>
        <th>Discount</th>
        <th>Amount Paid</th>
        <th>Cashier Duty</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
  </table>

  <script>
    $(document).ready(function() {
      $('#outpatient_payment_table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ route('collections.outpatient.getdata') }}",
        "columns": [
          {"data": "ordate"},
          {"data": "orno"},
          {"data": "hpercode"},
          {"data": "discount_percent"},
          {"data": "amt"},
          {"data": "entryby"},
          {"data": "status"},
          {"data": "action", orderable:false, searchable: false }
        ]
      });

    });
  </script>

</main>


@endsection