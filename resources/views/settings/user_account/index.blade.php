@extends ('layouts.master')

@section ('content')
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h2 class="h5">Settings - User Accounts</h2>

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
    <a href="{{ url('settings/user_account/create') }}" class="btn btn-primary btn-sm">Create</a>
    <!-- <button type="button" name="button" class="btn btn-primary btn-sm" id="btn_create" style="padding: 2px 20px;">Create</button> -->
  </div>

  <br>

  <table id="users" class="table table-sm table-striped table-hover" style="width:100%">
    <thead>
      <tr>
        <th>User ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Area/Office</th>
        <th>Administrator</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>

  </table>


</main>

<script type="text/javascript">
  $(document).ready(function() {

    var table = $('#users').DataTable({
      "processing": true,
      "ordering": false,
      "lengthMenu": [[50, 500, 2000, -1], [50, 500, 2000, "All"]],
      "pageLength": 50,
      "ajax": {
        "url": "/settings/user_acount/get-user-data",
        "dataSrc": "data"
      },
      "columnDefs": [
        {
          targets: [0],
          visible: false,
          searchable: false
        }
      ],
      "columns": [
        { "data": "id" },
        { "data": "username" },
        { "data": "email" },
        { "data": "area" },
        { defaultContent:
          '',
          render: function(data, type, row, meta) {
            if (row.is_admin === '1') {
              ret_val = '<td>Yes</td>';
            } else {
              ret_val = '<td>No</td>';
            }
            return ret_val;
          },
        },

        { defaultContent:
          '',
          render: function(data, type, row, meta) {
            if (row.is_active === '1') {
              ret_val = '<td>Active</td>';
            } else {
              ret_val = '<td>Inactive</td>';
            }
            return ret_val;
          },
        },
        { defaultContent:
          '',
          render: function(data, type, row, meta) {
            if (row.is_active === '1') {
              ret_val = '<button class="btn btn-sm btn-outline-danger edit"><span data-feather="x"></span>Edit</button> ' +
              '<button class="btn btn-sm btn-outline-secondary inactive"><span data-feather="x"></span>Set to Inactive</button>';

            } else {
              ret_val = '<button class="btn btn-sm btn-outline-success active"><span data-feather="x"></span>Mark as Active</button>';

            }

            return ret_val;
          },

        },

      ]
    });

    $('#btn_create').click(function() {
      // alert('clicked button create');
      window.location.replace('/settings/user_account/create');
    });

    $('#users tbody').on('click', '.edit', function () {
      var row = $(this).closest('tr');
      // var data = table.row( row ).data().prefix_or_number;
      // console.log(data);

      // window.location.href = '/collections/other/print/pdf/' + data;
    });

    $('#users tbody').on('click', '.active', function(){
      var row = $(this).closest('tr');
      var or_number = table.row(row).data().id;
      // var user_id = $('#user_id').val();
      var q = confirm('Are you sure you want to mark this user as active ?');

      if (q == true) {
        // console.log(or_number);
        // window.location.href = '/collections/other/cancel-payment/' + or_number;
      }
    });

    $('#users tbody').on('click', '.inactive', function(){
      var row = $(this).closest('tr');
      var or_number = table.row(row).data().id;
      // var user_id = $('#user_id').val();
      var q = confirm('Are you sure you want to set this user as inactive ?');

      if (q == true) {
        // console.log(or_number);

      }
      // window.location.href = '/collections/other/draft-payment/' + or_number;
    });



  });
</script>

@endsection
