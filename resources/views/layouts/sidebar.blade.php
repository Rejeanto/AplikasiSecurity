<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="{{ route('index') }}" class="b-brand text-primary">
        <img src="{{ asset('assets/images/logo-pertamina.png') }}" alt="Logo Pertamina" class="img-fluid" style="max-width: 150px" />
      </a>
    </div>
    <div class="navbar-content">
      <ul class="pc-navbar">
        <li class="pc-item">
          <a href="{{ route('admin') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
            <span class="pc-mtext">Admin</span>
          </a>
        </li>
        <li class="pc-item">
          <a href="{{ route('index') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
