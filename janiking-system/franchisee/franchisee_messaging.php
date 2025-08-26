<?php
// ====== PAGE CONFIG ======
$pageTitle = "Messages";

// --- Example data (replace with DB results) ---
$tabs = ["announcements" => "Announcements", "direct" => "Direct Messages"];

// Messages list items (LEFT PANE). Replace with SELECT id, tab, subject, preview, sender, sent_at ...
$messages = [
  ["id"=>1, "tab"=>"announcements", "subject"=>"New Cleaning Protocol Update", "preview"=>"Important update regarding the new cleaning protocols...", "sender"=>"Operations Team", "ago"=>"2h"],
  ["id"=>2, "tab"=>"announcements", "subject"=>"Quarterly Business Review", "preview"=>"Please schedule your quarterly business review...", "sender"=>"Regional Director", "ago"=>"Yesterday"],
  ["id"=>3, "tab"=>"announcements", "subject"=>"New Training Materials Available", "preview"=>"We’ve added new eco-friendly cleaning products training...", "sender"=>"Training Dept", "ago"=>"2 days ago"],
  ["id"=>4, "tab"=>"announcements", "subject"=>"Holiday Schedule Reminder", "preview"=>"Reminder about the upcoming holiday schedule adjustments...", "sender"=>"HR Department", "ago"=>"3 days ago"],
  ["id"=>5, "tab"=>"direct", "subject"=>"Invoice #INV-2023-042", "preview"=>"Hi, the invoice is due in 5 days. Please process...", "sender"=>"Billing", "ago"=>"1 week ago"],
];

