@extends ('layouts.master')

@section ('content')
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h5">In-Patient Payment</h1>
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

  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" id="user_id">
  <input type="hidden" name="is_admin" value="{{ Auth::user()->is_admin }}" id="is_admin">

  <!-- <h4 class="text-primary">Ongoing development ....</h4> -->
  <!-- <br> -->

  <div class="row">
    <a href="{{ route('collections.inpatient.create', ['' => Auth::user()->id]) }}" class="btn btn-primary btn-sm">Create</a>
  </div>
  <br>
  <table id="inpatient" class="table table-sm table-striped table-hover" style="width:100%">
    <thead>
      <tr>
        <!-- Table Header -->
        <th>O.R. Date</th>
        <th>O.R. No.</th>
        <th>SOA No.</th>
        <th>Patient Name</th>
        <th>Discount (%)</th>
        <th style="text-align:right">Amount Paid</th>
        <th>Cashier On-duty</th>
        <th>Status</th>
        <th>Action(s)</th>
      </tr>
    </thead>
  </table>
</main>


<script type="text/javascript">
  $(document).ready(function() {
    var table = $('#inpatient').DataTable({
      "processing": true,
      "ordering": false,
      "lengthMenu": [[50, 500, 1000, -1], [50, 500, 1000, "All"]],
      "pageLength": 50,
      // "dom": '<f>rt<"bottom"lip><"clear">',
      "ajax": {
          "url": "/collections/inpatient/get_inpatient_payment_data",
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
        { "data": "charge_slip_no" },
        { "data": "patient_name" },
        { "data": "discount" },
        { "data": "amount_paid" ,
          // Include thousands separator to the number
          render: $.fn.dataTable.render.number( ',', '.', 2, '' )},

        { "data": "employee_name" },
        { "data": "status" },

        { defaultContent:
          '',
          render: function(data, type, row, meta) {
            if (row.status === 'Cancelled') {
              ret_val = '<button class="btn btn-sm btn-outline-secondary draft" @if(Auth::user()->is_admin<>1) style="display:none;" @endif><span data-feather="x"></span>Set to Draft</button>';

            }

            else if (row.status === 'Draft') {
              ret_val = '<button class="btn btn-sm btn-outline-warning paid" @if(Auth::user()->is_admin<>1) style="display:none;" @endif><span data-feather="x"></span>Mark as Paid</button>';

            } else {
              ret_val = '<button class="btn btn-sm btn-outline-danger print"><span data-feather="printer"></span> Reprint Receipt</button>' +
              ' <button class="btn btn-sm btn-outline-dark cancel" @if(Auth::user()->is_admin<>1) style="display:none;" @endif><span data-feather="x"></span> Cancel</button>';

            }

            return ret_val;
          },

        },

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
      "order": [[ 0, "desc" ]]

    });


    $('#inpatient tbody').on('click', '.print', function () {
      var row = $(this).closest('tr');
      var data = table.row( row ).data().or_no_prefix;
      // console.log(data);
      window.location.href = '/collections/inpatient/print/pdf/' + data;
    });

    $('#inpatient tbody').on('click', '.cancel', function(){
      var row = $(this).closest('tr');
      var or_number = table.row(row).data().or_no_prefix;
      var user_id = $('#user_id').val();
      var q = confirm('Are you sure you want to cancel this payment ?');

      if (q == true) {
        // console.log(or_number);
        window.location.href = '/collections/inpatient/cancel-payment/' + or_number;
      }
    });

    $('#inpatient tbody').on('click', '.draft', function(){
      var row = $(this).closest('tr');
      var or_number = table.row(row).data().or_no_prefix;
      var user_id = $('#user_id').val();

      // console.log(or_number);
      window.location.href = '/collections/inpatient/draft-payment/' + or_number;
    });

    $('#inpatient tbody').on('click', '.paid', function() {
      var row = $(this).closest('tr');
      var or_number = table.row(row).data().or_no_prefix;
      var user_id = $('#user_id').val();
      var q = confirm('Are you sure you want to mark this payment as paid ?');

      if (q == true) {
        // console.log(or_number);
        window.location.href = '/collections/inpatient/mark-paid/' + or_number;
      }
    });


  });
</script>





@endsection
