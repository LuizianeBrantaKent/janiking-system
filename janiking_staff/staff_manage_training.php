<?php
declare(strict_types=1);
// --- JaniKing Manage Training (PHP view) with Bootstrap, icon sidebar, logo-only brand, and heading placed below header ---
$logoPath = 'Logo blue letters.png';
$theme = [
  'blue' => '#004990',
  'gray' => '#e9ecef',
  'white' => '#ffffff',
  'text' => '#1f2a37',
  'muted' => '#6b7280',
  'card' => '#ffffff',
  'border' => '#dfe3e7',
];

// Demo data (replace with DB)
$kpis = [
  ['label' => 'Courses Assigned', 'value' => 8],
  ['label' => 'Due This Week', 'value' => 2],
  ['label' => 'Completed', 'value' => 14],
  ['label' => 'Avg. Score', 'value' => '92%'],
];

$courses = [
  [
    'title' => 'Chemical Safety Training',
    'category' => 'Safety Training',
    'due' => 'Oct 20, 2023',
    'status' => 'In Progress',
    'progress' => 55,
    'score' => null,
    'type' => 'Video'
  ],
  [
    'title' => 'Customer Interaction Guidelines',
    'category' => 'Customer Service',
    'due' => 'Oct 22, 2023',
    'status' => 'Not Started',
    'progress' => 0,
    'score' => null,
    'type' => 'Course'
  ],
  [
    'title' => 'Equipment Manual – Floor Scrubber',
    'category' => 'Equipment',
    'due' => 'Oct 12, 2023',
    'status' => 'Completed',
    'progress' => 100,
    'score' => 98,
    'type' => 'PDF'
  ],
  [
    'title' => 'Infection Control Basics',
    'category' => 'Safety Training',
    'due' => 'Oct 08, 2023',
    'status' => 'Overdue',
    'progress' => 15,
    'score' => null,
    'type' => 'Course'
  ],
  [
    'title' => 'New Employee Orientation',
    'category' => 'HR',
    'due' => 'Oct 30, 2023',
    'status' => 'Completed',
    'progress' => 100,
    'score' => 88,
    'type' => 'Course'
  ],
];

