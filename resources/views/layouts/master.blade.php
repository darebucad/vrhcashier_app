<!DOCTYPE html>
<html lang="en">
  @include ('layouts.header')
  <body>
    @include ('layouts.navbar')
    <div class="container-fluid">
      <div class="row">
        @include ('layouts.sidebar')
        @yield ('content')
      </div>
    </div>
    @include ('layouts.footer')
  </body>
</html>
