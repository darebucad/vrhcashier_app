@extends ('layouts.master')


@section ('content')


<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Ajax Data Collections Outpatient</h1>
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

  <form>

    {{ csrf_field() }}

    <div class="row">
      <a class="btn btn-primary" href="{{ url('/collections/outpatient/create') }}">Create</a>
    </div>

    <br />
    <br />


  </form>
          

</main>

@endsection