@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h5">Create Settings User Account</h1>
      @if (session('status'))
        <div class="alert alert-success" role="alert">
          {{ session('status') }}
        </div>
      @endif
    <!-- <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <button class="btn btn-sm btn-outline-secondary">Share</button>
        <button class="btn btn-sm btn-outline-secondary">Export</button>
      </div>
      <button class="btn btn-sm btn-outline-secondary dropdown-toggle">
        <span data-feather="calendar"></span>
        This week
      </button>
    </div> -->
  </div>

  <form method="POST" action="{{ route('register') }}" aria-label="{{ __('Register') }}">
    @csrf
    <div class="form-group row">
      <button type="submit" class="btn btn-sm btn-primary" style="padding: 1px 20px;">Save</button>
      <p id="button-cancel">or <a class="btn-link" href="{{ url('settings/user_account') }}">Cancel</a></p>
    </div>

    <br>

    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">{{ __('New Account') }}</div>
            <div class="card-body">
              <div class="form-group row">
                <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Name') }}</label>
                <div class="col-md-8">
                  <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }} form-control-sm" name="name" value="{{ old('name') }}" required autofocus>
                    @if ($errors->has('name'))
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                      </span>
                    @endif
                </div>
              </div>

              <div class="form-group row">
                <label for="email" class="col-md-3 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                <div class="col-md-7">
                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }} form-control-sm" name="email" value="{{ old('email') }}" required>
                @if ($errors->has('email'))
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                  </span>
                @endif
                </div>
              </div>

              <div class="form-group row">
                  <label for="username" class="col-md-3 col-form-label text-md-right">{{ __('Username') }}</label>

                  <div class="col-md-7">
                      <input id="username" type="username" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }} form-control-sm" name="username" value="{{ old('username') }}" required>

                      @if ($errors->has('username'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('username') }}</strong>
                          </span>
                      @endif
                  </div>
              </div>

                    <div class="form-group row">
                        <label for="password" class="col-md-3 col-form-label text-md-right">{{ __('Password') }}</label>

                        <div class="col-md-4">
                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }} form-control-sm" name="password" required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password-confirm" class="col-md-3 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                        <div class="col-md-4">
                            <input id="password-confirm" type="password" class="form-control form-control-sm" name="password_confirmation" required>
                        </div>
                    </div>


                    <div class="form-group row">
                      <div class="col-md-1 offset-md-4">
                        <input type="checkbox" name="active" value="" id="active" class="form-check-input">
                        <label for="active" class="col-form-label">Active</label>
                      </div>

                      <div class="col-md-4">
                        <input type="checkbox" name="administrator" value="" id="administrator" class="form-check-input">
                        <label for="administrator" class="col-form-label">Administrator</label>
                      </div>

                    </div>
            </div>



                </form>

        </div>

      </div>
    </div>








</main>

@endsection
