<nav class="col-md-2 d-none d-md-block bg-light sidebar">
  <div class="sidebar-sticky">
    <ul class="nav flex-column">
      <li class="nav-item">
        <img src="{{ asset('vrhlogo.png') }}" alt="..." class="img-thumbnail" style="width:200px; height:200px; margin-left: 10%;">
      </li>
      <li class="nav-item">
        <h6 class="d-flex justify-content-between align-items-center px-4 mt-4 mb-1 text-muted">
          Dashboard
          <a class="d-flex align-items-center text-muted" href="#">
            <span data-feather="plus-circle"></span>
          </a>
        </h6>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ url('/') }}" style="text-indent: 2em">
          Cashier Transactions <span class="sr-only">(current)</span>
        </a>
      </li>

      <li class="nav-item">
         <h6 class=" d-flex justify-content-between align-items-center px-4 mt-4 mb-1 text-muted">
           <span>Collections</span>
           <a class="d-flex align-items-center text-muted" href="#">
             <span data-feather="plus-circle"></span>
           </a>
         </h6>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ url('collections/outpatient') }}" style="text-indent: 2em">
          Out-Patient Payment
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ url('collections/inpatient') }}" style="text-indent: 2em">
          In-Patient Payment
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ url('collections/walkin') }}" class="nav-link" style="text-indent: 2em">
          Walk-In Payment
        </a>
      </li>


      <li class="nav-item">
        <a class="nav-link" href="{{ url('/collections/other') }}" style="text-indent: 2em">
          Other Collection
        </a>
      </li>

      <li class="nav-item">
        <h6 class=" d-flex justify-content-between align-items-center px-4 mt-4 mb-1 text-muted">
          <span>Reports</span>
          <a class="d-flex align-items-center text-muted" href="#">
            <span data-feather="plus-circle"></span>
          </a>
        </h6>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" style="text-indent: 2em">
          Summary of Collections
        </a>
      </li>

      <li class="nav-item" style=@if(Auth::user()->isAdmin === '0') display:none @endif>
        <h6 class=" d-flex justify-content-between align-items-center px-4 mt-4 mb-1 text-muted">
          <span>Settings</span>
          <a class="d-flex align-items-center text-muted" href="#">
            <span data-feather="plus-circle"></span>
          </a>
        </h6>
      </li>

      <li class="nav-item" style=@if(Auth::user()->isAdmin === '0') display:none @endif>
        <a class="nav-link" href="{{ url('settings/user_account') }}" style="text-indent: 2em">
          User Accounts
        </a>
      </li>

      <li class="nav-item" style=@if(Auth::user()->isAdmin === '0') display:none @endif>
        <a class="nav-link" href="{{ url('settings/cashier-management') }}" style="text-indent: 2em">
          Cashier Management
        </a>
      </li>

    </ul>

    <hr />
  </div>
</nav>
