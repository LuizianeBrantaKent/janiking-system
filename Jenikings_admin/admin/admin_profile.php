<?php
// admin_profile.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Janiking - Profile Settings</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root{
      --header-h: 80px;   /* match header.php fixed height */
      --nav-w: 220px;     /* match admin_navbar.php width */
      --brand-blue: #004990;
    }

    *{ margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

    body{
      background:#f8f9fa;
      color:#333;
      height:100vh;
      overflow:hidden; /* scroll inside main-content only */
    }

    /* --- Layout that coordinates header + navbar + content --- */
    .main-container {
      display: flex;
      height: 100vh;
      padding-top: 80px; /* header height */
    }

    /* Ensure any included sidebar sits under header and spans the rest of the viewport */
    .admin-navbar{
      position:fixed !important;
      top:var(--header-h) !important;
      left:0 !important;
      width:var(--nav-w) !important;
      height:calc(100vh - var(--header-h)) !important;
      overflow-y:auto;
      background:#fff;
      border-right:1px solid #ddd;
      z-index: 900; /* below header if header has higher z-index */
    }

    /* Content area to the right of the sidebar */
    .main-content {
      flex: 1;
      padding: 30px;
      overflow-y: auto;
      background: #e9ecef; /* same as body to make it seamless with header */
    }

    /* Page Title */
    .page-title {
      color: var(--brand-blue);
      font-size: 28px;
      margin-bottom: 10px;
    }

    .page-subtitle {
      color: #6c757d;
      margin-bottom: 30px;
      font-size: 16px;
    }

    /* Two-column layout */
    .two-column-layout {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
    }

    /* Profile Sections */
    .profile-section {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }

    .section-title {
      color: var(--brand-blue);
      font-size: 20px;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }

    /* Personal Information */
    .info-item {
      margin-bottom: 20px;
    }

    .info-label {
      font-size: 14px;
      color: #6c757d;
      margin-bottom: 5px;
    }

    .info-value {
      font-size: 16px;
      font-weight: 500;
      color: #333;
    }

    .info-note {
      font-size: 13px;
      color: #6c757d;
      font-style: italic;
      margin-top: 5px;
    }

    /* Profile Picture */
    .profile-picture {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 20px;
    }

    .avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: var(--brand-blue);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 36px;
      font-weight: 600;
    }

    .change-photo-btn {
      padding: 8px 15px;
      background: var(--brand-blue);
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .change-photo-btn:hover {
      background: #003b73;
    }

    .file-note {
      font-size: 13px;
      color: #6c757d;
      margin-top: 10px;
    }

    /* Form Elements */
    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #495057;
    }

    .form-control {
      width: 100%;
      padding: 10px 15px;
      border: 1px solid #ced4da;
      border-radius: 5px;
      font-size: 14px;
    }

    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-primary {
      background: var(--brand-blue);
      color: white;
    }

    .btn-primary:hover {
      background: #003b73;
    }

    /* Notification Preferences */
    .preference-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 0;
      border-bottom: 1px solid #f1f1f1;
    }

    .preference-item:last-child {
      border-bottom: none;
    }

    .preference-info {
      flex: 1;
    }

    .preference-title {
      font-weight: 600;
      margin-bottom: 5px;
    }

    .preference-description {
      font-size: 13px;
      color: #6c757d;
    }

    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 24px;
    }

    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 24px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: var(--brand-blue);
    }

    input:checked + .slider:before {
      transform: translateX(26px);
    }

    .manage-link {
      text-align: center;
      margin-top: 15px;
    }

    .manage-link a {
      color: var(--brand-blue);
      text-decoration: none;
      font-weight: 600;
    }

    .manage-link a:hover {
      text-decoration: underline;
    }

    /* Account Settings */
    .account-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 0;
      border-bottom: 1px solid #f1f1f1;
    }

    .account-item:last-child {
      border-bottom: none;
    }

    .account-info {
      flex: 1;
    }

    .account-title {
      font-weight: 600;
      margin-bottom: 5px;
    }

    .account-description {
      font-size: 13px;
      color: #6c757d;
    }

    .language-select {
      padding: 5px 10px;
      border: 1px solid #ced4da;
      border-radius: 5px;
      background: white;
    }

    /* Responsive adjustments */
    @media (max-width: 992px){
      :root{ --nav-w: 80px; }
      .main-content{ padding:20px 15px; }
      .two-column-layout {
        grid-template-columns: 1fr;
      }
    }
    
    @media (max-width: 768px){
      .profile-picture {
        flex-direction: column;
        text-align: center;
      }
      
      .preference-item, .account-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
    }
    
    @media (max-width: 576px){
      .main-content{ margin-left:0; }
    }
  </style>
