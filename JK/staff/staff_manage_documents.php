<?php
declare(strict_types=1);

// (Optional) show errors while stabilizing
ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__.'/../app/auth.php';
require_once __DIR__.'/../app/helpers.php';              // provides e()
require_once __DIR__.'/../app/DocumentsRepository.php';

require_login();
$user = current_user();

// --- Page look & feel ---
$logoPath   = 'Logo blue letters.png';
$userName   = $user['name'] ?? 'User';
$userRole   = $user['role'] ?? 'Staff';
$avatarPath = $user['avatar'] ?? 'default-avatar.png';

$theme = [
  'blue' => '#004990',
  'gray' => '#e9ecef',
  'white'=> '#ffffff',
  'text' => '#1f2a37',
  'muted'=> '#6b7280',
  'card' => '#ffffff',
  'border'=>'#dfe3e7',
];

/** ---- Read filters from querystring ---- */
$q        = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$status   = trim($_GET['status'] ?? '');
$sortOpt  = $_GET['sort'] ?? 'updated_desc';   // updated_desc|alpha|owner
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 10;

/** ---- Fetch documents ---- */
$res = DocumentsRepository::search(
  (int)($user['user_id'] ?? 0),
  $q ?: null,
  $category ?: null,
  $status ?: null,
  $sortOpt,
  $page,
  $perPage
);
$documents = $res['rows'];
$totalDocs = $res['total'];
$lastPage  = (int)ceil(($totalDocs ?: 1) / $perPage);

