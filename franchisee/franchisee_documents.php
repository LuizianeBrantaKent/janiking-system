<?php
// ====== PAGE CONFIG ======
$pageTitle = "Documents";

// ====== EXAMPLE DATA (REPLACE WITH DB) ======
// Each doc: id, title, type, category, uploaded_at, file_url
$docs = [
  ["id"=>1,"title"=>"Employee Handbook 2023","type"=>"pdf","category"=>"HR Policy","uploaded_at"=>"2023-10-15","file_url"=>"#"],
  ["id"=>2,"title"=>"Q3 Cleaning Schedule","type"=>"excel","category"=>"Operations","uploaded_at"=>"2023-09-28","file_url"=>"#"],
  ["id"=>3,"title"=>"Safety Protocols 2023","type"=>"word","category"=>"Safety","uploaded_at"=>"2023-10-05","file_url"=>"#"],
  ["id"=>4,"title"=>"Client Presentation Template","type"=>"ppt","category"=>"Marketing","uploaded_at"=>"2023-10-12","file_url"=>"#"],
  ["id"=>5,"title"=>"Uniform Guidelines","type"=>"image","category"=>"HR Policy","uploaded_at"=>"2023-09-20","file_url"=>"#"],
  ["id"=>6,"title"=>"Equipment Manual","type"=>"pdf","category"=>"Operations","uploaded_at"=>"2023-10-08","file_url"=>"#"],
  ["id"=>7,"title"=>"Warehouse Layout","type"=>"pdf","category"=>"Operations","uploaded_at"=>"2023-08-25","file_url"=>"#"],
  ["id"=>8,"title"=>"Chemical SDS Pack","type"=>"pdf","category"=>"Safety","uploaded_at"=>"2023-07-22","file_url"=>"#"],
  ["id"=>9,"title"=>"Customer Contract Template","type"=>"word","category"=>"Legal","uploaded_at"=>"2023-06-01","file_url"=>"#"],
  ["id"=>10,"title"=>"Staff Roster Example","type"=>"excel","category"=>"Operations","uploaded_at"=>"2023-05-19","file_url"=>"#"],
  ["id"=>11,"title"=>"Holiday Poster","type"=>"image","category"=>"HR","uploaded_at"=>"2023-11-12","file_url"=>"#"],
  ["id"=>12,"title"=>"Onboarding Pack","type"=>"pdf","category"=>"HR","uploaded_at"=>"2023-12-01","file_url"=>"#"],
];

// Pagination (6 per page)
$perPage = 6;
$total    = count($docs);           // replace with COUNT(*) from DB
$totalPages = max(1, (int)ceil($total / $perPage));
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Slice example data (remove when using SQL LIMIT/OFFSET)
$docsPage = array_slice($docs, $offset, $perPage);

