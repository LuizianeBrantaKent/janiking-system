<?php
declare(strict_types=1);
// --- JaniKing Profile Settings (PHP view) with Bootstrap + icon sidebar and header text moved below ---
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

// Demo profile data (replace with DB values)
$profile = [
  'name' => 'Michael Thompson',
  'email' => 'michael.thompson@janiking.com',
  'phone' => '(555) 123-4567',
  'position' => 'Cleaning Supervisor',
  'location' => 'Chicago, IL',
  'timezone' => 'America/Chicago (CST)',
  'avatar' => 'https://i.pravatar.cc/120?img=12',
  'notifications' => [
    'email' => true,
    'tasks' => true,
    'schedule' => true,
    'training' => false,
    'documents' => true,
  ],
  'two_factor' => false,
  'language' => 'English',
];

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JaniKing – Profile Settings</title>
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
    .user{display:flex; align-items:center; gap:12px}
    .avatar{width:36px; height:36px; border-radius:999px; background:#cbd5e1; overflow:hidden}
    .avatar img{width:100%; height:100%; object-fit:cover}
    .user small{display:block; color:var(--muted)}
    /* Cards */
    .cardx{background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:0 1px 0 rgba(17,24,39,.04)}
    .cardx .card-header{padding:14px 16px; border-bottom:1px solid var(--border); font-weight:700}
    .cardx .card-body{padding:16px}
    /* Grid */
    .grid{display:grid; grid-template-columns: 1fr 420px; gap:18px}
    .stack{display:grid; gap:18px}
    /* Form */
    .field{display:flex; flex-direction:column; gap:6px}
    .field label{font-size:13px; color:#374151}
    .row2{display:grid; grid-template-columns: 1fr 1fr; gap:12px}
    input[type="text"], input[type="email"], input[type="password"], select, textarea{width:100%; border:1px solid var(--border); border-radius:10px; padding:10px 12px; background:#fff; font:inherit}
    .form-control{border-color:var(--border); border-radius:10px;}
    .hint{font-size:12px; color:var(--muted)}
    .btnx{display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:10px; border:1px solid var(--border); background:#f8fafc; padding:10px 14px; font-weight:600; cursor:pointer}
    .btnx-primary{background:var(--jk-blue); border-color:var(--jk-blue); color:#fff}

    /* Switch */
    .switch{position:relative; width:42px; height:24px}
    .switch input{opacity:0; width:0; height:0}
    .slider{position:absolute; cursor:pointer; inset:0; background:#cbd5e1; border-radius:999px; transition:all .2s}
    .slider:before{content:""; position:absolute; height:18px; width:18px; left:3px; top:3px; background:white; border-radius:50%; transition:all .2s}
    .switch input:checked + .slider{background:var(--jk-blue)}
    .switch input:checked + .slider:before{transform:translateX(18px)}

    .listy{display:flex; flex-direction:column; gap:12px}
    .itemline{display:flex; align-items:center; justify-content:space-between; gap:12px; padding:8px 0; border-bottom:1px dashed var(--border)}
    .itemline:last-child{border-bottom:0}

    /* Page heading moved below header */
    .page-head{margin:6px 0 14px 0}
    .page-head h1{margin:0; font-size:26px; font-weight:800; color:#1f2937}
    .page-head p{margin:.25rem 0 0; color:var(--muted)}

    @media (max-width: 1100px){
      .app{grid-template-columns: 1fr; grid-template-rows: 64px auto auto}
      .sidebar{grid-row:3}
      .grid{grid-template-columns:1fr}
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
        <a href="staff_reports.php" >
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
        <a href="staff_manage_training.php">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l9 4-9 4-9-4 9-4zm0 6l9 4-9 4-9-4 9-4zm0 8l9 4-9 4-9-4 9-4z"/></svg>
          <span>Training</span>
        </a>
        <a href="staff_profile_setting.php" class="active">
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

    <!-- Header (now only user block) -->
    <header class="header">
      <div class="user">
        <div class="text-end me-2">
          <strong><?= e($profile['name']) ?></strong>
          <small class="d-block" style="color:var(--muted)">Staff</small>
        </div>
        <div class="avatar"><img src="<?= e($profile['avatar']) ?>" alt="avatar"></div>
      </div>
    </header>

    <!-- Content -->
    <main class="content">
      <!-- TITLE BLOCK MOVED BELOW HEADER -->
      <div class="page-head">
        <h1>Profile Settings</h1>
        <p>View and update your personal information and preferences</p>
      </div>

      <form class="grid" method="post" action="#">
        <!-- Left column: Personal Info -->
        <section class="cardx">
          <div class="card-header">Personal Information</div>
          <div class="card-body">
            <div class="field mb-2">
              <label for="name">Full Name</label>
              <input id="name" name="name" type="text" class="form-control" value="<?= e($profile['name']) ?>"/>
            </div>
            <div class="field mb-2">
              <label for="email">Email Address</label>
              <input id="email" name="email" type="email" class="form-control" value="<?= e($profile['email']) ?>" disabled/>
              <div class="hint">Contact administrator to change email address</div>
            </div>
            <div class="row2 mt-2">
              <div class="field">
                <label for="phone">Phone Number</label>
                <input id="phone" name="phone" type="text" class="form-control" value="<?= e($profile['phone']) ?>"/>
              </div>
              <div class="field">
                <label for="position">Position</label>
                <input id="position" name="position" type="text" class="form-control" value="<?= e($profile['position']) ?>" disabled/>
                <div class="hint">Position is set by your administrator</div>
              </div>
            </div>
            <div class="row2 mt-2">
              <div class="field">
                <label for="location">Location</label>
                <input id="location" name="location" type="text" class="form-control" value="<?= e($profile['location']) ?>"/>
              </div>
              <div class="field">
                <label for="tz">Timezone</label>
                <select id="tz" name="timezone" class="form-select form-control">
                  <option><?= e($profile['timezone']) ?></option>
                  <option>America/New_York (EST)</option>
                  <option>America/Los_Angeles (PST)</option>
                  <option>Australia/Sydney (AEST)</option>
                  <option>UTC</option>
                </select>
              </div>
            </div>
            <div class="mt-3">
              <div class="hint mb-2">Profile Picture</div>
              <div class="d-flex align-items-center gap-3">
                <div class="avatar" style="width:72px;height:72px"><img src="<?= e($profile['avatar']) ?>" alt="avatar"></div>
                <button class="btnx" type="button">Change Photo</button>
                <div class="hint">JPG, GIF or PNG. Max size 2MB.</div>
              </div>
            </div>
          </div>
        </section>

        <!-- Right column: Stack -->
        <div class="stack">
          <section class="cardx">
            <div class="card-header">Change Password</div>
            <div class="card-body">
              <div class="field mb-2">
                <label for="cur">Current Password</label>
                <input id="cur" type="password" name="cur_password" class="form-control" placeholder="••••••••" />
              </div>
              <div class="row2 mt-2">
                <div class="field">
                  <label for="pw1">New Password</label>
                  <input id="pw1" type="password" name="new_password" class="form-control" placeholder="••••••••" />
                </div>
                <div class="field">
                  <label for="pw2">Confirm New Password</label>
                  <input id="pw2" type="password" name="confirm_password" class="form-control" placeholder="••••••••" />
                </div>
              </div>
              <div class="mt-2"><button class="btnx btnx-primary" type="button">Update Password</button></div>
            </div>
          </section>

          <section class="cardx">
            <div class="card-header">Notification Preferences</div>
            <div class="card-body">
              <div class="listy">
                <div class="itemline"><div>
                  <strong>Email Notifications</strong>
                  <div class="hint">Receive notifications via email</div>
                </div>
                <label class="switch"><input type="checkbox" <?= $profile['notifications']['email']?'checked':'' ?>><span class="slider"></span></label></div>

                <div class="itemline"><div>
                  <strong>Task Assignments</strong>
                  <div class="hint">Get notified when you’re assigned a new task</div>
                </div>
                <label class="switch"><input type="checkbox" <?= $profile['notifications']['tasks']?'checked':'' ?>><span class="slider"></span></label></div>

                <div class="itemline"><div>
                  <strong>Schedule Changes</strong>
                  <div class="hint">Get notified about changes to your schedule</div>
                </div>
                <label class="switch"><input type="checkbox" <?= $profile['notifications']['schedule']?'checked':'' ?>><span class="slider"></span></label></div>

                <div class="itemline"><div>
                  <strong>Training Updates</strong>
                  <div class="hint">Get notified about new training materials</div>
                </div>
                <label class="switch"><input type="checkbox" <?= $profile['notifications']['training']?'checked':'' ?>><span class="slider"></span></label></div>

                <div class="itemline"><div>
                  <strong>Document Updates</strong>
                  <div class="hint">Get notified when documents are updated</div>
                </div>
                <label class="switch"><input type="checkbox" <?= $profile['notifications']['documents']?'checked':'' ?>><span class="slider"></span></label></div>

                <div><button class="btnx" type="button">Manage All Notifications</button></div>
              </div>
            </div>
          </section>

          <section class="cardx">
            <div class="card-header">Account Settings</div>
            <div class="card-body">
              <div class="itemline"><div>
                <strong>Two‑Factor Authentication</strong>
                <div class="hint">Add an extra layer of security to your account</div>
              </div>
              <label class="switch"><input type="checkbox" <?= $profile['two_factor']?'checked':'' ?>><span class="slider"></span></label></div>

              <div class="d-flex align-items-end justify-content-between gap-3 mt-2">
                <div class="field flex-fill">
                  <label for="lang">Language</label>
                  <select id="lang" name="language" class="form-select form-control">
                    <option <?= $profile['language']==='English'?'selected':'' ?>>English</option>
                    <option>Spanish</option>
                    <option>French</option>
                    <option>Portuguese</option>
                  </select>
                </div>
                <div><button class="btnx" type="button">Manage Account</button></div>
              </div>
            </div>
          </section>
        </div>
      </form>

      <div class="d-flex gap-2 justify-content-end mt-3">
        <button class="btnx" type="button">Cancel</button>
        <button class="btnx btnx-primary" type="submit" form="">Save Changes</button>
      </div>
    </main>
  </div>

  <!-- Bootstrap 5 Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
