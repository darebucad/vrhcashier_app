@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h5"><a href="{{ url('/settings/user_account') }}">Settings - User Accounts </a>/ Create</h1>
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

  <!-- <h4 class="text-primary">Ongoing development ....</h4> -->

  <form method="POST" action="{{ route('register') }}" aria-label="{{ __('Register') }}">
    @csrf
    <div class="form-group row">
      <button type="submit" class="btn btn-primary btn-sm">Save</button>
      <p id="button-cancel">or <a class="btn-link" href="{{ url('settings/user_account') }}">Cancel</a></p>
    </div>

    <br>

    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">{{ __('New User Account') }}</div>
            <div class="card-body">

              <div class="form-group row">
                <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Employee Name') }}</label>
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
                <label for="area" class="col-md-3 col-form-label text-md-right">{{ __('Area/Office') }}</label>
                <div class="col-md-7">
                  <input type="text" name="area" value="" id="area" class="form-control{{ $errors->has('area') ? ' is-invalid' : '' }} form-control-sm" value="{{ old('area') }}">
                  @if ($errors->has('area'))
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $errors->first('area') }}</strong>
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
                <!-- <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input{{ $errors->all() ? (old('is_active') ? 'checked' : '') : 'checked' }}" checked> -->
                <label for="is_active" class="col-md-3 col-form-label text-md-right">Active: </label>

                <div class="col-md-2">
                  <select class="form-control form-control-sm" name="is_active" id="is_active">
                    <option value="0"> </option>
                    <option value="1" selected>Yes</option>
                    <option value="0">No</option>
                  </select>
                </div>

                <!-- <input type="checkbox" name="is_admin" value="0" id="is_admin" class="form-check-input{{ $errors->all() ? (old('is_admin') ? 'checked' : '') : 'checked' }}"> -->
                <label for="is_admin" class="col-md-3 col-form-label text-md-right">Administrator: </label>

                <div class="col-md-2">
                  <select class="form-control form-control-sm" name="is_admin" id="is_admin">
                    <option value="0"> </option>
                    <option value="1">Yes</option>
                    <option value="0" selected>No</option>
                  </select>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </form>
</main>

@endsection
