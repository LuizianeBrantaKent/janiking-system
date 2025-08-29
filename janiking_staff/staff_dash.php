<?php
declare(strict_types=1);
// --- JaniKing Staff Dashboard (PHP view) with Bootstrap, icon sidebar, logo-only brand, and heading placed below header ---
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
  ['label' => 'Open Tasks', 'value' => 7, 'icon' => '🧹'],
  ['label' => 'New Announcements', 'value' => 3, 'icon' => '📣'],
  ['label' => 'Upcoming Shifts', 'value' => 4, 'icon' => '🗓️'],
  ['label' => 'Pending Reports', 'value' => 2, 'icon' => '📄'],
];

$tasks = [
  ['title' => 'Deep clean – Riverdale Hospital (Wing B)', 'due' => 'Today 4:00 PM', 'progress' => 60],
  ['title' => 'Inventory count – Supplies room', 'due' => 'Tomorrow 9:00 AM', 'progress' => 20],
  ['title' => 'QA checklist – Lobby & Restrooms', 'due' => 'Fri 2:00 PM', 'progress' => 80],
];

$activity = [
  ['date' => 'Aug 15, 2023', 'type' => 'Report', 'detail' => 'Q2 Financial Summary uploaded by Sarah Johnson', 'status' => 'Complete'],
  ['date' => 'Aug 12, 2023', 'type' => 'Training', 'detail' => 'Completed module: Chemical Safety', 'status' => 'Passed'],
  ['date' => 'Aug 11, 2023', 'type' => 'Message', 'detail' => 'New announcement: Cleaning Protocol Update', 'status' => 'Unread'],
  ['date' => 'Aug 10, 2023', 'type' => 'Schedule', 'detail' => 'Shift swapped with Robert Chen', 'status' => 'Approved'],
];

$messages = [
  ['title' => 'Staff Meeting – July 15th', 'snippet' => 'Reminder about our quarterly staff meeting…', 'time' => '1d ago'],
  ['title' => 'New Client Onboarding', 'snippet' => 'We’re excited to announce Riverdale Hospital…', 'time' => '3d ago'],
  ['title' => 'Equipment Maintenance Schedule', 'snippet' => 'Please note the updated maintenance schedule…', 'time' => '5d ago'],
];