</head>
<body>

  <?php
    // Fixed header (should include the logo from /assets/logo.png inside header.php)
    include('../includes/header.php');

    // Fixed sidebar below header
    include('../includes/admin_navbar.php');
  ?>

  <div class="main-container">
    <main class="main-content">
      <h1 class="page-title">Profile Settings</h1>
      <p class="page-subtitle">View and update your personal information and preferences</p>

      <div class="two-column-layout">
        <!-- Left Column -->
        <div>
          <!-- Personal Information -->
          <div class="profile-section">
            <h2 class="section-title">Personal Information</h2>
            
            <div class="info-item">
              <div class="info-label">Full Name</div>
              <div class="info-value">Michael Thompson</div>
            </div>
            
            <div class="info-item">
              <div class="info-label">Email Address</div>
              <div class="info-value">michael.thompson@janiking.com</div>
              <div class="info-note">Contact administrator to change email address</div>
            </div>
            
            <div class="info-item">
              <div class="info-label">Phone Number</div>
              <div class="info-value">(555) 123-4567</div>
            </div>
            
            <div class="info-item">
              <div class="info-label">Position</div>
              <div class="info-value">Cleaning Supervisor</div>
              <div class="info-note">Position is set by your administrator</div>
            </div>
            
            <div class="info-item">
              <div class="info-label">Location</div>
              <div class="info-value">Chicago, IL</div>
            </div>
            
            <div class="info-item">
              <div class="info-label">Timezone</div>
              <div class="info-value">America/Chicago (CST)</div>
            </div>
          </div>
          
          <!-- Profile Picture -->
          <div class="profile-section">
            <h2 class="section-title">Profile Picture</h2>
            
            <div class="profile-picture">
              <div class="avatar">MT</div>
              <div>
                <button class="change-photo-btn">Change Photo</button>
                <div class="file-note">JPG, GIF or PNG. Max size 2MB.</div>
              </div>
            </div>
          </div>
          
          <!-- Change Password -->
          <div class="profile-section">
            <h2 class="section-title">Change Password</h2>
            
            <div class="form-group">
              <label for="current-password">Current Password</label>
              <input type="password" class="form-control" id="current-password" placeholder="..........">
            </div>
            
            <div class="form-group">
              <label for="new-password">New Password</label>
              <input type="password" class="form-control" id="new-password" placeholder="..........">
            </div>
            
            <div class="form-group">
              <label for="confirm-password">Confirm New Password</label>
              <input type="password" class="form-control" id="confirm-password" placeholder="..........">
            </div>
            
            <button class="btn btn-primary">Update Password</button>
          </div>
        </div>
        
        <!-- Right Column -->
        <div>
          <!-- Notification Preferences -->
          <div class="profile-section">
            <h2 class="section-title">Notification Preferences</h2>
            
            <div class="preference-item">
              <div class="preference-info">
                <div class="preference-title">Email Notifications</div>
                <div class="preference-description">Receive notifications via email</div>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
              </label>
            </div>
            
            <div class="preference-item">
              <div class="preference-info">
                <div class="preference-title">Task Assignments</div>
                <div class="preference-description">Get notified when you're assigned a new task</div>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
              </label>
            </div>
            
            <div class="preference-item">
              <div class="preference-info">
                <div class="preference-title">Schedule Changes</div>
                <div class="preference-description">Get notified about changes to your schedule</div>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
              </label>
            </div>
            
            <div class="preference-item">
              <div class="preference-info">
                <div class="preference-title">Training Updates</div>
                <div class="preference-description">Get notified about new training materials</div>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
              </label>
            </div>
            
            <div class="preference-item">
              <div class="preference-info">
                <div class="preference-title">Document Updates</div>
                <div class="preference-description">Get notified when documents are updated</div>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
              </label>
            </div>
            
            <div class="manage-link">
              <a href="#">Manage All Notifications</a>
            </div>
          </div>
          
          <!-- Account Settings -->
          <div class="profile-section">
            <h2 class="section-title">Account Settings</h2>
            
            <div class="account-item">
              <div class="account-info">
                <div class="account-title">Two-Factor Authentication</div>
                <div class="account-description">Add an extra layer of security to your account</div>
              </div>
              <label class="toggle-switch">
                <input type="checkbox">
                <span class="slider"></span>
              </label>
            </div>
            
            <div class="account-item">
              <div class="account-info">
                <div class="account-title">Language</div>
                <div class="account-description">Select your preferred language</div>
              </div>
              <select class="language-select">
                <option>English</option>
                <option>Spanish</option>
                <option>French</option>
                <option>German</option>
                <option>Chinese</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script>
    // Highlight the current page link in the sidebar if present
    document.addEventListener('DOMContentLoaded', function(){
      const navLinks = document.querySelectorAll('.admin-navbar a.nav-link, .admin-navbar a');
      const here = location.pathname.replace(/\/+$/, '');
      navLinks.forEach(a=>{
        const href = a.getAttribute('href') || '';
        if (!href) return;
        const path = href.replace(/\/+$/, '');
        if (path.endsWith('admin_profile.php')) {
          a.classList.add('active');
        }
      });
    });
  </script>
</body>
</html>