<?php
// ====== PAGE CONFIG ======
// testing 
$pageTitle = "Dashboard"; // change on each page
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($pageTitle); ?> | JaniKing</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons (optional) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom styles -->
  <style>
    :root{
      --jk-bg:#e9ecef;            /* requested background */
      --jk-primary:#004990;       /* optional brand blue */
      --jk-sidebar:#ffffff;
    }
    body{ background:var(--jk-bg); }

    /* Layout */
    .jk-layout{ min-height:100vh; display:flex; }
    .jk-sidebar{
      width:260px; flex:0 0 260px; background:var(--jk-sidebar);
      border-right:1px solid rgba(0,0,0,.075); position:sticky; top:0; height:100vh;
    }
    .jk-main{ flex:1; display:flex; flex-direction:column; }
    .jk-topbar{
      position:sticky; top:0; z-index:1020; background:#fff; border-bottom:1px solid rgba(0,0,0,.075);
    }
    .jk-content{ padding:1.25rem; }
    .nav-link.active{ background:rgba(0,0,0,.05); font-weight:600; }

    @media (max-width: 991.98px){
      .jk-sidebar{ position:fixed; left:-260px; transition:left .25s ease; }
      .jk-sidebar.show{ left:0; box-shadow:0 0.5rem 1rem rgba(0,0,0,.15); }
      .jk-backdrop{
        display:none; position:fixed; inset:0; background:rgba(0,0,0,.25); z-index:1010;
      }
      .jk-backdrop.show{ display:block; }
    }

    /* Logo slot */
    .jk-logo img{ max-width:160px; height:auto; }
    .jk-logo-placeholder{
      width:160px; height:40px; background:#f1f3f5; border:1px dashed #adb5bd; border-radius:.5rem;
      display:flex; align-items:center; justify-content:center; color:#6c757d; font-size:.875rem;
    }
  </style>
</head>
<body>

