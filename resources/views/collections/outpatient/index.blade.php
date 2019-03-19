@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

    <h1 class="h5">Out-Patient Payment</h1>

    <!-- <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <button class="btn btn-sm btn-outline-secondary">Share</button>
        <button class="btn btn-sm btn-outline-secondary">Export</button>
      </div>
      <button id="week" class="btn btn-sm btn-outline-secondary dropdown-toggle">
        <span data-feather="calendar"></span>
          This week
      </button>
    </div> -->
  </div>

  <div class="row">
    <a class="btn btn-sm btn-primary" href="{{ route('collections.outpatient.create',['' => Auth::user()->id]) }}">Create</a>
  </div>

  <br />
  <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">

  <table id="example" class="table table-sm table-striped table-hover" style="width:100%">
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

<script type="text/javascript">
  $(document).ready(function() {
    var table = $('#example').DataTable({
      "processing": true,
      "ordering": false,
      "lengthMenu": [[50, 500, 1000, -1], [50, 500, 1000, "All"]],
      "pageLength": 50,
      // "dom": '<f>rt<"bottom"lip><"clear">',
      "ajax": {
          "url": "/collections/outpatient/get_outpatient_payment_data",
          "dataSrc": "data"
      },
      "columnDefs": [
        {
          targets: [4],
          className: 'text-right'
        },
      ],
      "columns": [
        { "data": "or_date" },
        { "data": "or_no_prefix" },
        { "data": "patient_name" },
        { "data": "discount" },
        { "data": "amount_paid" ,
          // Include thousands separator to the number
          render: $.fn.dataTable.render.number( ',', '.', 2, '' )},
        { "data": "employee_name" },
        { "data": "status" },
        { defaultContent: '<button class="btn btn-sm btn-outline-danger print"><span data-feather="printer"></span> Reprint Receipt</button>' }
      ],
      "order": [[ 0, "desc" ]]
    });



    $('#example tbody').on('click', '.print', function () {
      var row = $(this).closest('tr');
      var data = table.row( row ).data().or_no_prefix;
      console.log(data);
      window.location.href = '/collections/outpatient/print/pdf/' + data;
    });
  });
</script>

<script type="text/javascript">
  $(document).on('click', '.cancel', function(){
    var id = $(this).attr('id');
    var user_id = $('#user_id').val();

    if (confirm('Are you sure you want to cancel this payment?')) {
      $.ajax({
        type: "GET",
        url: "/collections/outpatient/cancel/payment",
        data: {id:id, user_id:user_id},
        success:function(data){
          // alert(data);
        }
      });
    }
  });
</script>

<script type="text/javascript">
  $(document).on('click', '.edit', function() {
      var cid = $(this).attr('id');
      var cval = $(this).text();
      var user_id = $('#user_id').val();

      alert(cid);
      alert(cval);

      $.ajax({
        type: "GET",
        url: "/collections/outpatient/payment/edit",
        data: {id:id, user_id:user_id},
        dataType: "JSON",
        success:function(data){
          console.log(data);

        }
      });
  });
</script>

<script type="text/javascript">
  $(document).ready(function(){
    $('.draft').click(function(){
      alert('clicked draft button');
    });
  });
</script>

@endsection
