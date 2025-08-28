<?php
// admin_manage_users.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Janiking - Manage Users</title>
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

    /* User Controls */
    .user-controls {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .search-box {
      display: flex;
      align-items: center;
      background: white;
      border-radius: 5px;
      padding: 8px 15px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .search-box input {
      border: none;
      outline: none;
      padding: 5px;
      width: 250px;
    }

    .action-controls {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn {
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .btn-primary {
      background: var(--brand-blue);
      color: white;
    }

    .btn-primary:hover {
      background: #003b73;
    }

    .btn-outline {
      background: transparent;
      border: 1px solid #ced4da;
      color: #495057;
    }

    .btn-outline:hover {
      background: #f8f9fa;
    }

    /* Users Table */
    .users-table {
      width: 100%;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }

    .users-table table {
      width: 100%;
      border-collapse: collapse;
    }

    .users-table th {
      background-color: var(--brand-blue);
      color: white;
      text-align: left;
      padding: 15px;
    }

    .users-table td {
      padding: 15px;
      border-bottom: 1px solid #f1f1f1;
    }

    .users-table tr:last-child td {
      border-bottom: none;
    }

    .action-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
    }

    .action-btn {
      padding: 5px 10px;
      border-radius: 5px;
      font-size: 13px;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 3px;
      border: 1px solid transparent;
    }

    .view-btn {
      background: #e3f2fd;
      color: #1565c0;
      border-color: #bbdefb;
    }

    .edit-btn {
      background: #e8f5e9;
      color: #2e7d32;
      border-color: #c8e6c9;
    }

    .activate-btn {
      background: #e8f5e9;
      color: #2e7d32;
      border-color: #c8e6c9;
    }

    .deactivate-btn {
      background: #fff3e0;
      color: #ef6c00;
      border-color: #ffe0b2;
    }

    .delete-btn {
      background: #ffebee;
      color: #c62828;
      border-color: #ffcdd2;
    }

    .table-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px;
      background: #f8f9fa;
      border-top: 1px solid #ddd;
    }

    .pagination {
      display: flex;
      gap: 5px;
    }

    .page-btn {
      padding: 5px 10px;
      border: 1px solid #ddd;
      background: white;
      border-radius: 3px;
      cursor: pointer;
      font-size: 13px;
    }

    .page-btn.active {
      background: var(--brand-blue);
      color: white;
      border-color: var(--brand-blue);
    }

    .page-btn:hover:not(.active) {
      background: #f1f1f1;
    }

    .page-btn.disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    /* Two-column layout */
    .two-column-layout {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
    }

    /* User Statistics */
    .stats-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }

    .stats-card h3 {
      font-size: 16px;
      margin-bottom: 15px;
      color: var(--brand-blue);
    }

    .stat-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 0;
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
      font-size: 18px;
      font-weight: 700;
      color: var(--brand-blue);
    }

    /* Roles Distribution */
    .roles-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }

    .roles-card h3 {
      font-size: 16px;
      margin-bottom: 15px;
      color: var(--brand-blue);
    }

    .role-item {
      margin-bottom: 15px;
    }

    .role-name {
      display: flex;
      justify-content: space-between;
      margin-bottom: 5px;
    }

    .progress-bar {
      height: 10px;
      background: #e9ecef;
      border-radius: 5px;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      background: var(--brand-blue);
    }

    /* Recent Activities */
    .activities-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .activities-card h3 {
      font-size: 16px;
      margin-bottom: 15px;
      color: var(--brand-blue);
    }

    .activity-item {
      padding: 12px 0;
      border-bottom: 1px solid #f1f1f1;
    }

    .activity-item:last-child {
      border-bottom: none;
    }

    .activity-item p {
      margin-bottom: 5px;
      font-size: 14px;
    }

    .activity-meta {
      color: #6c757d;
      font-size: 12px;
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
    
    @media (max-width: 768px){
      .user-controls {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .action-controls {
        width: 100%;
        justify-content: space-between;
      }
      
      .search-box {
        width: 100%;
      }
      
      .search-box input {
        width: 100%;
      }
      
      .action-buttons {
        flex-direction: column;
        gap: 3px;
      }
      
      .table-footer {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
      }
      
      .pagination {
        flex-wrap: wrap;
      }
    }
    
    @media (max-width: 576px){
      .main-content{ margin-left:0; }
      .users-table {
        overflow-x: auto;
        display: block;
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
      <h1 class="page-title">Manage Users</h1>

      <!-- User Controls -->
      <div class="user-controls">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Search users...">
        </div>
        
        <div class="action-controls">
          <button class="btn btn-outline">
            <i class="fas fa-filter"></i> Filter
          </button>
          <button class="btn btn-outline">
            <i class="fas fa-download"></i> Export
          </button>
          <button class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Add User
          </button>
        </div>
      </div>
      
      <!-- Users Table -->
      <div class="users-table">
        <table>
          <thead>
            <tr>
              <th>User ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Department</th>
              <th>Last Login</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#USR-1024</td>
              <td>Michael Thompson</td>
              <td>m.thompson@janiking.com</td>
              <td>IT Department</td>
              <td>Today, 09:42 AM</td>
              <td>
                <div class="action-buttons">
                  <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i> View</a>
                  <a href="#" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                  <a href="#" class="action-btn deactivate-btn"><i class="fas fa-user-slash"></i> Deactivate</a>
                  <a href="#" class="action-btn delete-btn"><i class="fas fa-trash"></i> Delete</a>
                </div>
              </td>
            </tr>
            <tr>
              <td>#USR-0872</td>
              <td>Sarah Martinez</td>
              <td>s.martinez@janiking.com</td>
              <td>Operations</td>
              <td>Yesterday, 16:15 PM</td>
              <td>
                <div class="action-buttons">
                  <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i> View</a>
                  <a href="#" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                  <a href="#" class="action-btn deactivate-btn"><i class="fas fa-user-slash"></i> Deactivate</a>
                  <a href="#" class="action-btn delete-btn"><i class="fas fa-trash"></i> Delete</a>
                </div>
              </td>
            </tr>
            <tr>
              <td>#USR-0654</td>
              <td>David Wilson</td>
              <td>d.wilson@janiking.com</td>
              <td>Cleaning Services</td>
              <td>Oct 12, 2023, 08:30 AM</td>
              <td>
                <div class="action-buttons">
                  <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i> View</a>
                  <a href="#" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                  <a href="#" class="action-btn deactivate-btn"><i class="fas fa-user-slash"></i> Deactivate</a>
                  <a href="#" class="action-btn delete-btn"><i class="fas fa-trash"></i> Delete</a>
                </div>
              </td>
            </tr>
            <tr>
              <td>#USR-0543</td>
              <td>Jennifer Lee</td>
              <td>j.lee@janiking.com</td>
              <td>Human Resources</td>
              <td>Oct 10, 2023, 14:23 PM</td>
              <td>
                <div class="action-buttons">
                  <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i> View</a>
                  <a href="#" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                  <a href="#" class="action-btn deactivate-btn"><i class="fas fa-user-slash"></i> Deactivate</a>
                  <a href="#" class="action-btn delete-btn"><i class="fas fa-trash"></i> Delete</a>
                </div>
              </td>
            </tr>
            <tr>
              <td>#USR-0421</td>
              <td>Robert Johnson</td>
              <td>r.johnson@janiking.com</td>
              <td>Accounting</td>
              <td>Sep 28, 2023, 11:05 AM</td>
              <td>
                <div class="action-buttons">
                  <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i> View</a>
                  <a href="#" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                  <a href="#" class="action-btn activate-btn"><i class="fas fa-user-check"></i> Activate</a>
                  <a href="#" class="action-btn delete-btn"><i class="fas fa-trash"></i> Delete</a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        
        <div class="table-footer">
          <div>Showing 1 to 5 of 42 entries</div>
          <div class="pagination">
            <button class="page-btn disabled">
              <i class="fas fa-chevron-left"></i> Previous
            </button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn">4</button>
            <button class="page-btn">5</button>
            <button class="page-btn">
              Next <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
      
      <div class="two-column-layout">
        <!-- Left Column -->
        <div>
          <!-- User Statistics -->
          <div class="stats-card">
            <h3>User Statistics</h3>
            
            <div class="stat-item">
              <div class="stat-label">Total Users</div>
              <div class="stat-value">42</div>
            </div>
            
            <div class="stat-item">
              <div class="stat-label">Active Users</div>
              <div class="stat-value">36</div>
            </div>
            
            <div class="stat-item">
              <div class="stat-label">On Leave</div>
              <div class="stat-value">3</div>
            </div>
            
            <div class="stat-item">
              <div class="stat-label">Inactive Users</div>
              <div class="stat-value">3</div>
            </div>
          </div>
          
          <!-- Roles Distribution -->
          <div class="roles-card">
            <h3>User Roles Distribution</h3>
            
            <div class="role-item">
              <div class="role-name">
                <span>Administrators</span>
                <span>15%</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 15%;"></div>
              </div>
            </div>
            
            <div class="role-item">
              <div class="role-name">
                <span>Managers</span>
                <span>25%</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 25%;"></div>
              </div>
            </div>
            
            <div class="role-item">
              <div class="role-name">
                <span>Staff</span>
                <span>60%</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 60%;"></div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Right Column -->
        <div>
          <!-- Recent Activities -->
          <div class="activities-card">
            <h3>Record Activities</h3>
            
            <div class="activity-item">
              <p><strong>Michael Thompson added a new user Emily Parker</strong></p>
              <p class="activity-meta">Today, 09:42 AM</p>
            </div>
            
            <div class="activity-item">
              <p><strong>Sarah Martinez deactivated user Thomas Wright</strong></p>
              <p class="activity-meta">Yesterday, 16:15 PM</p>
            </div>
            
            <div class="activity-item">
              <p><strong>Jennifer Lee updated role for David Wilson</strong></p>
              <p class="activity-meta">Oct 10, 2023, 14:23 PM</p>
            </div>
            
            <div class="activity-item">
              <p><strong>Robert Johnson set to on leave status</strong></p>
              <p class="activity-meta">Oct 08, 2023, 09:15 AM</p>
            </div>
            
            <div class="view-all">
              <a href="#">View All Activities</a>
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
        if (path.endsWith('admin_manage_users.php')) {
          a.classList.add('active');
        }
      });
    });
  </script>
</body>
</html>