<?php
// ====== PAGE CONFIG ======
$pageTitle = "Reports";

// ====== FILTER OPTIONS (replace with DB lookups if needed) ======
$reportTypes = ["All Report Types","Financial","Operational","Customer","Inventory","Training"];
$dateRanges  = ["Last 7 Days","Last 30 Days","Last 90 Days","Year to Date"];
$locations   = ["All Locations","Sydney","Melbourne","Brisbane"];

// Read selected filters
$selectedType = $_GET['type']  ?? $reportTypes[0];
$selectedDate = $_GET['range'] ?? $dateRanges[1];
$selectedLoc  = $_GET['loc']   ?? $locations[0];

// ====== Data + Pagination Setup ======
$perPage = 5;
$page    = max(1, (int)($_GET['page'] ?? 1));

$allReports = [];   // <- Replace with your DB results after Generate
if (isset($_GET['generate'])) {
  // Dummy data (add more than 5 to see pagination)
  $allReports = [
    ["name"=>"Q3 Financial Summary","type"=>"Financial","date_range"=>"Jul 1 – Sep 30, 2023","generated"=>"Oct 2, 2023","size"=>"2.4 MB","icon"=>"bi-file-earmark","url"=>"#"],
    ["name"=>"Customer Satisfaction Analysis","type"=>"Customer","date_range"=>"Last 90 Days","generated"=>"Sep 28, 2023","size"=>"1.8 MB","icon"=>"bi-chat-dots","url"=>"#"],
    ["name"=>"Monthly Revenue Trends","type"=>"Financial","date_range"=>"Year to Date","generated"=>"Sep 15, 2023","size"=>"3.7 MB","icon"=>"bi-graph-up","url"=>"#"],
    ["name"=>"Staff Performance Metrics","type"=>"Operational","date_range"=>"Last 30 Days","generated"=>"Sep 10, 2023","size"=>"1.2 MB","icon"=>"bi-people","url"=>"#"],
    ["name"=>"Inventory Usage Report","type"=>"Inventory","date_range"=>"Last 90 Days","generated"=>"Aug 28, 2023","size"=>"4.1 MB","icon"=>"bi-box-seam","url"=>"#"],
    // extras to demo pagination
    ["name"=>"Training Completion Rates","type"=>"Training","date_range"=>"Last 30 Days","generated"=>"Aug 15, 2023","size"=>"900 KB","icon"=>"bi-mortarboard","url"=>"#"],
    ["name"=>"Expense Breakdown","type"=>"Financial","date_range"=>"Last 30 Days","generated"=>"Aug 02, 2023","size"=>"1.1 MB","icon"=>"bi-cash-stack","url"=>"#"],
    ["name"=>"Incident Log Summary","type"=>"Operational","date_range"=>"Last 90 Days","generated"=>"Jul 25, 2023","size"=>"750 KB","icon"=>"bi-journal-text","url"=>"#"],
  ];
}

// Compute pagination
$totalReports = count($allReports);
$totalPages   = max(1, (int)ceil($totalReports / $perPage));
$page         = min($page, $totalPages);
$offset       = ($page - 1) * $perPage;
$reports      = array_slice($allReports, $offset, $perPage);

