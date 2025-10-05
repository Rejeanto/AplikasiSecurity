<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="{{ route('index') }}" class="b-brand text-primary">
        <img src="{{ asset('assets/images/logo-pertamina.png') }}" alt="Logo Pertamina" class="img-fluid" style="max-width: 150px" />
      </a>
    </div>
    <div class="navbar-content">
      <ul class="pc-navbar">
        
        {{-- Admin Section --}}
        @if(auth()->user()->isAdmin())
        <li class="pc-item pc-caption">
          <label>Admin Panel</label>
        </li>
        
        <li class="pc-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <a href="{{ route('admin.dashboard') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>

        <li class="pc-item pc-hasmenu {{ request()->routeIs('admin.assessments.*') ? 'active pc-trigger' : '' }}">
          <a href="#!" class="pc-link">
            <span class="pc-micon"><i class="ti ti-clipboard-check"></i></span>
            <span class="pc-mtext">Assessments</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu">
            <li class="pc-item {{ request()->routeIs('admin.assessments.index') ? 'active' : '' }}">
              <a href="{{ route('admin.assessments.index') }}" class="pc-link">Daftar Assessment</a>
            </li>
            <li class="pc-item {{ request()->routeIs('admin.assessments.create') ? 'active' : '' }}">
              <a href="{{ route('admin.assessments.create') }}" class="pc-link">Tambah Assessment</a>
            </li>
          </ul>
        </li>

        <li class="pc-item pc-hasmenu {{ request()->routeIs('admin.questions.*') ? 'active pc-trigger' : '' }}">
          <a href="#!" class="pc-link">
            <span class="pc-micon"><i class="ti ti-help"></i></span>
            <span class="pc-mtext">Bank Soal</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu">
            <li class="pc-item {{ request()->routeIs('admin.questions.index') ? 'active' : '' }}">
              <a href="{{ route('admin.questions.index') }}" class="pc-link">Daftar Pertanyaan</a>
            </li>
            <li class="pc-item {{ request()->routeIs('admin.questions.create') ? 'active' : '' }}">
              <a href="{{ route('admin.questions.create') }}" class="pc-link">Tambah Pertanyaan</a>
            </li>
          </ul>
        </li>

        <li class="pc-item pc-caption">
          <label>Divider</label>
        </li>
        @endif

        {{-- User Section --}}
        <li class="pc-item pc-caption">
          <label>User Menu</label>
        </li>

        <li class="pc-item {{ request()->routeIs('user.assessments.*') ? 'active' : '' }}">
          <a href="{{ route('user.assessments.index') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
            <span class="pc-mtext">Assessments Saya</span>
          </a>
        </li>

        {{-- Logout --}}
        <li class="pc-item pc-caption">
          <label>Other</label>
        </li>

        <li class="pc-item">
          <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="pc-link w-100 text-start border-0 bg-transparent">
              <span class="pc-micon"><i class="ti ti-logout"></i></span>
              <span class="pc-mtext">Logout</span>
            </button>
          </form>
        </li>

      </ul>
    </div>
  </div>
</nav>