<?php
// ====== PAGE CONFIG ======
$pageTitle = "Training";

// ====== EXAMPLE DATA (replace with DB queries) ======
$categories = ["All Categories","Cleaning Procedures","Equipment Operation","Customer Service","Safety Training","Management Skills"];
$levels     = ["All Levels","Beginner","Intermediate","Advanced"];

// Example cards: id, title, category, level, desc, trainer, img, file
$trainings = [
  ["id"=>1,"title"=>"Advanced Floor Cleaning Techniques","category"=>"Equipment Operation","level"=>"Intermediate","desc"=>"Learn the latest techniques for cleaning different floor types with our specialized equipment.","trainer"=>"Sarah Johnson","img"=>"https://picsum.photos/seed/floor/640/360","file"=>"#"],
  ["id"=>2,"title"=>"Customer Interaction Best Practices","category"=>"Customer Service","level"=>"Beginner","desc"=>"Guidelines for providing exceptional customer service in various situations.","trainer"=>"Marcus Lee","img"=>"https://picsum.photos/seed/customer/640/360","file"=>"#"],
  ["id"=>3,"title"=>"Workplace Safety Protocols","category"=>"Safety Training","level"=>"All Levels","desc"=>"Maintain safety standards and handle hazardous materials the right way.","trainer"=>"Robert Chen","img"=>"https://picsum.photos/seed/safety/640/360","file"=>"#"],
  ["id"=>4,"title"=>"Team Leadership Fundamentals","category"=>"Management Skills","level"=>"Intermediate","desc"=>"Develop leadership skills to manage cleaning teams and improve productivity.","trainer"=>"Alicia Rodriguez","img"=>"https://picsum.photos/seed/lead/640/360","file"=>"#"],
  ["id"=>5,"title"=>"Chemical Handling & Safety","category"=>"Safety Training","level"=>"Advanced","desc"=>"Proper procedures for handling, storing, and using cleaning chemicals safely.","trainer"=>"David Wilson","img"=>"https://picsum.photos/seed/chem/640/360","file"=>"#"],
  ["id"=>6,"title"=>"Carpet Cleaning Certification","category"=>"Cleaning Procedures","level"=>"Beginner","desc"=>"Complete guide to carpet cleaning methods, stain removal, and maintenance.","trainer"=>"Jennifer Taylor","img"=>"https://picsum.photos/seed/carpet/640/360","file"=>"#"],
];

