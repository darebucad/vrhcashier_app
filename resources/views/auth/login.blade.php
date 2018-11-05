@extends('layouts.app')

@section('content')
<div class="container">
    <div class="login-container">
        <div class="avatar">
           <img src="vrhlogo.png"  style="width: 90px; height: 90px"  class="center"><br>
        </div>
         
        <form method="POST" action="{{ route('login') }}" class="form-box">
            @csrf

            <div class="form-group">
                <center><h2 style="font-weight: bold;">Cashiering - Localhost</h2></center>
            </div>

            <div class="form-group">
                <label for="username"  style="font-size: 12px;"><b>{{ __('Username') }}</b></label>
                <input id="username" type="username" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" required autofocus>

                @if ($errors->has('username'))
                    <span class="invalid-feedback" role="alert">
                        {{ $errors->first('username') }}
                    </span>
                @endif
            </div>
                    

            <div class="form-group">
                <label for="password"  style="font-size: 12px; border-bottom: 4px"><b>{{ __('Password') }}<b></label>
                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        {{ $errors->first('password') }}
                    </span>
                @endif
            </div>

            <div>
                <label style=" display: block;  padding-left:0px;  text-indent: 0px; font-size: 12px">
                <input type="checkbox" style="width: 13px;  height: 16px;  padding: 0;  margin:0;  vertical-align: bottom;  position: relative;  top: -1px;  *overflow: hidden; "> Remember me</label>
            </div>
                    
            <div class="form-group">
                <button class="btn btn-info btn-block login" type="submit" style="font-size: 14px"><b>Login</b></button>
                <a href="{{ route('password.request') }}"; style="font-size: 12px" >{{ __('Forgot Your Password?') }}</a>
                <a href="{{ route('register') }}">Register</a>
            </div>
        
        </form>       

    </div>
    
</div>
@endsection
