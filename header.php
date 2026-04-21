<style>
/* USER NAVBAR */
.user-navbar{
  background: linear-gradient(135deg, #1f2933, #111827);
  padding: 12px 0;
}

.user-navbar .navbar-brand{
  font-size: 1.4rem;
  letter-spacing: .5px;
}

/* Highlighted Report Button */
.btn-report{
  background: linear-gradient(135deg, #ff9800, #ff5722);
  color: #fff !important;
  border-radius: 30px;
  padding: 8px 18px;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(255,152,0,.4);
  transition: all .3s ease;
}

.btn-report:hover{
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(255,87,34,.6);
}

/* Active Dashboard Link */
.user-navbar .nav-link.active{
  color: #0d6efd !important;
  font-weight: 600;
}

/* Dropdown */
.user-navbar .dropdown-menu{
  border-radius: 12px;
}
</style>
<nav class="navbar navbar-expand-lg navbar-dark user-navbar sticky-top">
  <div class="container">

    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
      <i class="fas fa-city me-2"></i>
      <strong>Ludhiana City</strong>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

      <!-- LEFT LINKS -->
      <ul class="navbar-nav me-auto align-items-center">

        <!-- Highlighted CTA -->
        <li class="nav-item me-2">
          <a class="btn btn-report" href="report.php">
            <i class="fas fa-plus-circle me-1"></i> Report Issue
          </a>
        </li>

        <!-- Normal link -->
        <li class="nav-item">
          <a class="nav-link active" href="dashboard.php">
            <i class="fas fa-chart-line me-1"></i> Dashboard
          </a>
        </li>

      </ul>

      <!-- USER DROPDOWN -->
      <div class="dropdown">
        <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
           href="#"
           data-bs-toggle="dropdown">

          <img
            src="https://ui-avatars.com/api/?name=<?= urlencode($user['full_name']) ?>&background=0D6EFD&color=fff"
            class="rounded-circle me-2"
            width="34"
            height="34">

          <span class="fw-semibold"><?= htmlspecialchars($user['full_name']) ?></span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li>
            <a class="dropdown-item text-danger" href="logout.php">
              <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
          </li>
        </ul>
      </div>

    </div>
  </div>
</nav>
