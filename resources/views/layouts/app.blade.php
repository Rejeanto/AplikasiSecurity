<!DOCTYPE html>
<html lang="en">
@include('layouts.head')

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
  @include('layouts.sidebar')
  @include('layouts.navbar')

  <!-- Main Content -->
  <div class="pc-container">
    <div class="pc-content">
      @yield('content')
    </div>
  </div>

  {{-- Scripts --}}
  <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
  <script src="{{ asset('assets/js/pcoded.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/js/pages/dashboard-default.js') }}"></script>
</body>
</html>