// Helper to keep filters in links
function urlWith(array $extra){
  return '?'.http_build_query(array_merge($_GET, $extra));
}
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
    .jk-logo-placeholder{ width:160px; height:40px; background:#f1f3f5; border:1px dashed #adb5bd; border-radius:.5rem; display:flex; align-items:center; justify-content:center; color:#6c757d; font-size:.875rem; }
    .table thead th{ background:#0b2f5e; color:#fff; border-color:#0b2f5e; }
    .report-icon{ font-size:1.1rem; }
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
        <div class="jk-logo-placeholder">Your Logo</div>
      </a>
    </div>
    <nav class="p-2">
      <ul class="nav nav-pills flex-column gap-1">
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="training.php"><i class="bi bi-mortarboard"></i> Training</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="documents.php"><i class="bi bi-folder2-open"></i> Documents</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2 active" href="reports.php"><i class="bi bi-bar-chart-line"></i> Reports</a></li>
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

        <!-- Report Filters -->
        <form class="card border-0 shadow-sm mb-3" method="get">
          <div class="card-body">
            <div class="row g-3 align-items-end">
              <div class="col-12 col-md-4">
                <label class="form-label small text-muted mb-1">Report Type</label>
                <select class="form-select" name="type">
                  <?php foreach($reportTypes as $t): ?>
                    <option <?php echo $t===$selectedType?'selected':''; ?>><?php echo htmlspecialchars($t); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-6 col-md-4">
                <label class="form-label small text-muted mb-1">Date Range</label>
                <select class="form-select" name="range">
                  <?php foreach($dateRanges as $r): ?>
                    <option <?php echo $r===$selectedDate?'selected':''; ?>><?php echo htmlspecialchars($r); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label small text-muted mb-1">Location</label>
                <select class="form-select" name="loc">
                  <?php foreach($locations as $l): ?>
                    <option <?php echo $l===$selectedLoc?'selected':''; ?>><?php echo htmlspecialchars($l); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-12 col-md-1 d-grid">
                <input type="hidden" name="generate" value="1">
                <button class="btn btn-primary"><i class="bi bi-gear me-1"></i> Generate</button>
              </div>
            </div>
          </div>
        </form>

        <!-- Available Reports -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Available Reports</span>
            <div class="input-group input-group-sm" style="max-width:280px;">
              <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control" placeholder="Search reports…">
              <button class="btn btn-light border dropdown-toggle" data-bs-toggle="dropdown">Most Recent</button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Most Recent</a></li>
                <li><a class="dropdown-item" href="#">Oldest</a></li>
                <li><a class="dropdown-item" href="#">Name A–Z</a></li>
              </ul>
            </div>
          </div>

          <?php if (empty($allReports)): ?>
            <div class="card-body text-center text-muted py-5">
              <i class="bi bi-journal-text d-block mb-2" style="font-size:2rem;"></i>
              No reports yet. Choose filters and click <strong>Generate</strong>.
            </div>
          <?php else: ?>
            <div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead>
      <tr>
        <th>Report Name</th>
        <th>Type</th>
        <th>Date Range</th>
        <th>Generated</th>
        <th class="text-end">Size</th>
        <th class="text-end">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($reports as $r): ?>
        <tr>
          <td>
            <i class="report-icon bi <?php echo $r['icon']; ?> me-2"></i>
            <a href="<?php echo htmlspecialchars($r['url']); ?>" class="text-decoration-none">
              <?php echo htmlspecialchars($r['name']); ?>
            </a>
          </td>
          <td><?php echo htmlspecialchars($r['type']); ?></td>
          <td><?php echo htmlspecialchars($r['date_range']); ?></td>
          <td><?php echo htmlspecialchars($r['generated']); ?></td>
          <td class="text-end"><?php echo htmlspecialchars($r['size']); ?></td>
          <td class="text-end">
            <a
              href="<?php echo htmlspecialchars($r['url']); ?>"
              class="btn btn-sm btn-primary"
              download
              target="_blank" rel="noopener"
            >
              <i class="bi bi-download me-1"></i> Download
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>


            <?php
              $start = $totalReports ? (($page-1)*$perPage + 1) : 0;
              $end   = min($totalReports, $page*$perPage);
            ?>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
              <span class="text-muted small">
                Showing <?php echo $start; ?>–<?php echo $end; ?> of <?php echo $totalReports; ?> reports
              </span>
              <nav aria-label="Reports pagination">
                <ul class="pagination pagination-sm mb-0">
                  <li class="page-item <?php echo $page<=1?'disabled':''; ?>">
                    <a class="page-link" href="<?php echo urlWith(['page'=>max(1,$page-1)]); ?>">Previous</a>
                  </li>
                  <?php
                    $window = 2;
                    $from = max(1, $page-$window);
                    $to   = min($totalPages, $page+$window);

                    if ($from > 1) {
                      echo '<li class="page-item"><a class="page-link" href="'.urlWith(['page'=>1]).'">1</a></li>';
                      if ($from > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                    }
                    for ($i=$from; $i<=$to; $i++) {
                      $active = $i==$page ? 'active' : '';
                      echo '<li class="page-item '.$active.'"><a class="page-link" href="'.urlWith(['page'=>$i]).'">'.$i.'</a></li>';
                    }
                    if ($to < $totalPages) {
                      if ($to < $totalPages-1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                      echo '<li class="page-item"><a class="page-link" href="'.urlWith(['page'=>$totalPages]).'">'.$totalPages.'</a></li>';
                    }
                  ?>
                  <li class="page-item <?php echo $page>=$totalPages?'disabled':''; ?>">
                    <a class="page-link" href="<?php echo urlWith(['page'=>min($totalPages,$page+1)]); ?>">Next</a>
                  </li>
                </ul>
              </nav>
            </div>
          <?php endif; ?>
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