<div class="jk-layout">

  <!-- Sidebar  testing-->
  <aside id="sidebar" class="jk-sidebar">
    <div class="p-3 border-bottom">
      <a href="#" class="d-flex align-items-center gap-2 text-decoration-none">
        <div class="jk-logo">
          <!-- Replace this placeholder with your own image -->
          <!-- <img src="/path/to/your-logo.png" alt="JaniKing"> -->
          <div class="jk-logo-placeholder">Your Logo</div>
        </div>
      </a>
    </div>

    <nav class="p-2">
      <ul class="nav nav-pills flex-column gap-1">
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center gap-2 active" href="dashboard.php">
            <i class="bi bi-grid"></i> <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#"><i class="bi bi-mortarboard"></i> Training</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#"><i class="bi bi-folder2-open"></i> Documents</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#"><i class="bi bi-graph-up"></i> Reports</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#"><i class="bi bi-bag"></i> Buy Products</a></li>
        <li class="nav-item mt-2"><hr></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#"><i class="bi bi-person-gear"></i> Profile / Settings</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2 text-danger" href="#"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main -->
  <div class="jk-main">

    <!-- Top header -->
    <header class="jk-topbar">
      <div class="container-fluid py-2 px-3">
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle" aria-label="Toggle menu">
              <i class="bi bi-list"></i>
            </button>
            <h1 class="h5 m-0"><?php echo htmlspecialchars($pageTitle); ?></h1>
          </div>
          <div class="d-flex align-items-center gap-3">
            <form class="d-none d-md-block" role="search">
              <input class="form-control form-control-sm" type="search" placeholder="Search…" aria-label="Search">
            </form>
            <div class="dropdown">
              <button class="btn btn-light border dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i> Michael Reynolds
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">My Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Page content -->
    <main class="jk-content">
      <!-- ===== Replace the sample blocks below with your real content ===== -->
      <div class="container-fluid">

        <!-- Welcome & Quick Actions -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
              <div>
                <h2 class="h5 mb-1">Welcome back, Michael!</h2>
                <p class="text-muted mb-0">Here's an overview of your franchisee portal.</p>
              </div>
              <small class="text-muted">Last login: Today, 9:15 AM</small>
            </div>
          </div>
        </div>

        <div class="row g-3 mb-3">
          <?php
          // Quick action items (example)
          $actions = [
            ["icon"=>"bi-envelope", "title"=>"New Message", "subtitle"=>"Compose a new message"],
            ["icon"=>"bi-file-earmark-text", "title"=>"View Documents", "subtitle"=>"Access your files"],
            ["icon"=>"bi-mortarboard", "title"=>"Access Training", "subtitle"=>"View courses"],
            ["icon"=>"bi-bar-chart", "title"=>"Generate Report", "subtitle"=>"Create reports"],
            ["icon"=>"bi-bag", "title"=>"View Products", "subtitle"=>"Browse products"],
          ];
          foreach($actions as $a): ?>
            <div class="col-12 col-sm-6 col-lg">
              <a href="#" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                      <div class="fs-3"><i class="bi <?php echo $a['icon']; ?>"></i></div>
                      <div>
                        <div class="fw-semibold"><?php echo $a['title']; ?></div>
                        <div class="text-muted small"><?php echo $a['subtitle']; ?></div>
                      </div>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="row g-3">
          <!-- Mini Stats -->
          <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
              <div class="card-header bg-white">
                <span class="fw-semibold">Mini Stats</span>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                      <div class="small text-muted">Last Purchase</div>
                      <div class="fw-semibold">April 15, 2023</div>
                      <div class="small text-muted">Cleaning Supplies Bundle</div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                      <div class="small text-muted">Upcoming Training</div>
                      <div class="fw-semibold">May 3, 2023</div>
                      <div class="small text-muted">Advanced Cleaning Techniques</div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                      <div class="small text-muted">Unread Messages</div>
                      <div class="fw-semibold">5 messages</div>
                      <div class="small text-muted">Last received: Today</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Activity -->
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white d-flex justify-content-between">
                <span class="fw-semibold">Recent Activity</span>
                <a href="#" class="small">View All</a>
              </div>
              <div class="list-group list-group-flush">
                <div class="list-group-item"><i class="bi bi-info-circle me-2"></i>You uploaded a new document <span class="text-muted small">— Today, 10:23 AM</span></div>
                <div class="list-group-item"><i class="bi bi-bag-check me-2"></i>New order placed: <code>#ORD-7829</code> <span class="text-muted small">— Yesterday, 3:45 PM</span></div>
                <div class="list-group-item"><i class="bi bi-check2-circle me-2"></i>Completed “Customer Service” training <span class="text-muted small">— Apr 28, 2023</span></div>
                <div class="list-group-item"><i class="bi bi-file-earmark-bar-graph me-2"></i>Generated Monthly Performance Report <span class="text-muted small">— Apr 25, 2023</span></div>
              </div>
            </div>
          </div>

          <!-- Notifications -->
          <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white">
                <span class="fw-semibold">Notifications</span>
              </div>
              <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action" href="#"><i class="bi bi-megaphone me-2"></i>New Product Launch <span class="text-muted small">— 1h ago</span></a>
                <a class="list-group-item list-group-item-action" href="#"><i class="bi bi-mortarboard me-2"></i>Training Reminder <span class="text-muted small">— 5h ago</span></a>
                <a class="list-group-item list-group-item-action" href="#"><i class="bi bi-receipt me-2"></i>Invoice Due <span class="text-muted small">— 1d ago</span></a>
                <a class="list-group-item list-group-item-action" href="#"><i class="bi bi-trophy me-2"></i>Achievement Unlocked <span class="text-muted small">— 3d ago</span></a>
              </div>
              <div class="card-body">
                <a href="#" class="small">View all notifications</a>
              </div>
            </div>
          </div>
        </div>

      </div>
      <!-- ===== /sample blocks ===== -->
    </main>
  </div>
</div>

<!-- Mobile backdrop -->
<div id="backdrop" class="jk-backdrop"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Sidebar toggle for mobile
  const sidebar = document.getElementById('sidebar');
  const toggle  = document.getElementById('sidebarToggle');
  const backdrop= document.getElementById('backdrop');

  function closeSidebar(){ sidebar.classList.remove('show'); backdrop.classList.remove('show'); }
  function openSidebar(){ sidebar.classList.add('show'); backdrop.classList.add('show'); }

  toggle?.addEventListener('click', () => {
    if(sidebar.classList.contains('show')) closeSidebar(); else openSidebar();
  });
  backdrop?.addEventListener('click', closeSidebar);
</script>
</body>
</html>