// Helper: icon by file type
function docIcon($type){
  $map = [
    "pdf"=>"bi-file-earmark-pdf", "word"=>"bi-file-earmark-word",
    "excel"=>"bi-file-earmark-excel", "ppt"=>"bi-file-earmark-ppt",
    "image"=>"bi-file-earmark-image"
  ];
  return $map[strtolower($type)] ?? "bi-file-earmark";
}
// Helper: label for type
function typeLabel($type){
  return [
    "pdf"=>"PDF Document", "word"=>"Word Document", "excel"=>"Excel Spreadsheet",
    "ppt"=>"PowerPoint Presentation", "image"=>"Image Collection"
  ][strtolower($type)] ?? "Document";
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
    :root{ --jk-bg:#e9ecef; --jk-sidebar:#fff; --jk-primary:#004990; }
    body{ background:var(--jk-bg); }
    .jk-layout{ min-height:100vh; display:flex; }
    .jk-sidebar{ width:260px; flex:0 0 260px; background:var(--jk-sidebar); border-right:1px solid rgba(0,0,0,.075); position:sticky; top:0; height:100vh; }
    .jk-main{ flex:1; display:flex; flex-direction:column; }
    .jk-topbar{ position:sticky; top:0; z-index:1020; background:#fff; border-bottom:1px solid rgba(0,0,0,.075); }
    .jk-content{ padding:1.25rem; }
    .jk-logo-placeholder{ width:160px; height:40px; background:#f1f3f5; border:1px dashed #adb5bd; border-radius:.5rem; display:flex; align-items:center; justify-content:center; color:#6c757d; font-size:.875rem; }

    .doc-card .badge{ font-weight:600; }
    .doc-card .doc-icon{ font-size:1.5rem; }
    .doc-card .doc-title{ font-weight:700; }
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
        <!-- Replace with your logo img -->
        <div class="jk-logo-placeholder">Your Logo</div>
      </a>
    </div>
    <nav class="p-2">
      <ul class="nav nav-pills flex-column gap-1">
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="training.php"><i class="bi bi-mortarboard"></i> Training</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2 active" href="documents.php"><i class="bi bi-folder2-open"></i> Documents</a></li>
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

        <!-- Filter bar -->
        <form class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <div class="row g-2 align-items-center">
              <div class="col-12 col-xl">
                <div class="input-group">
                  <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                  <input type="text" class="form-control" name="q" placeholder="Search documents...">
                </div>
              </div>
              <div class="col-6 col-xl-2">
                <select class="form-select" name="type">
                  <option value="">Document Type</option>
                  <option value="pdf">PDF</option>
                  <option value="word">Word</option>
                  <option value="excel">Excel</option>
                  <option value="ppt">PowerPoint</option>
                  <option value="image">Image</option>
                </select>
              </div>
              <div class="col-6 col-xl-2">
                <select class="form-select" name="date">
                  <option value="">Date Range</option>
                  <option value="7">Last 7 days</option>
                  <option value="30">Last 30 days</option>
                  <option value="90">Last 90 days</option>
                  <option value="365">Last year</option>
                </select>
              </div>
              <div class="col-12 col-xl-auto">
                <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Apply Filters</button>
              </div>
            </div>
          </div>
        </form>

        <!-- Document grid (6 per page) -->
        <div class="row g-3">
          <?php foreach($docsPage as $d): ?>
          <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 doc-card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="d-flex align-items-center gap-2">
                    <i class="doc-icon bi <?php echo docIcon($d['type']); ?>"></i>
                    <div class="doc-title"><?php echo htmlspecialchars($d['title']); ?></div>
                  </div>
                  <?php if(!empty($d['category'])): ?>
                    <span class="badge bg-light text-secondary"><?php echo htmlspecialchars($d['category']); ?></span>
                  <?php endif; ?>
                </div>
                <div class="text-muted small mt-2"><?php echo typeLabel($d['type']); ?></div>
                <div class="text-muted small">Uploaded: <?php echo date("M d, Y", strtotime($d['uploaded_at'])); ?></div>
              </div>
              <div class="card-footer bg-white d-flex justify-content-end">
                <a href="<?php echo htmlspecialchars($d['file_url']); ?>" class="btn btn-sm btn-primary">
                  <i class="bi bi-download me-1"></i> Download
                </a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Footer line: count + pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div class="text-muted small">
            Showing <?php echo count($docsPage); ?> of <?php echo $total; ?> documents
          </div>
          <nav aria-label="Documents pagination">
            <ul class="pagination pagination-sm mb-0">
              <li class="page-item <?php echo $page<=1?'disabled':''; ?>">
                <a class="page-link" href="?page=<?php echo max(1,$page-1); ?>">Previous</a>
              </li>
              <?php for($i=1;$i<=$totalPages;$i++): ?>
                <li class="page-item <?php echo $i==$page?'active':''; ?>">
                  <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo $page>=$totalPages?'disabled':''; ?>">
                <a class="page-link" href="?page=<?php echo min($totalPages,$page+1); ?>">Next</a>
              </li>
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
  toggle?.addEventListener('click', () =>
    sidebar.classList.contains('show') ? closeSidebar() : openSidebar()
  );
  backdrop?.addEventListener('click', closeSidebar);
</script>
</body>
</html>