// Pagination placeholders
$page = 1; $totalPages = 4;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($pageTitle); ?> | JaniKing</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{ --jk-bg:#e9ecef; --jk-primary:#004990; --jk-sidebar:#fff; }
    body{ background:var(--jk-bg); }
    .jk-layout{ min-height:100vh; display:flex; }
    .jk-sidebar{ width:260px; flex:0 0 260px; background:var(--jk-sidebar); border-right:1px solid rgba(0,0,0,.075); position:sticky; top:0; height:100vh; }
    .jk-main{ flex:1; display:flex; flex-direction:column; }
    .jk-topbar{ position:sticky; top:0; z-index:1020; background:#fff; border-bottom:1px solid rgba(0,0,0,.075); }
    .jk-content{ padding:1.25rem; }
    .jk-logo img{ max-width:160px; height:auto; }
    .jk-logo-placeholder{ width:160px; height:40px; background:#f1f3f5; border:1px dashed #adb5bd; border-radius:.5rem; display:flex; align-items:center; justify-content:center; color:#6c757d; font-size:.875rem; }

    .training-card .trainer{
      display:flex; align-items:center; gap:.5rem; font-size:.9rem;
    }
    .trainer .avatar{
      width:28px; height:28px; border-radius:50%; background:#e9ecef; display:inline-flex; align-items:center; justify-content:center;
    }

    @media (max-width: 991.98px){
      .jk-sidebar{ position:fixed; left:-260px; transition:left .25s ease; }
      .jk-sidebar.show{ left:0; box-shadow:0 .5rem 1rem rgba(0,0,0,.15); }
      .jk-backdrop{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.25); z-index:1010; }
      .jk-backdrop.show{ display:block; }
    }
  </style>
</head>
<body>

<div class="jk-layout">
  <!-- Sidebar -->
  <aside id="sidebar" class="jk-sidebar">
    <div class="p-3 border-bottom">
      <a href="#" class="d-flex align-items-center gap-2 text-decoration-none">
        <div class="jk-logo">
          <!-- <img src="/path/to/logo.png" alt="JaniKing"> -->
          <div class="jk-logo-placeholder">Your Logo</div>
        </div>
      </a>
    </div>
    <nav class="p-2">
      <ul class="nav nav-pills flex-column gap-1">
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2 active" href="training.php"><i class="bi bi-mortarboard"></i> Training</a></li>
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
    <!-- Topbar -->
    <header class="jk-topbar">
      <div class="container-fluid py-2 px-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
          <h1 class="h5 m-0"><?php echo htmlspecialchars($pageTitle); ?></h1>
        </div>
        <div class="d-flex align-items-center gap-3">
          <form class="d-none d-md-block" role="search">
            <input class="form-control form-control-sm" type="search" placeholder="Search…" aria-label="Search">
          </form>
          <div class="dropdown">
            <button class="btn btn-light border dropdown-toggle" data-bs-toggle="dropdown">
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
  </header>

    <!-- Content -->
    <main class="jk-content">
      <div class="container-fluid">
        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <div class="row g-2 align-items-center">
              <div class="col-12 col-lg">
                <div class="input-group">
                  <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                  <input type="text" class="form-control" placeholder="Search training materials..." name="q">
                </div>
              </div>
              <div class="col-6 col-lg-3">
                <label class="form-label small text-muted mb-1">Filter by</label>
                <select class="form-select">
                  <?php foreach($categories as $c): ?>
                    <option><?php echo htmlspecialchars($c); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-6 col-lg-2">
                <label class="form-label small text-muted mb-1">Level</label>
                <select class="form-select">
                  <?php foreach($levels as $l): ?>
                    <option><?php echo htmlspecialchars($l); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Cards -->
        <div class="row g-3">
          <?php foreach($trainings as $t): ?>
            <div class="col-12 col-md-6 col-xl-4">
              <div class="card border-0 shadow-sm training-card h-100">
                <img src="<?php echo htmlspecialchars($t['img']); ?>" class="card-img-top" alt="cover">
                <div class="card-body">
                  <div class="small text-muted mb-1"><?php echo htmlspecialchars($t['category']); ?> • <?php echo htmlspecialchars($t['level']); ?></div>
                  <h5 class="card-title mb-1"><?php echo htmlspecialchars($t['title']); ?></h5>
                  <p class="card-text text-muted"><?php echo htmlspecialchars($t['desc']); ?></p>
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="trainer">
                      <span class="avatar"><i class="bi bi-person-fill"></i></span>
                      <span><?php echo htmlspecialchars($t['trainer']); ?></span>
                    </div>
                    <a href="<?php echo htmlspecialchars($t['file']); ?>" class="btn btn-sm btn-primary">
                      <i class="bi bi-download me-1"></i> Download
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div class="text-muted small">Showing <?php echo count($trainings); ?> of 24 documents</div>
          <nav aria-label="Training pagination">
            <ul class="pagination pagination-sm mb-0">
              <li class="page-item <?php echo $page<=1?'disabled':''; ?>"><a class="page-link" href="#">Previous</a></li>
              <?php for($i=1;$i<=$totalPages;$i++): ?>
                <li class="page-item <?php echo $i==$page?'active':''; ?>"><a class="page-link" href="#"><?php echo $i; ?></a></li>
              <?php endfor; ?>
              <li class="page-item <?php echo $page>=$totalPages?'disabled':''; ?>"><a class="page-link" href="#">Next</a></li>
            </ul>
          </nav>
        </div>
      </div>
    </main>
  </div>
</div>

<div id="backdrop" class="jk-backdrop"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Mobile sidebar toggle
  const sidebar = document.getElementById('sidebar');
  const toggle  = document.getElementById('sidebarToggle');
  const backdrop= document.getElementById('backdrop');
  function closeSidebar(){ sidebar.classList.remove('show'); backdrop.classList.remove('show'); }
  function openSidebar(){ sidebar.classList.add('show'); backdrop.classList.add('show'); }
  toggle?.addEventListener('click', () => sidebar.classList.contains('show') ? closeSidebar() : openSidebar());
  backdrop?.addEventListener('click', closeSidebar);
</script>
</body>
</html>
