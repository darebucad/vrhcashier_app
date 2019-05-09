<header>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top flex-md-nowrap p-0 shadow">

    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Region II Trauma and Medical Center</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">

      <ul class="navbar-nav mr-auto">
      </ul>

      @guest

        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
              <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
          </li>
        </ul>

      @else
      <ul class="navbar-nav mr-lg-3">
        <li class="nav-item dropdown">
          <a class="btn btn-sm btn-outline-info dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span data-feather="user"></span>  {{ Auth::user()->username }}<span class="caret"></span>
          </a>

          <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="margin-left:-80px">

            <!-- Button trigger modal -->
            <button type="button" class="dropdown-item" data-toggle="modal" data-target="#change_password">
              Change Password
            </button>
              <!-- <a href="#" class="dropdown-item" id="change_password">Change Password</a> -->
              <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  {{ __('Logout') }}
              </a>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
              </form>
          </div>

        </li>
      </ul>

      @endguest

    </div>

  </nav>


<!-- Modal -->
<div class="modal fade" id="change_password" tabindex="-1" role="dialog" aria-labelledby="change_password_label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="change_password_label">Change User Account Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group row">
          <input type="password" name="old_password" value="" placeholder="Old Password" id="old_password" class="form-control col-md-10 offset-md-1">
        </div>

        <div class="form-group row">
          <input type="password" name="new_password" value="" placeholder="New Password" id="new_password" class="form-control col-md-10 offset-md-1">
        </div>

        <div class="form-group row">
          <input type="password" name="confirm_new_password" value="" placeholder="Confirm New Password" id="confirm_new_password" class="form-control col-md-10 offset-md-1">
          <input type="hidden" name="user_id_value" value="{{ Auth::user()->id }}" id="user_id_value">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="save_password">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

</header>
