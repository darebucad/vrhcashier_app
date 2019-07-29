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

  <style>
    .red{
    color: red !important;

  }
  </style>


  <br />
  <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">

  <table id="example" class="table table-striped table-hover" style="width:100%; cursor: pointer;">
        <thead>
          <tr>
            <!-- Table Header -->
            <th>O.R. Date</th>
            <th>O.R. No.</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Suffix Name</th>
            <th>Middle Name</th>
            <!-- <th>Patient Name</th> -->
            <th>Discount (%)</th>
            <th style="text-align:right">Amount Paid</th>
            <th>Cashier On-duty</th>
            <th>Status</th>
            <!-- <th>Action</th> -->
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
          targets: [7],
          className: 'text-right'
        },
      ],
      "columns": [
        { "data": "created_at" },
        { "data": "preorno" },
        {"data": "patlast"},
        {"data": "patfirst"},
        {"data": "patsuffix"},
        {"data": "patmiddle"},
        // { "data": "patient_name" },
        { "data": "discount_name" },
        { "data": "amount" ,
          // Include thousands separator to the number
          render: $.fn.dataTable.render.number( ',', '.', 2, '' )},
        { "data": "name" },
        { "data": "payment_status" },

        // { defaultContent:
        //   '',
        //   render: function(data, type, row, meta) {
        //     if (row.status === 'Cancelled') {
        //       ret_val = '<button class="btn btn-sm btn-outline-secondary draft" @if(Auth::user()->is_admin<>1) style="display:none;" @endif><span data-feather="x"></span>Set to Draft</button>';
        //
        //     }
        //
        //     else if (row.status === 'Draft') {
        //       ret_val = '<button class="btn btn-sm btn-outline-warning paid" @if(Auth::user()->is_admin<>1) style="display:none;" @endif><span data-feather="x"></span>Mark as Paid</button>';
        //
        //     } else {
        //       ret_val = '<button class="btn btn-sm btn-outline-danger print"><span data-feather="printer"></span> Reprint Receipt</button>' +
        //       ' <button class="btn btn-sm btn-outline-dark cancel" @if(Auth::user()->is_admin<>1) style="display:none;" @endif><span data-feather="x"></span> Cancel</button>';
        //
        //     }
        //
        //     return ret_val;
        //   },
        //
        // },

      ],
    //   "createdRow": function( row, data, dataIndex ) {
    //          if ( data[6] == "Paid" ) {
    //      $(row).addClass('.red');
    //
    //    }
    // },

    "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
      if ( aData[6] == "Paid" )
      {
        $('tr', nRow).css('color', '#FF0000' );
      }

    },
      "order": [[ 0, "desc" ]],
      "deferRender": true,

    });


    $('#example tbody').on('click', '.print', function () {
      var row = $(this).closest('tr');
      var data = table.row( row ).data().or_no_prefix;
      console.log(data);
      window.location.href = '/collections/outpatient/print/pdf/' + data;
    });


    $('#example tbody').on('click', '.cancel', function(){
      var row = $(this).closest('tr');
      var or_number = table.row(row).data().or_no_prefix;
      var user_id = $('#user_id').val();
      var q = confirm('Are you sure you want to cancel this payment ?');

      if (q == true) {
        // console.log(or_number);
        window.location.href = '/collections/outpatient/cancel-payment/' + or_number;
      }
    });

    $('#example tbody').on('click', '.draft', function(){
      var row = $(this).closest('tr');
      var or_number = table.row(row).data().or_no_prefix;
      var user_id = $('#user_id').val();

      // console.log(or_number);
      window.location.href = '/collections/outpatient/draft-payment/' + or_number;
    });

    $('#example tbody').on('click', '.paid', function() {
      var row = $(this).closest('tr');
      var or_number = table.row(row).data().or_no_prefix;
      var user_id = $('#user_id').val();
      var q = confirm('Are you sure you want to mark this payment as paid ?');

      if (q == true) {
        // console.log(or_number);
        window.location.href = '/collections/outpatient/mark-paid/' + or_number;
      }
    });


    $('#example tbody').on('click', 'tr', function () {
      var row = $(this).closest('tr');
      var id = table.row(row).data().preorno;
      // var data = table.row( row ).data().or_no_prefix;

      // alert( 'You clicked on '+ data +'\'s row' );
      window.location.href = '/collections/outpatient/edit/' + id;
    });




  });
</script>

<!-- <script type="text/javascript">
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
</script> -->

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
