@extends ('layouts.master')

@section ('content')
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h2 class="h5">Reports - Summary of Collections</h2>

    @if (session('status'))
      <div class="alert alert-success" role="alert">
        {{ session('status') }}
      </div>
    @endif

  </div>


  <br><br>

  <table id="collection_summary" class="table table-striped table-hover" style="width:100%">
    <thead>
      <tr>
        <th>Date</th>
        <th>O.R. No.</th>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Suffix Name</th>
        <th>Middle Name</th>
        <!-- <th>Patient Name</th> -->
        <th>Amount Paid</th>
        <th>Account Code</th>
        <th>Category</th>
        <th>Cashier-on-duty</th>
        <th>Status</th>
        <th>Type of Collection</th>
      </tr>
    </thead>

  </table>


</main>

<script type="text/javascript">
  $(document).ready(function() {

    // ('created_at', 'prefix_or_number', 'patient_name', 'amount_paid', 'account_code', 'charge_description', 'name', 'payment_status', 'collection_type')->get();


    var table = $('#collection_summary').DataTable({
      "processing": true,
      "ordering": false,
      "lengthMenu": [[50, 500, 2000, -1], [50, 500, 2000, "All"]],
      "pageLength": 50,
      "ajax": {
        "url": "/reports/collection-summary/get-data",
        "dataSrc": "data"
      },
      "columns": [
        { "data": "created_at" },
        {"data": "preorno"},
        { "data": "patlast" },
        {"data": "patfirst"},
        {"data": "patsuffix"},
        {"data": "patmiddle"},
        // { "data": "prefix_or_number" },
        // { "data": "patient_name" },
        { "data": "amount_paid" },
        {"data": "acctcode"},
        {"data": "chrgdesc"},
        // {"data": "account_code"},
        // {"data": "charge_description"},
        {"data": "name"},
        {"data": "payment_status"},
        { defaultContent:
          '',
          render: function(data, type, row, meta) {
            if (row.advance_payment === '1') {
              ret_val = '<td>Inpatient Payment</td>';

            } else if (row.advance_payment === '0') {
              ret_val = '<td>Outpatient Payment</td>';

            } else {
              ret_val = '<td>Outpatient Payment</td>';

            }
            return ret_val;
          },

        },
        // {"data": "advance_payment"}
        // {"data": "collection_type"}
      ]
    });




  });
</script>

@endsection
