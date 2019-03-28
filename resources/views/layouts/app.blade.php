<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ 'IES Cashiering' }}</title>

  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}" defer></script>

  <!-- Bootstrap -->
  <!-- <link rel="stylesheet" href="{{ asset('/css/bootstrap.min.css') }}" type="text/css"> -->

  <!-- Fonts -->
  <!-- <link rel="dns-prefetch" href="https://fonts.gstatic.com"> -->
  <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css"> -->
  <link rel="stylesheet" href="{{ asset('css/font.css') }}" text="text/css">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

   <!--  <div class="navbar navbar-expand-md navbar-light navbar-laravel"; style="background-color:mediumseagreen;">
        <img src="banner.png" style="width: 410px; height: 55px;">
    </div> -->

    <main class="py-4">
        @yield('content')
    </main>

</body>

<!-- Footer -->
<footer class="page-footer font-small blue">
    <div class="footer-copyright text-center py-3">© 2018. Management Information System Department</div>
</footer>

</html>
