<?php
declare(strict_types=1); // must be first statement

// TEMP: show errors while fixing (remove once stable)
ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

// includes: /staff -> (.. up one) -> /app
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/AnnouncementRepository.php';
require_once __DIR__ . '/../app/db.php';

require_login();
$user = current_user();

/** ---- page constants (logo, theme) ---- */
$logoPath  = 'Logo blue letters.png';
$userName  = $user['name'];
$userRole  = $user['role'];
$avatarPath = $user['avatar'] ?? 'default-avatar.png';

$theme = [
  'blue'   => '#004990',
  'gray'   => '#e9ecef',
  'white'  => '#ffffff',
  'text'   => '#1f2a37',
  'muted'  => '#6b7280',
  'card'   => '#ffffff',
  'border' => '#dfe3e7',
];

$errors = [];
$flash  = null;

/** ---- handle POST actions ---- */
try {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify($_POST['csrf'] ?? null);

    if (isset($_POST['action']) && $_POST['action'] === 'create_announcement') {
      $subject = trim($_POST['subject'] ?? '');
      $body    = trim($_POST['body'] ?? '');

      // recipients: pills mimic these checkboxes
      $sendAll  = isset($_POST['to_all']) && $_POST['to_all'] === '1';
      $groupIds = array_map('intval', $_POST['group_ids'] ?? []); // Cleaning Team, Maintenance (ids)

      if ($subject === '') $errors[] = 'Subject is required.';
      if ($body === '')    $errors[] = 'Message body is required.';
      if (!$sendAll && empty($groupIds)) $errors[] = 'Please select recipients.';

      // handle attachments (up to 3 demo inputs, can be more if you add)
      $attachments = [];
      foreach (['file1','file2','file3'] as $field) {
        if (isset($_FILES[$field]) && is_array($_FILES[$field])) {
          $saved = save_upload($_FILES[$field]); // returns null if no file given
          if ($saved) $attachments[] = $saved;
        }
      }

      if (!$errors) {
        $targets = [];
        if ($sendAll) $targets[] = 'ALL';
        if ($groupIds) $targets['GROUP'] = $groupIds;

        $newId = AnnouncementRepository::create(
          (int)$user['user_id'],
          $subject,
          $body,
          $targets,
          $attachments
        );

        // redirect to the new announcement
        header('Location: '.$_SERVER['PHP_SELF'].'?id='.$newId.'&created=1');
        exit;
      }

    } elseif (isset($_POST['action']) && $_POST['action'] === 'reply') {
      $announcementId = (int)($_POST['announcement_id'] ?? 0);
      $replyBody      = trim($_POST['reply_body'] ?? '');

      if ($announcementId <= 0) $errors[] = 'Invalid announcement.';
      if ($replyBody === '')    $errors[] = 'Reply cannot be empty.';

      if (!$errors) {
        AnnouncementRepository::addReply($announcementId, (int)$user['user_id'], $replyBody);
        header('Location: '.$_SERVER['PHP_SELF'].'?id='.$announcementId.'&replied=1');
        exit;
      }
    }
  }
} catch (Throwable $e) {
  $errors[] = $e->getMessage();
}

/** ---- query (search + list + selected) ---- */
$search   = trim($_GET['q'] ?? '') ?: null;
$annList  = AnnouncementRepository::listRecent($search, 50);
$selectedId = isset($_GET['id']) ? (int)$_GET['id'] : ((int)($annList[0]['announcement_id'] ?? 0));
$selected = $selectedId ? AnnouncementRepository::find($selectedId) : null;

