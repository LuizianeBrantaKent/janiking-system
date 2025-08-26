<?php
// ====== PAGE CONFIG ======
$pageTitle = "Profile / Settings";

// ====== EXAMPLE USER (replace with DB row) ======
$user = [
  "first_name" => "Michael",
  "last_name"  => "Reynolds",
  "email"      => "michael.reynolds@example.com",
  "phone"      => "(555) 123-4567",
  "dob"        => "1978-05-12",
  "language"   => "English",
  "business_name" => "Reynolds Cleaning Services LLC",
  "franchise_id"  => "JK-2458-FR",
  "address"    => "1234 Business Ave, Suite 500",
  "city"       => "Austin",
  "state"      => "Texas",
  "zip"        => "78701",
  "tax_id"     => "XX-XXXXXXXX",
  "years"      => 7,
  "username"   => "mreynolds",
  // toggles
  "notif_email" => true,
  "notif_sms"   => true,
  "notif_mkt"   => false,
  // avatar (use your stored path)
  "avatar"      => "https://i.pravatar.cc/150?img=12"
];

// Handle POST later (save to DB, validate, etc.)
// if ($_SERVER['REQUEST_METHOD']==='POST') { ... }
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
    .jk-logo-placeholder{ width:160px; height:40px; background:#f1f3f5; border:1px dashed #adb5bd; border-radius:.5rem; display:flex; align-items:center; justify-content:center; color:#6c757d; font-size:.875rem; }
    .avatar{ width:72px; height:72px; border-radius:50%; object-fit:cover; }
    .section-title{ font-weight:700; color:#1f2937; font-size:.95rem; margin-bottom:.25rem; }
    .section-rule{ margin: .25rem 0 1rem; }
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
        <!-- Replace with your logo -->
        <div class="jk-logo-placeholder">Your Logo</div>
      </a>
    </div>
    <nav class="p-2">
      <ul class="nav nav-pills flex-column gap-1">
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="training.php"><i class="bi bi-mortarboard"></i> Training</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="documents.php"><i class="bi bi-folder2-open"></i> Documents</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="reports.php"><i class="bi bi-bar-chart-line"></i> Reports</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="buy-products.php"><i class="bi bi-cart3"></i> Buy Products</a></li>
        <li class="nav-item mt-2"><hr></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2 active" href="profile-settings.php"><i class="bi bi-person-gear"></i> Profile/Settings</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2 text-danger" href="#"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main -->
  <div class="jk-main">
    <!-- Header -->
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
        <form class="card border-0 shadow-sm" method="post" enctype="multipart/form-data">
          <div class="card-body">
            <!-- Header with avatar -->
            <div class="d-flex align-items-center gap-3 mb-3">
              <img src="<?php echo htmlspecialchars($user['avatar']); ?>" class="avatar" id="avatarPreview" alt="Avatar">
              <div>
                <div class="fw-semibold"><?php echo htmlspecialchars($user['first_name'].' '.$user['last_name']); ?></div>
                <div class="text-muted small">Premium Franchisee</div>
                <label class="btn btn-sm btn-outline-secondary mt-2">
                  <i class="bi bi-camera me-1"></i> Change Photo
                  <input type="file" class="d-none" name="avatar" id="avatarInput" accept="image/*">
                </label>
              </div>
            </div>

            <!-- Personal Information -->
            <div class="section-title">Personal Information</div>
            <hr class="section-rule">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">First Name</label>
                <input class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <input class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Preferred Language</label>
                <select class="form-select" name="language">
                  <?php foreach (["English","Spanish","French","Portuguese","German","Chinese"] as $lang): ?>
                    <option <?php echo $user['language']===$lang?'selected':''; ?>>
                      <?php echo htmlspecialchars($lang); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Business Information -->
            <div class="section-title mt-4">Business Information</div>
            <hr class="section-rule">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Business Name</label>
                <input class="form-control" name="business_name" value="<?php echo htmlspecialchars($user['business_name']); ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Franchise ID</label>
                <input class="form-control" name="franchise_id" value="<?php echo htmlspecialchars($user['franchise_id']); ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Business Address</label>
                <input class="form-control" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label">City</label>
                <input class="form-control" name="city" value="<?php echo htmlspecialchars($user['city']); ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label">State</label>
                <input class="form-control" name="state" value="<?php echo htmlspecialchars($user['state']); ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label">ZIP Code</label>
                <input class="form-control" name="zip" value="<?php echo htmlspecialchars($user['zip']); ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label">Tax ID (EIN)</label>
                <input class="form-control" name="tax_id" value="<?php echo htmlspecialchars($user['tax_id']); ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label">Years as Franchisee</label>
                <input type="number" min="0" class="form-control" name="years" value="<?php echo htmlspecialchars($user['years']); ?>">
              </div>
            </div>

            <!-- Account Settings -->
            <div class="section-title mt-4">Account Settings</div>
            <hr class="section-rule">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Username</label>
                <input class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" placeholder="••••••••">
              </div>
            </div>

            <!-- Notification Preferences -->
            <div class="section-title mt-4">Notification Preferences</div>
            <hr class="section-rule">
            <div class="row g-3">
              <div class="col-md-4">
                <div class="d-flex align-items-center justify-content-between border rounded p-2">
                  <div>
                    <div class="fw-semibold small mb-1">Email Notifications</div>
                    <div class="text-muted small">Receive updates about your account via email</div>
                  </div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="notif_email" <?php echo $user['notif_email']?'checked':''; ?>>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex align-items-center justify-content-between border rounded p-2">
                  <div>
                    <div class="fw-semibold small mb-1">SMS Notifications</div>
                    <div class="text-muted small">Receive text messages for important updates</div>
                  </div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="notif_sms" <?php echo $user['notif_sms']?'checked':''; ?>>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex align-items-center justify-content-between border rounded p-2">
                  <div>
                    <div class="fw-semibold small mb-1">Marketing Communications</div>
                    <div class="text-muted small">Receive promotional offers and news</div>
                  </div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="notif_mkt" <?php echo $user['notif_mkt']?'checked':''; ?>>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <div class="card-footer bg-white d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-light">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
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

// Avatar preview
const avatarInput = document.getElementById('avatarInput');
const avatarPreview = document.getElementById('avatarPreview');
avatarInput?.addEventListener('change', (e)=>{
  const file = e.target.files?.[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => avatarPreview.src = ev.target.result;
  reader.readAsDataURL(file);
});
</script>
</body>
</html>
```
