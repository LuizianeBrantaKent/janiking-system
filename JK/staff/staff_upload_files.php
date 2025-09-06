<?php
declare(strict_types=1);

// Show errors while stabilizing
ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__.'/../app/auth.php';
require_once __DIR__.'/../app/helpers.php';
require_once __DIR__.'/../app/UploadsRepository.php';

require_login();
$user   = current_user();
$userId = (int)($user['user_id'] ?? 0);

// --- Page look & feel ---
$logoPath   = 'Logo blue letters.png';
$userName   = $user['name'] ?? 'User';
$userRole   = $user['role'] ?? 'Staff';
$avatarPath = $user['avatar'] ?? 'default-avatar.png';

$theme = [
  'blue'  => '#004990',
  'gray'  => '#e9ecef',
  'white' => '#ffffff',
  'text'  => '#1f2a37',
  'muted' => '#6b7280',
  'card'  => '#ffffff',
  'border'=> '#dfe3e7',
];

// Flash
$flash = ['type'=>null,'msg'=>null];

/* ------------------------ Handle POST ------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['upload_training'])) {
            $title    = trim($_POST['t_title'] ?? '');
            $category = trim($_POST['t_category'] ?? '');
            $version  = trim($_POST['t_version'] ?? '1.0');

            if ($title === '' || $category === '') {
                throw new RuntimeException('Please provide Title and Category for the training file.');
            }
            if (!isset($_FILES['t_file'])) {
                throw new RuntimeException('No training file was selected.');
            }

            UploadsRepository::uploadTraining($userId, $title, $category, $version, $_FILES['t_file']);
            $flash = ['type'=>'success','msg'=>'Training file uploaded successfully.'];

        } elseif (isset($_POST['upload_document'])) {
            $title    = trim($_POST['d_title'] ?? '');
            $category = trim($_POST['d_category'] ?? '');
            $version  = trim($_POST['d_version'] ?? '1.0');

            if ($title === '' || $category === '') {
                throw new RuntimeException('Please provide Title and Category for the document.');
            }
            if (!isset($_FILES['d_file'])) {
                throw new RuntimeException('No document file was selected.');
            }

            UploadsRepository::uploadDocument($userId, $title, $category, $version, $_FILES['d_file']);
            $flash = ['type'=>'success','msg'=>'Document uploaded successfully.'];
        }
    } catch (Throwable $e) {
        $flash = ['type'=>'danger','msg'=>$e->getMessage()];
    }
}

/* ------------------------ Query data ------------------------ */
$recentUploads = UploadsRepository::recentUploads(20);

// Optional “current upload” visual placeholder
$currentUpload = [
  'filename' => '—',
  'uploaded' => 0,
  'total'    => 0,
  'percent'  => 0,
  'eta'      => '—',
];

