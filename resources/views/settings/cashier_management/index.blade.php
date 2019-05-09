@extends ('layouts.master')

@section ('content')
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h2 class="h5">Settings - Cashier Management</h2>

    @if (session('status'))
      <div class="alert alert-success" role="alert">
        {{ session('status') }}
      </div>
    @endif


  </div>

  <!-- <h4 class="text-primary">Ongoing development ....</h4> -->

  <form id="cashier_form">
    @csrf

    <div class="row">
      <button class="btn btn-sm btn-primary" id="btn_update">Update</button>
      <p id="button-cancel">or <a class="btn-link" href="{{ url('settings/cashier-management') }}">Cancel</a></p>
    </div>

    <br>

    @if (count($cashier) > 0)
      @foreach ($cashier as $c)

      <input type="hidden" name="" value="{{ $c->sucode }}" id="sucode">

      <div class="form-group row">
        <label for="or_number_prefix" class="col-form-label col-md-1">OR Number Prefix </label>

        <div class="col-md-2">
          <input type="text" name="or_number_prefix" value="{{ $c->or_prefix }}" class="form-control" id="or_number_prefix">
        </div>
      </div>

      <div class="form-group row">
        <label for="cashier_officer" class="col-form-label col-md-1">Cashier Head Officer Name</label>

        <div class="col-md-3">
          <input type="text" name="" value="{{ $c->cashier_officer }}" class="form-control" id="cashier_officer">
        </div>
      </div>

      <div class="form-group row">
        <label for="cashier_designation" class="col-form-label col-md-1">Cashier Head Designation</label>

        <div class="col-md-3">
          <input type="text" name="" value="{{ $c->cashier_designation }}" class="form-control" id="cashier_designation">
        </div>
      </div>

      @endforeach

    @endif




  </form>




</main>

<script type="text/javascript">
  $(document).ready(function(){
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    $('#btn_update').on('click', function(e){
      e.preventDefault();
      var _token = CSRF_TOKEN;
      var sucode = $('#sucode').val();
      var or_number_prefix = $('#or_number_prefix').val();
      var cashier_officer = $('#cashier_officer').val();
      var cashier_designation = $('#cashier_designation').val();

      var q = confirm('Are you sure you want to update ?');



      if (q == true) {
        if (or_number_prefix == '' || cashier_officer == '' || cashier_designation == '') {
          console.log('blank value');

        } else {
          console.log('passed');

          saveCashier(_token, sucode, or_number_prefix, cashier_officer, cashier_designation);


        }


      }
    });

    function isBlank(cval){
      if (cval == '') {

        return true;
      }
    }

    function saveCashier(_t, s, onp, co, cd){


      $.ajax({
        type: "POST",
        url: "/settings/cashier-management/save-cashier",
        data: { _token: _t, sucode: s, or_number_prefix: onp, cashier_officer: co, cashier_designation: cd },
        dataType: "JSON",
        success: function(data) {
          console.log(data);

        }

      });

    }


  });

</script>



@endsection
