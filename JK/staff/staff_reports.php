<?php
declare(strict_types=1);

// (Optional) show errors while stabilizing
ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__.'/../app/auth.php';
require_once __DIR__.'/../app/helpers.php';
require_once __DIR__.'/../app/ReportsRepository.php';

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
$rangeA   = trim($_GET['from'] ?? '');   // YYYY-MM-DD
$rangeB   = trim($_GET['to'] ?? '');     // YYYY-MM-DD
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 10;

/** ---- Fetch data ---- */
$search   = ReportsRepository::search($q ?: null, $rangeA ?: null, $rangeB ?: null, $page, $perPage);
$reports  = $search['rows'];
$total    = $search['total'];
$lastPage = (int)ceil(($total ?: 1)/$perPage);

$notifications = ReportsRepository::notifications(6);

/** ---- View helpers ---- */
if (!function_exists('formatBadge')) {
  function formatBadge(string $fmt): string {
    $map = [
      'PDF'   => ['label' => 'PDF',   'icon' => '🧾'],
      'Excel' => ['label' => 'Excel', 'icon' => '📊'],
      'PPT'   => ['label' => 'PPT',   'icon' => '📑'],
      '—'     => ['label' => '—',     'icon' => '📄'],
    ];
    $d = $map[$fmt] ?? ['label' => $fmt, 'icon' => '📄'];
    return '<span class="badgex">'.$d['icon'].' '.e($d['label']).'</span>';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JaniKing – Reports</title>
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

    .logo{display:flex; align-items:center; gap:10px; padding:18px 16px; border-bottom:1px solid var(--border)}
    .logo img{height:28px}
    .nav{padding:12px 8px; display:flex; flex-direction:column; gap:6px}
    .nav a{display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; color:#334155; text-decoration:none}
    .nav a:hover{background:#f3f4f6}
    .nav a.active{background:rgba(0,73,144,0.12); color:var(--jk-blue); font-weight:700}
    .nav svg{width:18px; height:18px; fill:var(--jk-blue)}
    .spacer{flex:1}
    .sidebar .logout{padding:12px 8px; border-top:1px solid var(--border)}

    .user{display:flex; align-items:center; gap:12px}
    .avatar{width:36px; height:36px; border-radius:50%; object-fit:cover; object-position:center; box-shadow:0 0 0 2px #fff; border:1px solid var(--border)}
    .user small{display:block; color:var(--muted)}

    .cardx{background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:0 1px 0 rgba(17,24,39,.04)}
    .cardx .card-header{padding:14px 16px; border-bottom:1px solid var(--border); font-weight:700}
    .cardx .card-body{padding:16px}

    .btnx{display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:10px; border:1px solid var(--border); background:#f8fafc; padding:10px 14px; font-weight:700}
    .btnx-primary{background:var(--jk-blue); border-color:var(--jk-blue); color:#fff}

    .page-head{margin:6px 0 14px 0}
    .page-head h1{margin:0; font-size:26px; font-weight:800; color:#1f2937}
    .page-head p{margin:.25rem 0 0; color:var(--muted)}

    .filters .form-select, .filters .form-control{border-color:var(--border); border-radius:10px}

    .table-wrap{border:1px solid var(--border); border-radius:12px; overflow:hidden; background:#fff}
    table.tablex{width:100%; border-collapse:separate; border-spacing:0}
    .tablex th{background:var(--jk-blue); color:#fff; text-align:left; font-weight:700; padding:12px 14px; font-size:14px}
    .tablex td{padding:12px 14px; border-top:1px solid var(--border); font-size:14px}
    .badgex{display:inline-flex; align-items:center; gap:6px; padding:4px 8px; border-radius:999px; border:1px solid var(--border); background:#f8fafc; font-size:12px}
    .actionsx{display:flex; gap:8px}

    .chart{width:100%; height:180px; background:linear-gradient(to top, #f8fafc, #fff); border:1px solid var(--border); border-radius:10px; display:flex; align-items:end; gap:10px; padding:10px}
    .bar{width:18px; background:var(--jk-blue); border-radius:6px 6px 0 0}
    .bar.alt{background:#9aa6b2}

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
      <div class="logo"><img src="<?= e($logoPath) ?>" alt="JaniKing logo" /></div>
      <nav class="nav">
        <a href="staff_dash.php">
          <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg>
          <span>Dashboard</span>
        </a>
        <a href="staff_communication.php">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h12a3 3 0 0 1 3 3v5a3 3 0 0 1-3 3H11l-4 4v-4H4a3 3 0 0 1 3-3V8a3 3 0 0 1 3-3z"/></svg>
          <span>Communication</span>
        </a>
        <a href="staff_reports.php" class="active">
          <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="3" height="9" rx="1"/><rect x="10" y="6" width="3" height="13" rx="1"/><rect x="16" y="3" width="3" height="16" rx="1"/></svg>
          <span>Reports</span>
        </a>
        <a href="staff_manage_documents.php">
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
        <h1>Reports</h1>
        <p>Generate and access all your business reports in one place</p>
      </div>

      <!-- Filters -->
      <form class="cardx filters mb-3" method="get">
        <div class="card-header">Report Filters</div>
        <div class="card-body">
          <div class="row g-3 align-items-center">
            <div class="col-md-3">
              <label class="form-label">Search</label>
              <input type="text" name="q" class="form-control" value="<?= e($q) ?>" placeholder="Search by title…">
            </div>
            <div class="col-md-4">
              <label class="form-label">Date Range</label>
              <div class="d-flex gap-2">
                <input type="date" name="from" class="form-control" value="<?= e($rangeA) ?>">
                <input type="date" name="to"   class="form-control" value="<?= e($rangeB) ?>">
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Region</label>
              <select class="form-select form-control" disabled>
                <option selected>All Regions</option>
              </select>
              <div class="text-muted" style="font-size:12px">Region filter coming soon</div>
            </div>
            <div class="col-md-2 d-flex align-items-end justify-content-end">
              <button class="btnx btnx-primary" type="submit">Apply</button>
            </div>
          </div>
        </div>
      </form>

      <!-- Recent Reports Table -->
      <section class="cardx mb-3">
        <div class="card-header">Recent Reports</div>
        <div class="card-body">
          <div class="table-wrap">
            <table class="tablex">
              <thead>
                <tr>
                  <th>Report Name</th>
                  <th>Created On</th>
                  <th>Created By</th>
                  <th>Format</th>
                  <th style="width:160px">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reports as $r): ?>
                  <tr>
                    <td><?= e($r['name']) ?></td>
                    <td><?= e($r['created_on']) ?></td>
                    <td><?= e($r['created_by']) ?></td>
                    <td><?= formatBadge($r['format']) ?></td>
                    <td>
                      <div class="actionsx">
                        <a class="btnx" href="view_report.php?id=<?= (int)$r['report_id'] ?>">View</a>
                        <a class="btnx" href="download_report.php?id=<?= (int)$r['report_id'] ?>">Download</a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if (!$reports): ?>
                  <tr><td colspan="5" class="text-muted">No reports found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <?php
            $from = $total ? (($page-1)*$perPage + 1) : 0;
            $to   = min($page*$perPage, $total);
          ?>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted" style="font-size:13px">
              Showing <?= $from ?>–<?= $to ?> of <?= $total ?> reports
            </div>
            <nav>
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
            </nav>
          </div>
        </div>
      </section>

      <!-- Bottom widgets -->
      <div class="row g-3">
        <div class="col-lg-6">
          <section class="cardx">
            <div class="card-header">Report Analytics</div>
            <div class="card-body">
              <div class="chart">
                <div class="bar" style="height:40%"></div>
                <div class="bar alt" style="height:12%"></div>
                <div class="bar" style="height:65%"></div>
                <div class="bar alt" style="height:15%"></div>
                <div class="bar" style="height:80%"></div>
                <div class="bar alt" style="height:18%"></div>
                <div class="bar" style="height:78%"></div>
                <div class="bar alt" style="height:10%"></div>
                <div class="bar" style="height:30%"></div>
                <div class="bar alt" style="height:12%"></div>
                <div class="bar" style="height:25%"></div>
                <div class="bar alt" style="height:8%"></div>
                <div class="bar" style="height:90%"></div>
              </div>
              <div class="text-muted mt-2" style="font-size:13px">Most frequently accessed reports by department</div>
            </div>
          </section>
        </div>
        <div class="col-lg-6">
          <section class="cardx">
            <div class="card-header">Recent Notifications</div>
            <div class="card-body">
              <div class="d-flex flex-column gap-3">
                <?php foreach ($notifications as $n): ?>
                  <div class="d-flex gap-2">
                    <div style="width:10px; height:10px; border-radius:999px; margin-top:6px; background: <?= e($n['color']) ?>"></div>
                    <div>
                      <div><strong><?= e($n['title']) ?></strong></div>
                      <div class="text-muted" style="font-size:13px">Added by <?= e($n['by']) ?> · <?= e($n['when']) ?></div>
                      <a href="#" style="font-weight:600; color:var(--jk-blue)"><?= e($n['cta']) ?></a>
                    </div>
                  </div>
                <?php endforeach; ?>
                <?php if (!$notifications): ?>
                  <div class="text-muted">No recent notifications.</div>
                <?php endif; ?>
                <div class="mt-2"><a href="#" style="font-weight:600; color:var(--jk-blue)">View All Notifications</a></div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