// compute sidebar time labels similar to your demo
function human_time(string $ts): string {
  $t = strtotime($ts);
  $diff = time() - $t;
  if ($diff < 3600) return floor($diff/60).'m ago';
  if ($diff < 86400) return floor($diff/3600).'h ago';
  if ($diff < 604800) return floor($diff/86400).'d ago';
  return date('M j', $t);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JaniKing – Communication Center</title>
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
    .cardx{background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:0 1px 0 rgba(17,24,39,.04)}
    .cardx .card-header{padding:14px 16px; border-bottom:1px solid var(--border); font-weight:700}
    .cardx .card-body{padding:16px}
    .tabs{display:flex; gap:8px; margin-bottom:12px}
    .tab{background:#f3f4f6; border:1px solid var(--border); color:#374151; padding:8px 12px; border-radius:8px; font-weight:700}
    .tab.active{background:var(--jk-blue); color:#fff; border-color:var(--jk-blue)}
    input[type="text"], textarea{width:100%; border:1px solid var(--border); border-radius:10px; padding:10px 12px; background:#fff; font:inherit}
    .form-control{border-color:var(--border); border-radius:10px}
    textarea{min-height:120px; resize:vertical}
    .pill{background:#f1f5f9; border:1px solid var(--border); padding:6px 10px; border-radius:999px; font-size:12px}
    .btnx{display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:10px; border:1px solid var(--border); background:#f8fafc; padding:10px 14px; font-weight:700}
    .btnx-primary{background:var(--jk-blue); border-color:var(--jk-blue); color:#fff}
    .hint{font-size:12px; color:var(--muted)}
    .grid{display:grid; grid-template-columns: 380px 1fr; gap:18px}
    .search{margin-bottom:8px}
    .list{display:flex; flex-direction:column; gap:10px; max-height:540px; overflow:auto}
    .item{border:1px solid var(--border); background:#fff; border-radius:10px; padding:10px 12px; display:grid; grid-template-columns:1fr auto; gap:6px}
    .item h4{margin:0; font-size:14px}
    .item p{margin:0; font-size:12px; color:var(--muted); line-height:1.4}
    .item small{color:#94a3b8}
    .item.active{outline:2px solid rgba(0,73,144,0.2)}
    .meta{display:flex; flex-wrap:wrap; gap:18px; color:#475569; font-size:13px; margin-bottom:4px}
    .attachments{display:flex; gap:10px; flex-wrap:wrap; margin-top:8px}
    .chip{display:inline-flex; align-items:center; gap:8px; padding:8px 10px; border-radius:10px; border:1px solid var(--border); background:#f8fafc; font-size:12px}
    .page-head{margin:6px 0 14px 0}
    .page-head h1{margin:0; font-size:26px; font-weight:800; color:#1f2937}
    .page-head p{margin:.25rem 0 0; color:var(--muted)}
    .avatar{width:36px; height:36px; border-radius:50%; object-fit:cover; object-position:center; box-shadow:0 0 0 2px #fff; border:1px solid var(--border);}
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
        <a href="staff_dash.php">
          <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg>
          <span>Dashboard</span>
        </a>
        <a href="staff_communication.php" class="active">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h12a3 3 0 0 1 3 3v5a3 3 0 0 1-3 3H11l-4 4v-4H4a3 3 0 0 1 3-3V8a3 3 0 0 1 3-3z"/></svg>
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
        <a href="staff_manage_trainings.php"><!-- filename is plural per your tree -->
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
        <h1>Communication Center</h1>
        <p>Manage all your messages and announcements in one place</p>
      </div>

      <?php if ($errors): ?>
        <div class="alert alert-danger"><?php foreach($errors as $er) echo '<div>'.e($er).'</div>'; ?></div>
      <?php elseif (isset($_GET['created'])): ?>
        <div class="alert alert-success">Announcement posted successfully.</div>
      <?php elseif (isset($_GET['replied'])): ?>
        <div class="alert alert-success">Reply sent.</div>
      <?php endif; ?>

      <!-- Compose -->
      <section class="cardx mb-3">
        <div class="card-header">Compose New Announcement</div>
        <div class="card-body">
          <div class="tabs">
            <button class="tab">New Message</button>
            <button class="tab active">Announcements</button>
            <button class="tab">Direct Messages</button>
          </div>

          <form method="post" enctype="multipart/form-data" class="mb-0">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create_announcement">

            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
              <label class="me-1" style="min-width:36px">To:</label>
              <!-- match the 3 pills you show in UI -->
              <label class="pill"><input type="checkbox" name="to_all" value="1" class="form-check-input me-1"> All Staff</label>
              <label class="pill"><input type="checkbox" name="group_ids[]" value="2" class="form-check-input me-1"> Cleaning Team</label>
              <label class="pill"><input type="checkbox" name="group_ids[]" value="3" class="form-check-input me-1"> Maintenance</label>
            </div>

            <div class="mb-2">
              <label class="mb-1">Subject:</label>
              <input type="text" name="subject" class="form-control" placeholder="Enter subject…" required />
            </div>

            <div class="mb-2">
              <label class="mb-1">Message:</label>
              <textarea name="body" class="form-control" placeholder="Type your message here…" required></textarea>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <div class="btnx" onclick="document.getElementById('file1').click()" type="button">Attach Files</div>
                <span class="hint">Maximum file size: 10MB</span>
              </div>
              <button class="btnx btnx-primary" type="submit">Send Announcement</button>
            </div>

            <!-- Hidden file inputs (you can add more if you want) -->
            <input id="file1" name="file1" type="file" class="d-none" />
            <input name="file2" type="file" class="d-none" />
            <input name="file3" type="file" class="d-none" />
          </form>
        </div>
      </section>

      <div class="grid">
        <!-- Left column: Recent Announcements -->
        <aside class="cardx">
          <div class="card-header">Recent Announcements</div>
          <div class="card-body">
            <form class="search" method="get">
              <input type="text" name="q" class="form-control" placeholder="Search announcements…" value="<?= e($search ?? '') ?>" />
            </form>
            <div class="list">
              <?php foreach ($annList as $a): ?>
                <?php
                  $active  = (int)$a['announcement_id'] === (int)$selectedId;
                  $snippet = trim((string)$a['snippet']);
                ?>
                <a class="item <?= $active ? 'active' : '' ?>" href="?id=<?= (int)$a['announcement_id'] ?>">
                  <div>
                    <h4><?= e($a['subject']) ?></h4>
                    <p><?= e($snippet) ?><?= strlen($snippet) >= 159 ? '…' : '' ?></p>
                  </div>
                  <small><?= e(human_time($a['created_at'])) ?></small>
                </a>
              <?php endforeach; ?>
              <?php if (!$annList): ?>
                <div class="text-muted">No announcements yet.</div>
              <?php endif; ?>
            </div>
          </div>
        </aside>

        <!-- Right column: Message detail -->
        <section class="cardx">
          <div class="card-header">
            <?= $selected ? e($selected['subject']) : 'No announcement selected' ?>
          </div>
          <div class="card-body">
            <?php if ($selected): ?>
              <div class="meta">
                <div><strong>From:</strong> <?= e(($selected['author_name'] ?? 'Unknown').' ('.$selected['author_role'].')') ?></div>
                <div><strong>To:</strong> <?= 'Recipients of this announcement' ?></div>
                <div><strong>Date:</strong> <?= e(date('F j, Y \a\t g:i A', strtotime($selected['created_at']))) ?></div>
              </div>

              <p><?= nl2br(e($selected['body'])) ?></p>

              <?php if (!empty($selected['attachments'])): ?>
                <div class="attachments">
                  <?php foreach ($selected['attachments'] as $f): ?>
                    <a class="chip" href="<?= e($f['server_path']) ?>" target="_blank" rel="noopener">
                      📎 <?= e($f['original_name']) ?> • <?= number_format((int)$f['file_size']/1024, 1) ?> KB
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <?php if (!empty($selected['replies'])): ?>
                <div class="mt-3 pt-3" style="border-top:1px solid var(--border)">
                  <h4 class="mb-2" style="margin:0">Thread</h4>
                  <?php foreach ($selected['replies'] as $r): ?>
                    <div class="mb-2">
                      <div class="small text-muted">
                        <strong><?= e($r['author_name'].' ('.$r['author_role'].')') ?></strong>
                        • <?= e(date('M j, g:i A', strtotime($r['created_at']))) ?>
                      </div>
                      <div><?= nl2br(e($r['body'])) ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <div class="mt-3" style="border-top:1px solid var(--border); padding-top:14px">
                <h4 class="mb-2" style="margin:0">Reply</h4>
                <form method="post">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="reply">
                  <input type="hidden" name="announcement_id" value="<?= (int)$selected['announcement_id'] ?>">
                  <textarea class="form-control" name="reply_body" placeholder="Type your reply here…"></textarea>
                  <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="btnx disabled" aria-disabled="true" title="Files in replies not implemented">Attach</div>
                    <button class="btnx btnx-primary" type="submit">Send Reply</button>
                  </div>
                </form>
              </div>
            <?php else: ?>
              <p class="text-muted mb-0">Create an announcement or select one from the left.</p>
            <?php endif; ?>
          </div>
        </section>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
