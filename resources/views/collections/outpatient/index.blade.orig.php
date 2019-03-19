@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h4">Out-Patient Payment</h1>

    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <button class="btn btn-sm btn-outline-secondary">Share</button>
        <button class="btn btn-sm btn-outline-secondary">Export</button>
      </div>
      <button id="week" class="btn btn-sm btn-outline-secondary dropdown-toggle">
        <span data-feather="calendar"></span>
          This week
      </button>
    </div>
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


    <table id="outpatient_payment_table" class="table table-sm table-striped table-hover" style="width:100%">
      <thead>
        <!-- Table Row -->
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

      <tbody>
        @foreach ($payments as $payment)
        <tr style="cursor: pointer;">
          <td id="{{ $payment->or_no_prefix }}" class="edit">{{ $payment->or_date }}</td>
          <td id="{{ $payment->or_no_prefix }}" class="edit">{{ $payment->or_no_prefix }}</td>
          <td id="{{ $payment->or_no_prefix }}" class="edit">{{ $payment->patient_name }}</td>
          <td id="{{ $payment->or_no_prefix }}" class="edit">{{ $payment->discount }}</td>
          <td id="{{ $payment->or_no_prefix }}" class="edit" align="right">{{ $payment->amount_paid }}</td>
          <td id="{{ $payment->or_no_prefix }}" class="edit">{{ $payment->employee_name }}</td>
          <td id="{{ $payment->or_no_prefix }}" class="edit">{{ $payment->status }}</td>
          <td>
            <a href="{{ route('collections.outpatient.print.pdf', [ '' => $payment->or_no_prefix ]) }}" class="btn btn-sm btn-outline-danger"><span data-feather="printer"></span> Print Receipt</a>
            @if(Auth::user()->isAdmin === '1')
              @if($payment->status === 'Cancelled')
                <a href="#" class="btn btn-sm btn-outline-secondary draft" id="{{ $payment->or_no_prefix }}" style="padding: 3px 20px"><span data-feather="folder"></span> Set to Draft</a>
              @else
                <a href="#" class="btn btn-sm btn-outline-info cancel" id="{{ $payment->or_no_prefix }}"><span data-feather="x" ></span> Cancel Payment</a>
              @endif
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>


    <script type="text/javascript">
      $(document).ready(function() {

        var table = $('#example').DataTable({
          "processing": true,
          "ordering": false,
          // "dom": '<f>rt<"bottom"lip><"clear">',
          "ajax": {
              "url": "/collections/outpatient/get_outpatient_payment_data",
              "dataSrc": "data"
          },
          "columns": [
            { "data": "or_date" },
            { "data": "or_no_prefix" },
            { "data": "patient_name" },
            { "data": "discount" },
            { "data": "amount_paid" },
            { "data": "employee_name" },
            { "data": "status" },

            {
              defaultContent: '<button class="btn btn-sm btn-outline-danger print"><span data-feather="printer"></span> Print Receipt</button>'
            }

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









  <!-- <script>
    $(document).ready(function() {
      $('#outpatient_payment_table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ route('collections.outpatient.getdata') }}",
        "columns": [
          {"data": "or_date"},
          {"data": "or_no_prefix"},
          {"data": "patient_name"},
          {"data": "discount"},
          {"data": "amount_paid"},
          {"data": "employee_name"},
          {"data": "status"},
          {"data": "action", orderable:false, searchable: false }
        ]
      });

    });
  </script> -->

  <!-- <script type="text/javascript">
    $(document).on('click', '.print', function(){
      var ids = $(this).attr('id');
      var user_id = $('#user_id').val();

      alert('clicked print receipt button');
      alert(ids);

      $.ajax({
        type: "POST",
        url: "/collections/outpatient/print/pdf",
        data: { id:ids, user_id:user_id},
        dataType: "JSON",
        success: function(data) {
          console.log(data.id);
        }
      });
    });

  </script> -->

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


</main>


@endsection