/** ---- View helpers ---- */
function tagChip(string $t): string { return '<span class="tag">'.e($t).'</span>'; }
function statusBadge(string $s): string {
  $map = ['Published'=>'badge-published','Draft'=>'badge-draft','Archived'=>'badge-archived'];
  $cls = $map[$s] ?? 'badge-draft';
  return '<span class="status-chip '.$cls.'">'.e($s).'</span>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JaniKing – Manage Documents</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <style>
    :root{
      --jk-blue: <?= e($theme['blue']) ?>;
      --jk-gray: <?= e($theme['gray']) ?>;
      --jk-white: <?= e($theme['white']) ?>;
      --text: <?= e($theme['text']) ?>;
      --muted: <?= e($theme['muted']) ?>;
      --card: <?= e($theme['card']) ?>;
      --border: <?= e($theme['border']) ?>;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{margin:0;font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";color:var(--text);background:var(--jk-gray)}
    .app{display:grid; grid-template-columns: 260px 1fr; grid-template-rows: 64px 1fr; height:100%}
    .sidebar{grid-row:1 / span 2; background:var(--jk-white); border-right:1px solid var(--border); display:flex; flex-direction:column}
    .header{grid-column:2; background:var(--jk-white); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:flex-end; padding:0 20px}
    .content{grid-column:2; padding:24px; overflow:auto}

    /* Sidebar */
    .logo{display:flex; align-items:center; gap:10px; padding:18px 16px; border-bottom:1px solid var(--border)}
    .logo img{height:28px}
    .nav{padding:12px 8px; display:flex; flex-direction:column; gap:6px}
    .nav a{display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; color:#334155; text-decoration:none}
    .nav a:hover{background:#f3f4f6}
    .nav a.active{background:rgba(0,73,144,0.12); color:var(--jk-blue); font-weight:700}
    .nav svg{width:18px; height:18px; fill:var(--jk-blue)}
    .spacer{flex:1}
    .sidebar .logout{padding:12px 8px; border-top:1px solid var(--border)}

    /* Cards */
    .cardx{background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:0 1px 0 rgba(17,24,39,.04)}
    .cardx .card-header{padding:14px 16px; border-bottom:1px solid var(--border); font-weight:700}
    .cardx .card-body{padding:16px}

    /* Heading below header */
    .page-head{margin:6px 0 14px 0}
    .page-head h1{margin:0; font-size:26px; font-weight:800; color:#1f2937}
    .page-head p{margin:.25rem 0 0; color:var(--muted)}

    /* Filters */
    .filters .form-select, .filters .form-control{border-color:var(--border); border-radius:10px}
    .btnx{display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:10px; border:1px solid var(--border); background:#f8fafc; padding:10px 14px; font-weight:700; text-decoration:none}
    .btnx-primary{background:var(--jk-blue); border-color:var(--jk-blue); color:#fff}

    /* Table */
    .table-wrap{border:1px solid var(--border); border-radius:12px; overflow:hidden; background:#fff}
    table.tablex{width:100%; border-collapse:separate; border-spacing:0}
    .tablex th{background:var(--jk-blue); color:#fff; text-align:left; font-weight:700; padding:12px 14px; font-size:14px}
    .tablex td{padding:12px 14px; border-top:1px solid var(--border); font-size:14px}
    .name-cell{display:flex; align-items:center; gap:10px}

    .tag{display:inline-flex; align-items:center; gap:6px; padding:4px 8px; border-radius:999px; border:1px solid var(--border); background:#f8fafc; font-size:12px}
    .tags{display:flex; flex-wrap:wrap; gap:6px}

    .status-chip{font-weight:700}
    .badge-published{color:#16a34a}
    .badge-draft{color:#f59e0b}
    .badge-archived{color:#6b7280}

    /* Avatar */
    .avatar{
      width:36px; height:36px; border-radius:50%;
      object-fit:cover; object-position:center;
      box-shadow:0 0 0 2px #fff; border:1px solid var(--border);
    }

    @media (max-width: 1100px){
      .app{grid-template-columns: 1fr; grid-template-rows: 64px auto auto}
      .sidebar{grid-row:3}
    }
  </style>
</head>
<body>
  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        <img src="<?= e($logoPath) ?>" alt="JaniKing logo" />
      </div>
      <nav class="nav">
        <a href="staff_dash.php">
          <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg>
          <span>Dashboard</span>
        </a>
        <a href="staff_communication.php">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h12a3 3 0 0 1 3 3v5a3 3 0 0 1-3 3H11l-4 4v-4H4a3 3 0 0 1 3-3V8a3 3 0 0 1 3-3z"/></svg>
          <span>Communication</span>
        </a>
        <a href="staff_reports.php">
          <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="3" height="9" rx="1"/><rect x="10" y="6" width="3" height="13" rx="1"/><rect x="16" y="3" width="3" height="16" rx="1"/></svg>
          <span>Reports</span>
        </a>
        <a href="staff_manage_documents.php" class="active">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2h8l4 4v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/><path d="M14 2v4h4" fill="#fff"/></svg>
          <span>Documents</span>
        </a>
        <a href="staff_upload_files.php">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2h8l4 4v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/><path d="M14 2v4h4" fill="#fff"/></svg>
          <span>Upload Files</span>
        </a>
        <a href="staff_manage_training.php">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l9 4-9 4-9-4 9-4zm0 6l9 4-9 4-9-4 9-4zm0 8l9 4-9 4-9-4 9-4z"/></svg>
          <span>Training</span>
        </a>
        <a href="staff_profile_setting.php">
          <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/></svg>
          <span>Profile / Settings</span>
        </a>
      </nav>
      <div class="spacer"></div>
      <div class="logout">
        <a href="#" class="d-flex align-items-center" style="gap:10px; padding:10px 12px; color:#334155; text-decoration:none">
          <svg viewBox="0 0 24 24" aria-hidden="true" style="width:18px;height:18px; fill:var(--jk-blue)"><path d="M10 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h5"/><path d="M17 16l4-4-4-4"/><path d="M7 12h14"/></svg>
          <span>Logout</span>
        </a>
      </div>
    </aside>

    <!-- Header (user info only) -->
    <header class="header">
      <div class="user d-flex align-items-center gap-2">
        <div class="text-end me-2">
          <strong><?= e($userName) ?></strong>
          <small class="d-block" style="color:var(--muted)"><?= e($userRole) ?></small>
        </div>
        <img src="<?= e($avatarPath) ?>" alt="<?= e($userName) ?>" class="avatar">
      </div>
    </header>

    <!-- Content -->
    <main class="content">
      <div class="page-head">
        <h1>Manage Documents</h1>
        <p>Find, filter, and manage training materials and company documents</p>
      </div>

      <!-- Filters -->
      <form method="get" class="cardx filters mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Filters</span>
          <div class="d-flex gap-2">
            <a href="staff_manage_documents.php" class="btnx">Reset</a>
            <a href="#upload" class="btnx btnx-primary">New Document</a>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Search</label>
              <input type="text" name="q" value="<?= e($q) ?>" class="form-control" placeholder="Search by name or tag…" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Category</label>
              <select name="category" class="form-select form-control">
                <option value="">All</option>
                <option <?= $category==='Policies'?'selected':'' ?>>Policies</option>
                <option <?= $category==='Manuals'?'selected':'' ?>>Manuals</option>
                <option <?= $category==='Customer Service'?'selected':'' ?>>Customer Service</option>
                <option <?= $category==='Safety Training'?'selected':'' ?>>Safety Training</option>
                <option <?= $category==='Schedules'?'selected':'' ?>>Schedules</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Status</label>
              <select name="status" class="form-select form-control">
                <option value="">Any</option>
                <option <?= $status==='Published'?'selected':'' ?>>Published</option>
                <option <?= $status==='Draft'?'selected':'' ?>>Draft</option>
                <option <?= $status==='Archived'?'selected':'' ?>>Archived</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Sort</label>
              <select name="sort" class="form-select form-control">
                <option value="updated_desc" <?= $sortOpt==='updated_desc'?'selected':'' ?>>Recently Updated</option>
                <option value="alpha"        <?= $sortOpt==='alpha'?'selected':'' ?>>Alphabetical</option>
                <option value="owner"        <?= $sortOpt==='owner'?'selected':'' ?>>Owner</option>
              </select>
            </div>
            <div class="col-md-1 d-flex justify-content-end">
              <button class="btnx" type="submit">Apply</button>
            </div>
          </div>
        </div>
      </form>

      <!-- Bulk actions -->
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="d-flex align-items-center gap-2">
          <input class="form-check-input" type="checkbox" id="selectAll">
          <label for="selectAll" class="form-check-label">Select All</label>
        </div>
        <div class="d-flex gap-2">
          <button class="btnx" type="button">Download</button>
          <button class="btnx" type="button">Archive</button>
          <button class="btnx" type="button">Delete</button>
        </div>
      </div>

      <!-- Documents table -->
      <section class="cardx mb-3">
        <div class="table-wrap">
          <table class="tablex">
            <thead>
              <tr>
                <th style="width:40px"></th>
                <th>Name</th>
                <th>Category</th>
                <th>Version</th>
                <th>Updated</th>
                <th>Owner</th>
                <th>Tags</th>
                <th>Status</th>
                <th style="width:210px">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($documents as $doc): ?>
                <tr>
                  <td><input class="form-check-input row-check" type="checkbox"></td>
                  <td>
                    <div class="name-cell">
                      <svg viewBox="0 0 24 24" aria-hidden="true" style="width:18px;height:18px; fill:var(--jk-blue)">
                        <path d="M6 2h8l4 4v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
                        <path d="M14 2v4h4" fill="#fff"/>
                      </svg>
                      <span><?= e($doc['name']) ?></span>
                    </div>
                  </td>
                  <td><?= e($doc['category']) ?></td>
                  <td><?= e($doc['version']) ?></td>
                  <td><?= e($doc['updated']) ?></td>
                  <td><?= e($doc['owner']) ?></td>
                  <td>
                    <div class="tags">
                      <?php foreach ($doc['tags'] as $t): echo tagChip($t); endforeach; ?>
                    </div>
                  </td>
                  <td><?= statusBadge($doc['status']) ?></td>
                  <td>
                    <div class="d-flex flex-wrap gap-2">
                      <a class="btnx" href="view_document.php?id=<?= (int)$doc['document_id'] ?>">View</a>
                      <a class="btnx" href="download_document.php?id=<?= (int)$doc['document_id'] ?>">Download</a>
                      <a class="btnx" href="edit_document.php?id=<?= (int)$doc['document_id'] ?>">Edit</a>
                      <a class="btnx" href="archive_document.php?id=<?= (int)$doc['document_id'] ?>">Archive</a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$documents): ?>
                <tr><td colspan="9" class="text-muted">No documents found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Pagination -->
      <?php
        $from = $totalDocs ? (($page-1)*$perPage + 1) : 0;
        $to   = min($page*$perPage, $totalDocs);
      ?>
      <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted" style="font-size:13px">
          Showing <?= $from ?>–<?= $to ?> of <?= $totalDocs ?> documents
        </div>
        <ul class="pagination mb-0">
          <li class="page-item <?= $page<=1?'disabled':'' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page'=>max(1,$page-1)])) ?>">Previous</a>
          </li>
          <?php for ($i=1; $i<=max(1,$lastPage); $i++): ?>
            <li class="page-item <?= $i===$page?'active':'' ?>">
              <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page'=>$i])) ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
          <li class="page-item <?= $page>=$lastPage?'disabled':'' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page'=>min($lastPage,$page+1)])) ?>">Next</a>
          </li>
        </ul>
      </div>
    </main>
  </div>

  <!-- Bootstrap 5 Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script>
    // Select all behavior
    const selectAll = document.getElementById('selectAll');
    const checks = document.querySelectorAll('.row-check');
    if(selectAll){
      selectAll.addEventListener('change', e => checks.forEach(c => c.checked = e.target.checked));
    }
  </script>
</body>
</html>
