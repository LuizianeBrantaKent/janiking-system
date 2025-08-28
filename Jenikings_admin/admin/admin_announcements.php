<?php
// admin_announcements.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Janiking - Communication Center</title>
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
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid #ddd;
    }

    /* Navigation Tabs */
    .nav-tabs {
      display: flex;
      margin-bottom: 25px;
      border-bottom: 1px solid #ddd;
    }

    .nav-tab {
      padding: 12px 20px;
      cursor: pointer;
      font-weight: 600;
      color: #6c757d;
      border-bottom: 3px solid transparent;
      transition: all 0.3s;
    }

    .nav-tab.active {
      color: var(--brand-blue);
      border-bottom: 3px solid var(--brand-blue);
    }

    .nav-tab:hover {
      color: var(--brand-blue);
    }

    /* Section Header with Button */
    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .section-title {
      color: var(--brand-blue);
      font-size: 20px;
      padding-bottom: 10px;
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

    /* Two-column layout */
    .two-column-layout {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 30px;
    }

    /* Announcements Section */
    .announcements-container {
      margin-bottom: 30px;
    }

    .announcement-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      margin-bottom: 20px;
    }

    .announcement-title {
      font-size: 18px;
      font-weight: 600;
      color: var(--brand-blue);
      margin-bottom: 10px;
    }

    .announcement-date {
      color: #6c757d;
      font-size: 14px;
      margin-bottom: 15px;
    }

    .announcement-content {
      margin-bottom: 15px;
      line-height: 1.6;
    }

    .announcement-author {
      font-size: 14px;
      color: #6c757d;
    }

    .author-name {
      font-weight: 600;
      color: var(--brand-blue);
    }

    /* Statistics Section */
    .statistics-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }

    .stat-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 0;
      border-bottom: 1px solid #f1f1f1;
    }

    .stat-item:last-child {
      border-bottom: none;
    }

    .stat-label {
      font-size: 14px;
      color: #6c757d;
    }

    .stat-value {
      font-size: 24px;
      font-weight: 700;
      color: var(--brand-blue);
    }

    .stat-change {
      font-size: 12px;
      padding: 3px 8px;
      border-radius: 20px;
      background: #e8f5e9;
      color: #2e7d32;
    }

    .stat-change.negative {
      background: #ffebee;
      color: #c62828;
    }

    .stat-period {
      font-size: 12px;
      color: #6c757d;
      text-align: right;
      margin-top: 5px;
    }

    /* Compose Message Section */
    .compose-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }

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

    textarea.form-control {
      min-height: 120px;
      resize: vertical;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }

    .checkbox-group input {
      margin-right: 10px;
    }

    .btn-outline {
      background: transparent;
      border: 1px solid #ced4da;
      color: #495057;
      margin-right: 10px;
    }

    .btn-outline:hover {
      background: #f8f9fa;
    }

    /* Quick Contacts Section */
    .contacts-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .contact-item {
      display: flex;
      align-items: center;
      padding: 15px 0;
      border-bottom: 1px solid #f1f1f1;
    }

    .contact-item:last-child {
      border-bottom: none;
    }

    .contact-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--brand-blue);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      margin-right: 15px;
    }

    .contact-info {
      flex: 1;
    }

    .contact-name {
      font-weight: 600;
      color: var(--brand-blue;
    }

    .contact-role {
      font-size: 13px;
      color: #6c757d;
    }

    .view-all {
      text-align: center;
      margin-top: 15px;
    }

    .view-all a {
      color: var(--brand-blue);
      text-decoration: none;
      font-weight: 600;
    }

    .view-all a:hover {
      text-decoration: underline;
    }

    /* Responsive adjustments */
    @media (max-width: 992px){
      :root{ --nav-w: 80px; }
      .main-content{ padding:20px 15px; }
      .two-column-layout {
        grid-template-columns: 1fr;
      }
    }
    
    @media (max-width: 576px){
      .main-content{ margin-left:0; }
      .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
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
      <h1 class="page-title">Communication Center</h1>
      
      <!-- Navigation Tabs -->
      <div class="nav-tabs">
        <div class="nav-tab active">Announcements</div>
        <div class="nav-tab">Messages</div>
      </div>

      <div class="two-column-layout">
        <!-- Left Column -->
        <div>
          <!-- Recent Announcements -->
          <div class="announcements-container">
            <div class="section-header">
              <h2 class="section-title">Recent Announcements</h2>
              <button class="btn btn-primary">
                <i class="fas fa-plus"></i> New Announcement
              </button>
            </div>
            
            <div class="announcement-card">
              <h3 class="announcement-title">System Maintenance Scheduled</h3>
              <div class="announcement-date">2 days ago</div>
              <div class="announcement-content">
                The system will be undergoing maintenance on Saturday from 2AM to 5AM EST. Please ensure all pending tasks are completed beforehand.
              </div>
              <div class="announcement-author">
                <span class="author-name">Posted by: Sarah Johnson</span> IT Department
              </div>
            </div>
            
            <div class="announcement-card">
              <h3 class="announcement-title">New Client Onboarding Process</h3>
              <div class="announcement-date">1 week ago</div>
              <div class="announcement-content">
                We've updated our client onboarding process. Please review the new documentation and attend the training session next Tuesday.
              </div>
              <div class="announcement-author">
                <span class="author-name">Posted by: Michael Rodrigues</span> Operations
              </div>
            </div>
            
            <div class="announcement-card">
              <h3 class="announcement-title">Quarterly Review Meeting</h3>
              <div class="announcement-date">2 weeks ago</div>
              <div class="announcement-content">
                The quarterly review meeting is scheduled for next Friday at 10AM in the main conference room. All department heads are required to attend.
              </div>
              <div class="announcement-author">
                <span class="author-name">Posted by: Jennifer Williams</span> Executive Office
              </div>
            </div>
            
            <div class="announcement-card">
              <h3 class="announcement-title">Holiday Schedule Update</h3>
              <div class="announcement-date">3 weeks ago</div>
              <div class="announcement-content">
                Please note that our office will be closed on July 3rd in observance of Independence Day. On-call support will be available for emergencies.
              </div>
              <div class="announcement-author">
                <span class="author-name">Posted by: Robert Chen</span> Human Resources
              </div>
            </div>
          </div>
        </div>
        
        <!-- Right Column -->
        <div>
          <!-- Message Statistics -->
          <div class="statistics-card">
            <h2 class="section-title">Message Statistics</h2>
            
            <div class="stat-item">
              <div>
                <div class="stat-label">Total Messages</div>
                <div class="stat-value">247</div>
              </div>
              <div class="stat-change">+12%</div>
            </div>
            
            <div class="stat-item">
              <div>
                <div class="stat-label">Response Rate</div>
                <div class="stat-value">94%</div>
              </div>
              <div class="stat-change">+3%</div>
            </div>
            
            <div class="stat-item">
              <div>
                <div class="stat-label">Avg. Response Time</div>
                <div class="stat-value">3.2h</div>
              </div>
              <div class="stat-change negative">-0.5h</div>
            </div>
            
            <div class="stat-period">Last 7 days</div>
          </div>
          
          <!-- Compose Message -->
          <div class="compose-card">
            <h2 class="section-title">Compose Message</h2>
            
            <div class="form-group">
              <label for="recipients">Recipients</label>
              <select class="form-control" id="recipients">
                <option>Select recipients...</option>
                <option>All Users</option>
                <option>Administrators</option>
                <option>Staff Members</option>
                <option>Department Heads</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="subject">Subject</label>
              <input type="text" class="form-control" id="subject" placeholder="Enter subject...">
            </div>
            
            <div class="form-group">
              <label for="message">Message</label>
              <textarea class="form-control" id="message" placeholder="Type your message here..."></textarea>
            </div>
            
            <div class="checkbox-group">
              <input type="checkbox" id="priority">
              <label for="priority">Mark as priority</label>
            </div>
            
            <div>
              <button class="btn btn-outline">
                <i class="fas fa-paperclip"></i> Attach File
              </button>
              <button class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Send Message
              </button>
            </div>
          </div>
          
          <!-- Quick Contacts -->
          <div class="contacts-card">
            <h2 class="section-title">Quick Contacts</h2>
            
            <div class="contact-item">
              <div class="contact-avatar">EP</div>
              <div class="contact-info">
                <div class="contact-name">Emily Parker</div>
                <div class="contact-role">Operation Manager</div>
              </div>
            </div>
            
            <div class="contact-item">
              <div class="contact-avatar">MJ</div>
              <div class="contact-info">
                <div class="contact-name">Marcus Johnson</div>
                <div class="contact-role">IT Director</div>
              </div>
            </div>
            
            <div class="contact-item">
              <div class="contact-avatar">SR</div>
              <div class="contact-info">
                <div class="contact-name">Sophia Rodriguez</div>
                <div class="contact-role">HR Manager</div>
              </div>
            </div>
            
            <div class="contact-item">
              <div class="contact-avatar">DC</div>
              <div class="contact-info">
                <div class="contact-name">David Chen</div>
                <div class="contact-role">Finance Director</div>
              </div>
            </div>
            
            <div class="view-all">
              <a href="#">View all contacts</a>
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
        if (path.endsWith('admin_announcements.php')) {
          a.classList.add('active');
        }
      });
      
      // Tab switching functionality
      const tabs = document.querySelectorAll('.nav-tab');
      tabs.forEach(tab => {
        tab.addEventListener('click', () => {
          tabs.forEach(t => t.classList.remove('active'));
          tab.classList.add('active');
        });
      });
    });
  </script>
</body>
</html>