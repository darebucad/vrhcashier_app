<header>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top flex-md-nowrap p-0 shadow">

    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Veterans Regional Hospital</a>

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
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">

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

</header>




