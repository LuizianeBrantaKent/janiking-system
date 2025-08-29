<?php
declare(strict_types=1);
// --- JaniKing Communication Center (PHP view) with Bootstrap, icon sidebar, logo-only brand, and heading placed below header ---
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
$announcements = [
  [ 'title' => 'New Cleaning Protocol Update', 'snippet' => 'Important updates to our cleaning protocols effective next week. Please review the attached documents…', 'time' => '2h ago', 'active' => true ],
  [ 'title' => 'Staff Meeting – July 15th', 'snippet' => 'Reminder about our quarterly staff meeting scheduled for July 15th at 9:00 AM in the main conference room…', 'time' => '1d ago' ],
  [ 'title' => 'New Client Onboarding', 'snippet' => 'We’re excited to announce that Riverdale Hospital has signed on as our newest client. The onboarding process will begin…', 'time' => '3d ago' ],
  [ 'title' => 'Equipment Maintenance Schedule', 'snippet' => 'Please note the updated maintenance schedule for all cleaning equipment. Regular maintenance checks will be performed…', 'time' => '5d ago' ],
  [ 'title' => 'Holiday Schedule Announcement', 'snippet' => 'Please find attached the holiday schedule for the upcoming months. Make sure to submit your time‑off requests…', 'time' => '1w ago' ],
];