// Message detail (RIGHT PANE). Replace with SELECT by id (title, body html/text, attachments, meta...)
$currentMessage = [
  "id"=>1,
  "title"=>"New Cleaning Protocol Update",
  "from"=>"Operations Team",
  "sent_at"=>"May 15, 2023 at 10:30 AM",
  "body"=><<<HTML
<p>Dear Franchisees,</p>
<p>We are writing to inform you about important updates to our cleaning protocols, particularly for healthcare facilities. These changes are in response to the latest industry standards and client requirements.</p>
<p><strong>The key updates include:</strong></p>
<ul>
  <li>Enhanced disinfection procedures for high-touch surfaces</li>
  <li>New requirements for PPE when handling certain cleaning agents</li>
  <li>Updated documentation processes for infection control areas</li>
  <li>Revised frequency schedules for critical care environments</li>
</ul>
<p>Please ensure all your team members are trained on these new protocols by June 1st. You can find detailed documentation and training materials in the Documents &amp; Training section of the portal.</p>
<p>Best regards,<br>The Operations Team</p>
HTML,
  "attachments"=>[
    ["name"=>"Healthcare_Cleaning_Protocol_v2.pdf", "url"=>"#"],
    ["name"=>"Training_Video_Healthcare_Protocols.mp4", "url"=>"#"],
  ],
];
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
    :root{ --jk-bg:#e9ecef; --jk-primary:#004990; --jk-sidebar:#fff; }
    body{ background:var(--jk-bg); }
    .jk-layout{ min-height:100vh; display:flex; }
    .jk-sidebar{ width:260px; flex:0 0 260px; background:var(--jk-sidebar); border-right:1px solid rgba(0,0,0,.075); position:sticky; top:0; height:100vh; }
    .jk-main{ flex:1; display:flex; flex-direction:column; }
    .jk-topbar{ position:sticky; top:0; z-index:1020; background:#fff; border-bottom:1px solid rgba(0,0,0,.075); }
    .jk-content{ padding:1.25rem; }

    .jk-logo img{ max-width:160px; height:auto; }
    .jk-logo-placeholder{ width:160px; height:40px; background:#f1f3f5; border:1px dashed #adb5bd; border-radius:.5rem; display:flex; align-items:center; justify-content:center; color:#6c757d; font-size:.875rem; }

    /* Messages layout */
    .message-list{ max-height:70vh; overflow:auto; }
    .message-item{ cursor:pointer; }
    .message-item.active{ background:rgba(0,0,0,.05); }
    .message-body{ max-height:70vh; overflow:auto; }

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
        <div class="jk-logo">
          <!-- Replace with your logo -->
          <!-- <img src="/path/to/logo.png" alt="JaniKing"> -->
          <div class="jk-logo-placeholder">Your Logo</div>
        </div>
      </a>
    </div>
    <nav class="p-2">
      <ul class="nav nav-pills flex-column gap-1">
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2 active" href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#"><i class="bi bi-mortarboard"></i> Training</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#"><i class="bi bi-folder2-open"></i> Documents</a></li>
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
        <div class="row g-3">
          <!-- LEFT: list -->
          <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white">
                <ul class="nav nav-tabs card-header-tabs" id="msgTabs" role="tablist">
                  <?php $first=true; foreach($tabs as $key=>$label): ?>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link <?php echo $first?'active':''; ?>" data-bs-toggle="tab" data-bs-target="#tab-<?php echo $key; ?>" type="button" role="tab">
                        <?php echo htmlspecialchars($label); ?>
                      </button>
                    </li>
                  <?php $first=false; endforeach; ?>
                </ul>
              </div>
              <div class="card-body p-2">
                <div class="input-group input-group-sm mb-2 px-1">
                  <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                  <input type="text" class="form-control" placeholder="Search messages…" />
                </div>

                <div class="tab-content">
                  <?php foreach($tabs as $key=>$label): ?>
                    <div class="tab-pane fade <?php echo ($key==='announcements')?'show active':''; ?>" id="tab-<?php echo $key; ?>">
                      <div class="list-group message-list">
                        <?php foreach($messages as $m): if($m['tab']!==$key) continue; ?>
                          <a href="?id=<?php echo $m['id']; ?>" 
                             class="list-group-item list-group-item-action message-item <?php echo ($m['id']==$currentMessage['id'])?'active':''; ?>">
                            <div class="d-flex justify-content-between">
                              <h6 class="mb-1"><?php echo htmlspecialchars($m['subject']); ?></h6>
                              <small class="text-muted"><?php echo htmlspecialchars($m['ago']); ?></small>
                            </div>
                            <div class="small text-muted text-truncate"><?php echo htmlspecialchars($m['preview']); ?></div>
                            <div class="small text-muted">From: <?php echo htmlspecialchars($m['sender']); ?></div>
                          </a>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- RIGHT: detail -->
          <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h2 class="h6 mb-0"><?php echo htmlspecialchars($currentMessage['title']); ?></h2>
                    <small class="text-muted">From: <?php echo htmlspecialchars($currentMessage['from']); ?> &nbsp; • &nbsp; Sent: <?php echo htmlspecialchars($currentMessage['sent_at']); ?></small>
                  </div>
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-reply"></i></button>
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-archive"></i></button>
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-three-dots"></i></button>
                  </div>
                </div>
              </div>
              <div class="card-body message-body">
                <?php echo $currentMessage['body']; ?>
                <?php if(!empty($currentMessage['attachments'])): ?>
                  <div class="mt-3">
                    <?php foreach($currentMessage['attachments'] as $att): ?>
                      <a href="<?php echo $att['url']; ?>" class="btn btn-sm btn-light border me-2 mb-2">
                        <i class="bi bi-paperclip me-1"></i> <?php echo htmlspecialchars($att['name']); ?>
                      </a>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
              <div class="card-footer bg-white">
                <form>
                  <label class="form-label small text-muted">Reply to this message</label>
                  <textarea class="form-control mb-2" rows="4" placeholder="Type your reply here..."></textarea>
                  <div class="d-flex justify-content-end">
                    <button class="btn btn-primary"><i class="bi bi-send me-1"></i> Send Reply</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

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
  toggle?.addEventListener('click', () => sidebar.classList.contains('show') ? closeSidebar() : openSidebar());
  backdrop?.addEventListener('click', closeSidebar);
</script>
</body>
</html>
