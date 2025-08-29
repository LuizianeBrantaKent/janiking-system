<?php
declare(strict_types=1);
// --- JaniKing Reports (PHP view) with Bootstrap, icon sidebar, logo-only brand, and heading placed below header ---
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
$reports = [
  ['name' => 'Q2 Financial Summary',         'created_on' => 'Aug 15, 2023', 'created_by' => 'Sarah Johnson',    'format' => 'PDF'],
  ['name' => 'Customer Satisfaction Survey', 'created_on' => 'Aug 10, 2023', 'created_by' => 'Michael Thompson', 'format' => 'Excel'],
  ['name' => 'Northeast Region Performance', 'created_on' => 'Aug  5, 2023', 'created_by' => 'Robert Chen',      'format' => 'PPT'],
  ['name' => 'Staff Performance Analysis',   'created_on' => 'Jul 28, 2023', 'created_by' => 'Jennifer Martinez', 'format' => 'PDF'],
  ['name' => 'Communication Effectiveness Report','created_on'=>'Jul 22, 2023','created_by'=>'David Wilson','format'=>'Excel'],
];

$notifications = [
  ['title' => 'New Q3 Financial Report Available', 'by' => 'Finance Department', 'when' => '2 hours ago', 'cta' => 'View Details', 'color' => '#0ea5e9'],
  ['title' => 'Report Access Request Pending', 'by' => 'Jennifer Martinez', 'when' => '1 day ago', 'cta' => 'Review Request', 'color' => '#f97316'],
  ['title' => 'Staff Performance Report Approved', 'by' => 'Regional Manager', 'when' => '2 days ago', 'cta' => 'View Report', 'color' => '#10b981'],
];

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function formatBadge(string $fmt): string {
  $map = [
    'PDF' => ['label' => 'PDF', 'icon' => '🧾'],
    'Excel' => ['label' => 'Excel', 'icon' => '📊'],
    'PPT' => ['label' => 'PPT', 'icon' => '📑'],
  ];
  $d = $map[$fmt] ?? ['label' => e($fmt), 'icon' => '📄'];
  return '<span class="badgex">'.$d['icon'].' '.e($d['label']).'</span>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JaniKing – Reports</title>
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

    .btnx{display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:10px; border:1px solid var(--border); background:#f8fafc; padding:10px 14px; font-weight:700}
    .btnx-primary{background:var(--jk-blue); border-color:var(--jk-blue); color:#fff}

    /* Heading below header */
    .page-head{margin:6px 0 14px 0}
    .page-head h1{margin:0; font-size:26px; font-weight:800; color:#1f2937}
    .page-head p{margin:.25rem 0 0; color:var(--muted)}

    /* Filters */
    .filters .form-select, .filters .form-control{border-color:var(--border); border-radius:10px}

    /* Table */
    .table-wrap{border:1px solid var(--border); border-radius:12px; overflow:hidden; background:#fff}
    table.tablex{width:100%; border-collapse:separate; border-spacing:0}
    .tablex th{background:var(--jk-blue); color:#fff; text-align:left; font-weight:700; padding:12px 14px; font-size:14px}
    .tablex td{padding:12px 14px; border-top:1px solid var(--border); font-size:14px}
    .badgex{display:inline-flex; align-items:center; gap:6px; padding:4px 8px; border-radius:999px; border:1px solid var(--border); background:#f8fafc; font-size:12px}
    .actionsx{display:flex; gap:8px}

    /* Analytics */
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
      <div class="logo">
        <img src="<?= e($logoPath) ?>" alt="JaniKing logo" />
      </div>
      <nav class="nav">
        <a href="staff_dash.php" >
          <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg>
          <span>Dashboard</span>
        </a>
        <a href="staff_communication.php" >
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h12a3 3 0 0 1 3 3v5a3 3 0 0 1-3 3H11l-4 4v-4H4a3 3 0 0 1-3-3V8a3 3 0 0 1 3-3z"/></svg>
          <span>Communication</span>
        </a>
        <a href="staff_reports.php" class="active">
          <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="3" height="9" rx="1"/><rect x="10" y="6" width="3" height="13" rx="1"/><rect x="16" y="3" width="3" height="16" rx="1"/></svg>
          <span>Reports</span>
        </a>
        <a href="staff_manage_documents.php" >
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2h8l4 4v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/><path d="M14 2v4h4" fill="#fff"/></svg>
          <span>Documents</span>
        </a>
        <a href="staff_upload_files.php" >
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2h8l4 4v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/><path d="M14 2v4h4" fill="#fff"/></svg>
          <span>Upload Files</span>
        </a>
        <a href="staff_manage_training.php" >
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l9 4-9 4-9-4 9-4zm0 6l9 4-9 4-9-4 9-4zm0 8l9 4-9 4-9-4 9-4z"/></svg>
          <span>Training</span>
        </a>
        <a href="staff_profile_setting.php" >
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
        <h1>Reports</h1>
        <p>Generate and access all your business reports in one place</p>
      </div>

      <!-- Filters -->
      <section class="cardx filters mb-3">
        <div class="card-header">Report Filters</div>
        <div class="card-body">
          <div class="row g-3 align-items-center">
            <div class="col-md-3">
              <label class="form-label">Report Type</label>
              <select class="form-select form-control">
                <option selected>Select Report Type</option>
                <option>Financial</option>
                <option>Operations</option>
                <option>Customer Satisfaction</option>
                <option>Performance</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Date Range</label>
              <div class="d-flex gap-2">
                <input type="date" class="form-control" placeholder="From">
                <input type="date" class="form-control" placeholder="To">
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Franchisee/Region (Optional)</label>
              <select class="form-select form-control">
                <option selected>All Regions</option>
                <option>Northeast</option>
                <option>Midwest</option>
                <option>South</option>
                <option>West</option>
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end justify-content-end">
              <button class="btnx btnx-primary" type="button">Generate Report</button>
            </div>
          </div>
        </div>
      </section>

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
                        <button class="btnx" type="button">View</button>
                        <button class="btnx" type="button">Download</button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted" style="font-size:13px">Showing 5 of 24 reports</div>
            <nav>
              <ul class="pagination mb-0">
                <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">4</a></li>
                <li class="page-item"><a class="page-link" href="#">5</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
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
                <div class="mt-2"><a href="#" style="font-weight:600; color:var(--jk-blue)">View All Notifications</a></div>
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
