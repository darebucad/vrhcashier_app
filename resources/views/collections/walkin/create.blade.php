@extends ('layouts.master')
@section ('content')
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h5"><a href="#">Walk-In Payment</a> / Create Walk-In Payment</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <input  type="text" name="search_barcode" id="search_barcode" class="form-control form-control-sm"  placeholder="Charge Slip / Barcode"  style="background-color:#99ccff!important;" required autofocus>
      </div>

      <div class="btn-group mr-2">
        <button id="post_data" class="btn btn-outline-info pull-right btn-sm">
          Search
        </button>
      </div>

    </div>
  </div>

</main>