// View helper (kept idempotent; helpers.php also defines this).
if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JaniKing – Upload Files</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <style>
    :root{ --jk-blue: <?= e($theme['blue']) ?>; --jk-gray: <?= e($theme['gray']) ?>; --jk-white: <?= e($theme['white']) ?>; --text: <?= e($theme['text']) ?>; --muted: <?= e($theme['muted']) ?>; --card: <?= e($theme['card']) ?>; --border: <?= e($theme['border']) ?>; }
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
    .page-head{margin:6px 0 14px 0}
    .page-head h1{margin:0; font-size:26px; font-weight:800; color:#1f2937}
    .page-head p{margin:.25rem 0 0; color:var(--muted)}
    .btnx{display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:10px; border:1px solid var(--border); background:#f8fafc; padding:10px 14px; font-weight:700}
    .btnx-primary{background:var(--jk-blue); border-color:var(--jk-blue); color:#fff}
    .form-control, .form-select{border-color:var(--border); border-radius:10px}
    .dropzone{border:2px dashed var(--border); border-radius:10px; min-height:170px; padding:14px 16px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; color:#64748b; background:#fff}
    .dropzone.drag{border-color: var(--jk-blue); background: #f0f6fc}
    .dz-icon{font-size:28px; color:var(--jk-blue); margin-bottom:4px}
    .table-wrap{border:1px solid var(--border); border-radius:12px; overflow:hidden; background:#fff}
    table.tablex{width:100%; border-collapse:separate; border-spacing:0}
    .tablex th{background:var(--jk-blue); color:#fff; text-align:left; font-weight:700; padding:12px 14px; font-size:14px}
    .tablex td{padding:12px 14px; border-top:1px solid var(--border); font-size:14px}
    .status-chip{font-weight:700}
    .status-complete{color:#16a34a}
    .status-processing{color:#f59e0b}
    .progress{height:10px; background:#e5e7eb}
    .progress-bar{background:var(--jk-blue)}
    @media (max-width: 1100px){ .app{grid-template-columns: 1fr; grid-template-rows: 64px auto auto} .sidebar{grid-row:3} }
  </style>
</head>
<body>
  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo"><img src="<?= e($logoPath) ?>" alt="JaniKing logo" /></div>
      <nav class="nav">
        <a href="staff_dash.php"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg><span>Dashboard</span></a>
        <a href="staff_communication.php"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h12a3 3 0 0 1 3 3v5a3 3 0 0 1-3 3H11l-4 4v-4H4a3 3 0 0 1 3-3V8a3 3 0 0 1 3-3z"/></svg><span>Communication</span></a>
        <a href="staff_reports.php"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="3" height="9" rx="1"/><rect x="10" y="6" width="3" height="13" rx="1"/><rect x="16" y="3" width="3" height="16" rx="1"/></svg><span>Reports</span></a>
        <a href="staff_manage_documents.php"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2h8l4 4v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/><path d="M14 2v4h4" fill="#fff"/></svg><span>Documents</span></a>
        <a href="staff_upload_files.php" class="active"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2h8l4 4v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/><path d="M14 2v4h4" fill="#fff"/></svg><span>Upload Files</span></a>
        <a href="staff_manage_training.php"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l9 4-9 4-9-4 9-4zm0 6l9 4-9 4-9-4 9-4zm0 8l9 4-9 4-9-4 9-4z"/></svg><span>Training</span></a>
        <a href="staff_profile_setting.php"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/></svg><span>Profile / Settings</span></a>
      </nav>
      <div class="spacer"></div>
      <div class="logout">
        <a href="#" class="d-flex align-items-center" style="gap:10px; padding:10px 12px; color:#334155; text-decoration:none">
          <svg viewBox="0 0 24 24" aria-hidden="true" style="width:18px;height:18px; fill:var(--jk-blue)"><path d="M10 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h5"/><path d="M17 16l4-4-4-4"/><path d="M7 12h14"/></svg>
          <span>Logout</span>
        </a>
      </div>
    </aside>

    <!-- Header -->
    <header class="header">
      <div class="user">
        <div class="text-end me-2">
          <strong><?= e($userName)?></strong>
          <small class="d-block" style="color:var(--muted)"><?= e($userRole) ?></small>
        </div>
        <img src="<?= e($avatarPath) ?>" alt="<?= e($userName) ?>" class="avatar">
      </div>
    </header>

    <!-- Content -->
    <main class="content">
      <div class="page-head">
        <h1>Upload Files</h1>
        <p>Upload training materials and documents for your organization</p>
      </div>

      <?php if ($flash['type']): ?>
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['msg'] ?? '') ?></div>
      <?php endif; ?>

      <div class="d-flex gap-2 mb-3">
        <button class="btnx btnx-primary" type="button">Training Files</button>
        <button class="btnx" type="button">Documents</button>
      </div>

      <div class="row g-3 mb-3">
        <!-- Training upload -->
        <div class="col-lg-6">
          <section class="cardx">
            <div class="card-header d-flex align-items-center gap-2"><span>📁</span><span>Upload Training File</span></div>
            <div class="card-body">
              <form method="post" enctype="multipart/form-data">
                <div class="mb-2">
                  <label class="form-label">Title</label>
                  <input type="text" name="t_title" class="form-control" placeholder="Enter file title" required />
                </div>
                <div class="mb-2">
                  <label class="form-label">Category</label>
                  <select name="t_category" class="form-select form-control" required>
                    <option value="">Select…</option>
                    <option>Safety Training</option>
                    <option>Customer Service</option>
                    <option>Equipment</option>
                  </select>
                </div>
                <div class="mb-2">
                  <label class="form-label">Version</label>
                  <input type="text" name="t_version" class="form-control" value="1.0" />
                </div>

                <div class="dropzone mb-3" id="dz-training">
                  <div class="dz-icon">⬆️</div>
                  <div>Drag and drop files here or</div>
                  <label class="btnx mb-0">
                    Browse Files
                    <input type="file" name="t_file" accept=".mp4,.mov,.avi,.mkv,.pdf,.ppt,.pptx" hidden required>
                  </label>
                  <div class="small text-muted">Supported: MP4, MOV, AVI, MKV, PDF, PPT/PPTX (Max size depends on server)</div>
                </div>

                <button class="btnx btnx-primary" type="submit" name="upload_training" value="1">Upload File</button>
              </form>
            </div>
          </section>
        </div>

        <!-- Document upload -->
        <div class="col-lg-6">
          <section class="cardx">
            <div class="card-header d-flex align-items-center gap-2"><span>📄</span><span>Upload Document</span></div>
            <div class="card-body">
              <form method="post" enctype="multipart/form-data">
                <div class="mb-2">
                  <label class="form-label">Title</label>
                  <input type="text" name="d_title" class="form-control" placeholder="Enter document title" required />
                </div>
                <div class="mb-2">
                  <label class="form-label">Category</label>
                  <select name="d_category" class="form-select form-control" required>
                    <option value="">Select…</option>
                    <option>Policies</option>
                    <option>Manuals</option>
                    <option>Templates</option>
                    <option>Customer Service</option>
                    <option>Schedules</option>
                    <option>Safety Training</option>
                  </select>
                </div>
                <div class="mb-2">
                  <label class="form-label">Version</label>
                  <input type="text" name="d_version" class="form-control" value="1.0" />
                </div>

                <div class="dropzone mb-3" id="dz-doc">
                  <div class="dz-icon">⬆️</div>
                  <div>Drag and drop files here or</div>
                  <label class="btnx mb-0">
                    Browse Files
                    <input type="file" name="d_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" hidden required>
                  </label>
                  <div class="small text-muted">Supported: PDF, DOC/DOCX, XLS/XLSX, PPT/PPTX</div>
                </div>

                <button class="btnx btnx-primary" type="submit" name="upload_document" value="1">Upload Document</button>
              </form>
            </div>
          </section>
        </div>
      </div>

      <!-- Recent uploads table -->
      <section class="cardx mb-3">
        <div class="card-header">Recent Uploads</div>
        <div class="card-body">
          <div class="table-wrap">
            <table class="tablex">
              <thead>
                <tr>
                  <th>File Name</th>
                  <th>Type</th>
                  <th>Category</th>
                  <th>Version</th>
                  <th>Upload Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recentUploads as $u): ?>
                  <tr>
                    <td><?= e($u['name']) ?></td>
                    <td><?= e($u['type']) ?></td>
                    <td><?= e($u['category']) ?></td>
                    <td><?= e($u['version']) ?></td>
                    <td><?= e($u['date']) ?></td>
                    <td>
                      <?php if (strtolower((string)$u['status']) === 'complete'): ?>
                        <span class="status-chip status-complete">Complete</span>
                      <?php else: ?>
                        <span class="status-chip status-processing"><?= e((string)$u['status']) ?></span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if (!$recentUploads): ?>
                  <tr><td colspan="6" class="text-muted">No uploads yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Current upload (placeholder) -->
      <section class="cardx">
        <div class="card-header">Current Upload</div>
        <div class="card-body">
          <div class="mb-2"><strong>Uploading:</strong> <?= e($currentUpload['filename']) ?> (<?= e((string)$currentUpload['uploaded']) ?>MB / <?= e((string)$currentUpload['total']) ?>MB)</div>
          <div class="progress" role="progressbar" aria-valuenow="<?= e((string)$currentUpload['percent']) ?>" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar" style="width: <?= e((string)$currentUpload['percent']) ?>%"></div>
          </div>
          <div class="d-flex justify-content-between mt-2">
            <div class="text-muted" style="font-size:13px"><?= e((string)$currentUpload['percent']) ?>% Complete</div>
            <div class="text-muted" style="font-size:13px">Estimated time: <?= e($currentUpload['eta']) ?></div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script>
    // Tiny visual-only drag highlight
    document.querySelectorAll('.dropzone').forEach(zone=>{
      ['dragenter','dragover'].forEach(ev=>zone.addEventListener(ev, e=>{e.preventDefault(); zone.classList.add('drag');}));
      ['dragleave','drop'].forEach(ev=>zone.addEventListener(ev, e=>{e.preventDefault(); zone.classList.remove('drag');}));
    });
  </script>
</body>
</html>