$shifts = [
  ['when' => 'Thu, 9:00 AM – 5:00 PM', 'loc' => 'Riverdale Hospital', 'role' => 'Cleaning Supervisor'],
  ['when' => 'Fri, 1:00 PM – 9:00 PM', 'loc' => 'Westview Offices', 'role' => 'Team Lead'],
  ['when' => 'Sat, 7:00 AM – 3:00 PM', 'loc' => 'City Hall', 'role' => 'Supervisor'],
];

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JaniKing – Dashboard</title>
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
    .kpi-left{display:flex; align-items:center; gap:10px}
    .kpi-ico{width:36px; height:36px; border-radius:10px; display:grid; place-items:center; background:rgba(0,73,144,.08); font-size:18px}
    .kpi-val{font-size:28px; font-weight:800; color:#111827}
    .kpi-lbl{font-size:13px; color:var(--muted)}

    /* Tables */
    .table-wrap{border:1px solid var(--border); border-radius:12px; overflow:hidden; background:#fff}
    table.tablex{width:100%; border-collapse:separate; border-spacing:0}
    .tablex th{background:var(--jk-blue); color:#fff; text-align:left; font-weight:700; padding:12px 14px; font-size:14px}
    .tablex td{padding:12px 14px; border-top:1px solid var(--border); font-size:14px}

    .status-chip{font-weight:700}
    .status-complete{color:#16a34a}
    .status-unread{color:#2563eb}
    .status-approved{color:#0ea5e9}

    /* Lists */
    .listy{display:flex; flex-direction:column; gap:10px}
    .list-item{display:flex; justify-content:space-between; gap:10px; padding:10px 12px; border:1px solid var(--border); border-radius:10px; background:#fff}
    .list-item .meta{font-size:13px; color:var(--muted)}

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
        <a href="staff_dash.php" class="active">
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
          <strong>Michael Thompson</strong>
          <small class="d-block" style="color:var(--muted)">Staff</small>
        </div>
        <div style="width:36px;height:36px;border-radius:999px;background:#cbd5e1"></div>
      </div>
    </header>

    <!-- Content -->
    <main class="content">
      <div class="page-head">
        <h1>Dashboard</h1>
        <p>Overview of your tasks, messages, reports and schedule</p>
      </div>

      <!-- KPI Row -->
      <div class="row g-3 mb-3">
        <?php foreach ($kpis as $k): ?>
          <div class="col-md-6 col-xl-3">
            <section class="cardx">
              <div class="kpi-card">
                <div class="kpi-left">
                  <div class="kpi-ico"><?= e($k['icon']) ?></div>
                  <div>
                    <div class="kpi-lbl"><?= e($k['label']) ?></div>
                    <div class="kpi-val"><?= e((string)$k['value']) ?></div>
                  </div>
                </div>
              </div>
            </section>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="row g-3">
        <!-- Left column -->
        <div class="col-xl-8">
          <!-- My Tasks -->
          <section class="cardx mb-3">
            <div class="card-header">My Tasks</div>
            <div class="card-body">
              <div class="listy">
                <?php foreach ($tasks as $t): ?>
                  <div class="list-item">
                    <div>
                      <div style="font-weight:700;"><?= e($t['title']) ?></div>
                      <div class="meta">Due: <?= e($t['due']) ?></div>
                    </div>
                    <div style="min-width:220px">
                      <div class="progress" role="progressbar" aria-valuenow="<?= e((string)$t['progress']) ?>" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: <?= e((string)$t['progress']) ?>%"></div>
                      </div>
                      <div class="meta text-end mt-1"><?= e((string)$t['progress']) ?>% complete</div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </section>

          <!-- Recent Activity -->
          <section class="cardx">
            <div class="card-header">Recent Activity</div>
            <div class="card-body">
              <div class="table-wrap">
                <table class="tablex">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Type</th>
                      <th>Details</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($activity as $a): ?>
                      <tr>
                        <td><?= e($a['date']) ?></td>
                        <td><?= e($a['type']) ?></td>
                        <td><?= e($a['detail']) ?></td>
                        <td>
                          <?php if ($a['status']==='Complete'): ?>
                            <span class="status-chip status-complete">Complete</span>
                          <?php elseif ($a['status']==='Unread'): ?>
                            <span class="status-chip status-unread">Unread</span>
                          <?php else: ?>
                            <span class="status-chip status-approved"><?= e($a['status']) ?></span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </section>
        </div>

        <!-- Right column -->
        <div class="col-xl-4">
          <!-- Quick Actions -->
          <section class="cardx mb-3">
            <div class="card-header">Quick Actions</div>
            <div class="card-body d-grid gap-2">
              <button class="btnx btnx-primary" type="button">New Announcement</button>
              <button class="btnx" type="button">Upload Document</button>
              <button class="btnx" type="button">Log Issue</button>
              <button class="btnx" type="button">Request Time Off</button>
            </div>
          </section>

          <!-- Messages Preview -->
          <section class="cardx mb-3">
            <div class="card-header">Messages & Announcements</div>
            <div class="card-body">
              <div class="listy">
                <?php foreach ($messages as $m): ?>
                  <div class="list-item">
                    <div>
                      <div style="font-weight:700;"><?= e($m['title']) ?></div>
                      <div class="meta"><?= e($m['snippet']) ?></div>
                    </div>
                    <div class="meta" style="min-width:80px; text-align:right;"><?= e($m['time']) ?></div>
                  </div>
                <?php endforeach; ?>
                <div class="text-end"><a href="#" style="font-weight:700; color:var(--jk-blue)">View all</a></div>
              </div>
            </div>
          </section>

          <!-- Upcoming Shifts -->
          <section class="cardx">
            <div class="card-header">Upcoming Shifts</div>
            <div class="card-body">
              <div class="listy">
                <?php foreach ($shifts as $s): ?>
                  <div class="list-item">
                    <div>
                      <div style="font-weight:700;"><?= e($s['when']) ?></div>
                      <div class="meta"><?= e($s['loc']) ?> • <?= e($s['role']) ?></div>
                    </div>
                    <button class="btnx" type="button">Details</button>
                  </div>
                <?php endforeach; ?>
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