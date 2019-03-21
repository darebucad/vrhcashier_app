@extends ('layouts.master')

@section ('content')
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h2 class="h5">Index Settings User Account</h2>

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

  <h4 class="text-primary">Ongoing development ....</h4>

  <br>

  <div class="row">
    <button type="button" name="button" class="btn btn-primary btn-sm" id="btn_create" style="padding: 2px 20px;">Create</button>
      </div>
</main>

<script type="text/javascript">
  $(document).ready(function() {
    $('#btn_create').click(function() {
      alert('clicked button create');
      window.location.replace('/settings/user_account/create');
    });
  });
</script>

@endsection
