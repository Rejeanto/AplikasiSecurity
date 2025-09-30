<header class="pc-header">
  <div class="header-wrapper">
    <div class="me-auto pc-mob-drp">
      <ul class="list-unstyled">
        <li class="pc-h-item pc-sidebar-collapse">
          <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
        <li class="pc-h-item pc-sidebar-popup">
          <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
      </ul>
    </div>
    <div class="ms-auto">
      <ul class="list-unstyled">
        <li class="dropdown pc-h-item header-user-profile">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#">
            <img src="{{ asset('assets/images/user/avatar-2.jpg') }}" alt="user-image" class="user-avtar" />
            <span>Stebin Ben</span>
          </a>
          <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header">
              <div class="d-flex mb-1">
                <div class="flex-shrink-0">
                  <img src="{{ asset('assets/images/user/avatar-2.jpg') }}" class="user-avtar wid-35" />
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="mb-1">Stebin Ben</h6>
                  <span>Satpam</span>
                </div>
                <a href="#!" class="pc-head-link bg-transparent"><i class="ti ti-power text-danger"></i></a>
              </div>
            </div>
            <ul class="nav drp-tabs nav-fill nav-tabs">
              <li class="nav-item">
                <button class="nav-link active" type="button"><i class="ti ti-user"></i> Profile</button>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade show active">
                <a href="#!" class="dropdown-item"><i class="ti ti-edit-circle"></i> Edit Profile</a>
                <a href="#!" class="dropdown-item"><i class="ti ti-power"></i> Logout</a>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</header>