$certificates = [
  ['course' => 'Equipment Manual – Floor Scrubber', 'issued' => 'Oct 12, 2023', 'expires' => 'Oct 12, 2025', 'id' => 'CERT-004219'],
  ['course' => 'New Employee Orientation', 'issued' => 'Oct 05, 2023', 'expires' => 'Oct 05, 2025', 'id' => 'CERT-003901'],
];

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function statusBadge(string $s): string {
  $map = [
    'Completed'   => 'badge-complete',
    'In Progress' => 'badge-progress',
    'Not Started' => 'badge-not',
    'Overdue'     => 'badge-overdue',
  ];
  $cls = $map[$s] ?? 'badge-not';
  return '<span class="status-chip '.$cls.'">'.e($s).'</span>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JaniKing – Manage Training</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
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

    /* KPI cards */
    .kpi-card{display:flex; align-items:center; justify-content:space-between; padding:16px}
    .kpi-val{font-size:28px; font-weight:800; color:#111827}
    .kpi-lbl{font-size:13px; color:var(--muted)}

    /* Filters */
    .filters .form-select, .filters .form-control{border-color:var(--border); border-radius:10px}
    .btnx{display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:10px; border:1px solid var(--border); background:#f8fafc; padding:10px 14px; font-weight:700}
    .btnx-primary{background:var(--jk-blue); border-color:var(--jk-blue); color:#fff}

    /* Table */
    .table-wrap{border:1px solid var(--border); border-radius:12px; overflow:hidden; background:#fff}
    table.tablex{width:100%; border-collapse:separate; border-spacing:0}
    .tablex th{background:var(--jk-blue); color:#fff; text-align:left; font-weight:700; padding:12px 14px; font-size:14px}
    .tablex td{padding:12px 14px; border-top:1px solid var(--border); font-size:14px}

    .status-chip{font-weight:700}
    .badge-complete{color:#16a34a}
    .badge-progress{color:#2563eb}
    .badge-not{color:#6b7280}
    .badge-overdue{color:#f97316}

    .progress{height:8px; background:#e5e7eb}
    .progress-bar{background:var(--jk-blue)}

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
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h12a3 3 0 0 1 3 3v5a3 3 0 0 1-3 3H11l-4 4v-4H4a3 3 0 0 1-3-3V8a3 3 0 0 1 3-3z"/></svg>
          <span>Communication</span>
        </a>
        <a href="staff_reports.php">
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
        <a href="staff_manage_training.php" class="active">
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
          <strong>Michael Thompson</strong>
          <small class="d-block" style="color:var(--muted)">Staff</small>
        </div>
        <div style="width:36px;height:36px;border-radius:999px;background:#cbd5e1"></div>
      </div>
    </header>

    <!-- Content -->
    <main class="content">
      <div class="page-head">
        <h1>Manage Training</h1>
        <p>View your assigned courses, track progress, and download certificates</p>
      </div>

      <!-- KPI Row -->
      <div class="row g-3 mb-3">
        <?php foreach ($kpis as $k): ?>
          <div class="col-md-6 col-xl-3">
            <section class="cardx">
              <div class="kpi-card">
                <div class="kpi-lbl"><?= e($k['label']) ?></div>
                <div class="kpi-val"><?= e((string)$k['value']) ?></div>
              </div>
            </section>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Filters -->
      <section class="cardx filters mb-3">
        <div class="card-header">Filters</div>
        <div class="card-body">
          <div class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Search</label>
              <input type="text" class="form-control" placeholder="Search courses…" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Category</label>
              <select class="form-select form-control">
                <option value="">All</option>
                <option>Safety Training</option>
                <option>Customer Service</option>
                <option>Equipment</option>
                <option>HR</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select class="form-select form-control">
                <option value="">Any</option>
                <option>Not Started</option>
                <option>In Progress</option>
                <option>Completed</option>
                <option>Overdue</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Sort</label>
              <select class="form-select form-control">
                <option>Due Soon</option>
                <option>Recently Assigned</option>
                <option>A–Z</option>
              </select>
            </div>
          </div>
        </div>
      </section>

      <!-- Assigned Training Table -->
      <section class="cardx mb-3">
        <div class="card-header">Assigned Training</div>
        <div class="card-body">
          <div class="table-wrap">
            <table class="tablex">
              <thead>
                <tr>
                  <th>Course</th>
                  <th>Category</th>
                  <th>Due</th>
                  <th>Status</th>
                  <th>Progress</th>
                  <th>Score</th>
                  <th style="width:220px">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($courses as $c): ?>
                  <tr>
                    <td><?= e($c['title']) ?></td>
                    <td><?= e($c['category']) ?></td>
                    <td><?= e($c['due']) ?></td>
                    <td><?= statusBadge($c['status']) ?></td>
                    <td style="min-width:170px">
                      <div class="progress" role="progressbar" aria-valuenow="<?= e((string)$c['progress']) ?>" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: <?= e((string)$c['progress']) ?>%"></div>
                      </div>
                      <div class="text-muted" style="font-size:12px"><?= e((string)$c['progress']) ?>% complete</div>
                    </td>
                    <td><?= $c['score']!==null ? e((string)$c['score']).'%' : '—' ?></td>
                    <td>
                      <div class="d-flex flex-wrap gap-2">
                        <?php if ($c['status']==='Completed'): ?>
                          <button class="btnx" type="button">View</button>
                          <button class="btnx" type="button">Download Cert</button>
                        <?php elseif ($c['status']==='Not Started'): ?>
                          <button class="btnx btnx-primary" type="button">Start</button>
                          <button class="btnx" type="button">Details</button>
                        <?php else: ?>
                          <button class="btnx btnx-primary" type="button">Resume</button>
                          <button class="btnx" type="button">Details</button>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Bottom widgets: Current Course + Certificates -->
      <div class="row g-3">
        <div class="col-lg-6">
          <section class="cardx">
            <div class="card-header">Current Course</div>
            <div class="card-body">
              <?php $current = $courses[0]; ?>
              <div class="mb-1" style="font-weight:700; font-size:16px"><?= e($current['title']) ?></div>
              <div class="text-muted" style="font-size:13px">Category: <?= e($current['category']) ?> • Due: <?= e($current['due']) ?></div>
              <div class="mt-2">
                <div class="progress" role="progressbar" aria-valuenow="<?= e((string)$current['progress']) ?>" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar" style="width: <?= e((string)$current['progress']) ?>%"></div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                  <div class="text-muted" style="font-size:12px"><?= e((string)$current['progress']) ?>% complete</div>
                  <div class="text-muted" style="font-size:12px">Status: <?= e($current['status']) ?></div>
                </div>
              </div>
              <div class="mt-3 d-flex gap-2">
                <button class="btnx btnx-primary" type="button">Resume Course</button>
                <button class="btnx" type="button">View Details</button>
              </div>
            </div>
          </section>
        </div>
        <div class="col-lg-6">
          <section class="cardx">
            <div class="card-header">Certificates</div>
            <div class="card-body">
              <div class="table-wrap">
                <table class="tablex">
                  <thead>
                    <tr>
                      <th>Course</th>
                      <th>Issued</th>
                      <th>Expires</th>
                      <th style="width:140px">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($certificates as $cert): ?>
                      <tr>
                        <td><?= e($cert['course']) ?></td>
                        <td><?= e($cert['issued']) ?></td>
                        <td><?= e($cert['expires']) ?></td>
                        <td>
                          <div class="d-flex gap-2">
                            <button class="btnx" type="button">View</button>
                            <button class="btnx" type="button">Download</button>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>

  <!-- Bootstrap 5 Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