$selected = [
  'header' => 'New Cleaning Protocol Update',
  'meta' => [ 'from' => 'Sarah Johnson (Operations Manager)', 'to' => 'All Staff', 'date' => 'July 10, 2023 at 10:30 AM' ],
  'paras' => [
    'Dear Team,',
    "I'm writing to inform you about important updates to our cleaning protocols that will be effective starting next Monday, July 17th.",
    "In response to recent health and safety guidelines, we've revised our standard operating procedures for all client locations. These changes include:",
  ],
  'bullets' => [
    'Enhanced disinfection procedures for high‑touch surfaces',
    'New eco‑friendly cleaning solutions for specific environments',
    'Updated frequency schedules for common areas',
    'Revised personal protective equipment requirements',
  ],
  'closing' => [
    "Please review the attached documents carefully and complete the acknowledgment form by this Friday. We'll be holding training sessions throughout this week to ensure everyone is familiar with the new protocols.",
    'Thank you for your continued dedication to maintaining the highest standards of cleanliness and safety for our clients.',
    'Best regards,',
    'Sarah Johnson',
    'Operations Manager',
  ],
  'attachments' => [
    ['icon' => '📄', 'name' => 'Cleaning_Protocols_2023.pdf', 'size' => '2.4 MB'],
    ['icon' => '📊', 'name' => 'Cleaning_Schedule_Template.xlsx', 'size' => '1.1 MB'],
  ],
];

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JaniKing – Communication Center</title>
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

    /* Tabs */
    .tabs{display:flex; gap:8px; margin-bottom:12px}
    .tab{background:#f3f4f6; border:1px solid var(--border); color:#374151; padding:8px 12px; border-radius:8px; font-weight:700}
    .tab.active{background:var(--jk-blue); color:#fff; border-color:var(--jk-blue)}

    /* Form controls */
    input[type="text"], textarea{width:100%; border:1px solid var(--border); border-radius:10px; padding:10px 12px; background:#fff; font:inherit}
    .form-control{border-color:var(--border); border-radius:10px}
    textarea{min-height:120px; resize:vertical}
    .pill{background:#f1f5f9; border:1px solid var(--border); padding:6px 10px; border-radius:999px; font-size:12px}
    .btnx{display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:10px; border:1px solid var(--border); background:#f8fafc; padding:10px 14px; font-weight:700}
    .btnx-primary{background:var(--jk-blue); border-color:var(--jk-blue); color:#fff}
    .hint{font-size:12px; color:var(--muted)}

    /* Grid in content */
    .grid{display:grid; grid-template-columns: 380px 1fr; gap:18px}

    /* Left list */
    .search{margin-bottom:8px}
    .list{display:flex; flex-direction:column; gap:10px; max-height:540px; overflow:auto}
    .item{border:1px solid var(--border); background:#fff; border-radius:10px; padding:10px 12px; display:grid; grid-template-columns:1fr auto; gap:6px}
    .item h4{margin:0; font-size:14px}
    .item p{margin:0; font-size:12px; color:var(--muted); line-height:1.4}
    .item small{color:#94a3b8}
    .item.active{outline:2px solid rgba(0,73,144,0.2)}

    /* Detail */
    .meta{display:flex; flex-wrap:wrap; gap:18px; color:#475569; font-size:13px; margin-bottom:4px}
    .attachments{display:flex; gap:10px; flex-wrap:wrap; margin-top:8px}
    .chip{display:inline-flex; align-items:center; gap:8px; padding:8px 10px; border-radius:10px; border:1px solid var(--border); background:#f8fafc; font-size:12px}

    /* Page heading below header */
    .page-head{margin:6px 0 14px 0}
    .page-head h1{margin:0; font-size:26px; font-weight:800; color:#1f2937}
    .page-head p{margin:.25rem 0 0; color:var(--muted)}

    @media (max-width: 1100px){
      .app{grid-template-columns: 1fr; grid-template-rows: 64px auto auto}
      .sidebar{grid-row:3}
      .grid{grid-template-columns: 1fr}
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
        <a href="staff_communication.php" class="active">
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
      <!-- Page Title moved below header -->
      <div class="page-head">
        <h1>Communication Center</h1>
        <p>Manage all your messages and announcements in one place</p>
      </div>

      <!-- Compose -->
      <section class="cardx mb-3">
        <div class="card-header">Compose New Announcement</div>
        <div class="card-body">
          <div class="tabs">
            <button class="tab">New Message</button>
            <button class="tab active">Announcements</button>
            <button class="tab">Direct Messages</button>
          </div>
          <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <label class="me-1" style="min-width:36px">To:</label>
            <span class="pill">All Staff</span>
            <span class="pill">Cleaning Team</span>
            <span class="pill">Maintenance</span>
            <span class="pill">+ Add more…</span>
          </div>
          <div class="mb-2">
            <label class="mb-1">Subject:</label>
            <input type="text" class="form-control" placeholder="Enter subject…"/>
          </div>
          <div class="mb-2">
            <label class="mb-1">Message:</label>
            <textarea class="form-control" placeholder="Type your message here…"></textarea>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
              <button class="btnx" type="button">Attach Files</button>
              <span class="hint">Maximum file size: 10MB</span>
            </div>
            <button class="btnx btnx-primary" type="button">Send Announcement</button>
          </div>
        </div>
      </section>

      <div class="grid">
        <!-- Left column: Recent Announcements -->
        <aside class="cardx">
          <div class="card-header">Recent Announcements</div>
          <div class="card-body">
            <div class="search"><input type="text" class="form-control" placeholder="Search announcements…" /></div>
            <div class="list">
              <?php foreach ($announcements as $a): ?>
                <div class="item <?php if(!empty($a['active'])) echo 'active'; ?>">
                  <div>
                    <h4><?= e($a['title']) ?></h4>
                    <p><?= e($a['snippet']) ?></p>
                  </div>
                  <small><?= e($a['time']) ?></small>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </aside>

        <!-- Right column: Message detail -->
        <section class="cardx">
          <div class="card-header"><?= e($selected['header']) ?></div>
          <div class="card-body">
            <div class="meta">
              <div><strong>From:</strong> <?= e($selected['meta']['from']) ?></div>
              <div><strong>To:</strong> <?= e($selected['meta']['to']) ?></div>
              <div><strong>Date:</strong> <?= e($selected['meta']['date']) ?></div>
            </div>
            <?php foreach ($selected['paras'] as $p): ?>
              <p><?= e($p) ?></p>
            <?php endforeach; ?>
            <ul>
              <?php foreach ($selected['bullets'] as $b): ?>
                <li><?= e($b) ?></li>
              <?php endforeach; ?>
            </ul>
            <?php foreach ($selected['closing'] as $c): ?>
              <p><?= e($c) ?></p>
            <?php endforeach; ?>

            <div class="attachments">
              <?php foreach ($selected['attachments'] as $f): ?>
                <span class="chip"><?= e($f['icon']) ?> <?= e($f['name']) ?> • <?= e($f['size']) ?></span>
              <?php endforeach; ?>
            </div>

            <div class="mt-3" style="border-top:1px solid var(--border); padding-top:14px">
              <h4 class="mb-2" style="margin:0">Reply</h4>
              <textarea class="form-control" placeholder="Type your reply here…"></textarea>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <button class="btnx" type="button">Attach</button>
                <button class="btnx btnx-primary" type="button">Send Reply</button>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>

  <!-- Bootstrap 5 Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
