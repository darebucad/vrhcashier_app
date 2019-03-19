@extends('layouts.app')

@section('content')
<div class="container">
    <div class="login-container">
       <!--  <div class="avatar">
           <img src="vrhlogo.png"  style="width: 90px; height: 90px"  class="center"><br>
        </div> -->

        <form method="POST" action="{{ route('login') }}" class="form-box">
            @csrf

            <div class="form-group">
              <img src="banner4.png" style="width: 290px; border-left: 0px">
                <center><h5 style="font-size: 17px;"><b>iHOMIS Extension Systems</b></h5></center>
                <center><h5 style="font-size: 20px;"><b>CASHIERING</b></h5></center>
            </div>

            <div class="form-group">
                <label for="username"  style="font-size: 12px; border-bottom: 100px"><b>{{ __('Username') }}</b></label>
                <input id="username" type="username" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" required autofocus>

                @if ($errors->has('username'))
                    <span class="invalid-feedback" role="alert">
                        {{ $errors->first('username') }}
                    </span>
                @endif
            </div>


            <div class="form-group">
                <label for="password"  style="font-size: 12px; padding-bottom: -200px"><b>{{ __('Password') }}<b></label>
                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        {{ $errors->first('password') }}
                    </span>
                @endif
            </div>

            <div>
                <label style=" display: block;  padding-left:0px;  text-indent: 0px; font-size: 10px">
                <input type="checkbox" style="width: 13px;  height: 16px;  padding: 0;  margin:0;  vertical-align: bottom;  position: relative;  top: -1px;  *overflow: hidden; ">Remember me</label>
            </div>

            <div class="form-group">
                <center><button class="btn btn-info btn-block login" type="submit" style="font-size: 10px"><b>Login</b></button></center>
                <a href="{{ route('password.request') }}"; style="font-size: 10px" >{{ __('Forgot Your Password?') }}</a>
                <!-- <a href="{{ route('register') }}"; style="font-size: 10px; text-align: right; padding-bottom: 0px">Register</a> -->
            </div>

    </form>

    </div>

</div>
@endsection
