<nav class="col-md-2 d-none d-md-block bg-light sidebar">
          <div class="sidebar-sticky">


            <ul class="nav flex-column">


              <li class="nav-item">
                <a class="nav-link disabled" href="#">
                  <span data-feather="clipboard"></span>
                  Dashboard <span class="sr-only"></span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="{{ url('/') }}" style="text-indent: 2em">
                  Cashier Transactions <span class="sr-only">(current)</span>
                </a>
              </li>

              <li class="nav-item">
                 <a class="nav-link disabled" href="#">
                  <span data-feather="credit-card"></span>
                 Collection</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="#" style="text-indent: 2em">
                  In-Patient Payment
                </a>
              </li>


              <li class="nav-item">
                <a class="nav-link" href="{{ url('collections/outpatient') }}" style="text-indent: 2em">
                 
                  Out-Patient Payment
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" style="text-indent: 2em">
                  
                  Walk-in Payment
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="archive"></span>
                  Reports
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" style="text-indent: 2em">
                  Summary of Collections
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="settings"></span>
                  Settings
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" style="text-indent: 2em">
                  User Accounts
                </a>
              </li>


            </ul>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>Saved reports</span>
              <a class="d-flex align-items-center text-muted" href="#">
                <span data-feather="plus-circle"></span>
              </a>
            </h6>
            <ul class="nav flex-column mb-2">
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Current month
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Last quarter
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Social engagement
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Year-end sale
                </a>
              </li>
            </ul>
          </div>
        </nav>