<?php
// admin_manage_inventory.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Janiking - Manage Inventory</title>
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

    /* Inventory Table */
    .inventory-controls {
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

    .filter-controls {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .filter-controls select {
      padding: 8px 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      background: white;
    }

    .inventory-table {
      width: 100%;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }

    .inventory-table table {
      width: 100%;
      border-collapse: collapse;
    }

    .inventory-table th {
      background-color: var(--brand-blue);
      color: white;
      text-align: left;
      padding: 15px;
    }

    .inventory-table td {
      padding: 15px;
      border-bottom: 1px solid #f1f1f1;
    }

    .inventory-table tr:last-child td {
      border-bottom: none;
    }

    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }

    .status-in-stock {
      background: #e8f5e9;
      color: #2e7d32;
    }

    .status-low-stock {
      background: #fff3e0;
      color: #ef6c00;
    }

    .status-out-of-stock {
      background: #ffebee;
      color: #c62828;
    }

    .action-btn {
      padding: 5px 10px;
      border-radius: 5px;
      font-size: 13px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
    }

    .edit-btn {
      background: #e3f2fd;
      color: #1565c0;
      border: 1px solid #bbdefb;
    }

    .remove-btn {
      background: #ffebee;
      color: #c62828;
      border: 1px solid #ffcdd2;
      margin-left: 5px;
    }

    .table-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px;
      background: #f8f9fa;
      border-top: 1px solid #ddd;
    }

    /* Dashboard Cards */
    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .dashboard-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .dashboard-card h3 {
      font-size: 16px;
      margin-bottom: 15px;
      color: var(--brand-blue);
    }

    .stat-value {
      font-size: 28px;
      font-weight: 700;
      color: var(--brand-blue);
      margin-bottom: 10px;
    }

    .stat-label {
      color: #6c757d;
      font-size: 14px;
    }

    /* Category Distribution */
    .category-distribution {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }

    .category-distribution h3 {
      font-size: 16px;
      margin-bottom: 15px;
      color: var(--brand-blue);
      display: flex;
      justify-content: space-between;
    }

    .category-item {
      margin-bottom: 15px;
    }

    .category-name {
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
    .recent-activities {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .recent-activities h3 {
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

    /* Responsive adjustments */
    @media (max-width: 992px){
      :root{ --nav-w: 80px; }
      .main-content{ padding:20px 15px; }
      .search-box input { width: 150px; }
    }
    
    @media (max-width: 768px){
      .inventory-controls {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .filter-controls {
        width: 100%;
        justify-content: space-between;
      }
      
      .filter-controls select {
        flex: 1;
        margin-bottom: 10px;
      }
    }
    
    @media (max-width: 576px){
      .main-content{ margin-left:0; }
      .inventory-table {
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
      <h1 class="page-title">Manage Inventory</h1>

      <!-- Inventory Table -->
      <div class="inventory-controls">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Search Inventory...">
        </div>
        
        <div class="filter-controls">
          <select>
            <option>All Categories</option>
            <option>Cleaning Supplies</option>
            <option>Equipment</option>
            <option>Safety Gear</option>
            <option>Chemicals</option>
            <option>Tools</option>
          </select>
          
          <select>
            <option>All Locations</option>
            <option>Warehouse A</option>
            <option>Warehouse B</option>
            <option>Store Room C</option>
          </select>
          
          <select>
            <option>Sort By: Name</option>
            <option>Sort By: Stock</option>
            <option>Sort By: Price</option>
          </select>
          
          <select>
            <option>Show: 10</option>
            <option>Show: 25</option>
            <option>Show: 50</option>
            <option>Show: 100</option>
          </select>
        </div>
      </div>
      
      <div class="inventory-table">
        <table>
          <thead>
            <tr>
              <th>Item ID</th>
              <th>Product Name</th>
              <th>Category</th>
              <th>Stock</th>
              <th>Unit Price</th>
              <th>Location</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#INV-1001</td>
              <td>Premium Floor Cleaner</td>
              <td>Cleaning Supplies</td>
              <td>128</td>
              <td>$24.99</td>
              <td>Warehouse A</td>
              <td><span class="status-badge status-in-stock">in stock</span></td>
              <td>
                <a href="#" class="action-btn edit-btn">Edit</a>
                <a href="#" class="action-btn remove-btn">Remove</a>
              </td>
            </tr>
            <tr>
              <td>#INV-1002</td>
              <td>Industrial Vacuum Cleaner</td>
              <td>Equipment</td>
              <td>15</td>
              <td>$349.99</td>
              <td>Warehouse B</td>
              <td><span class="status-badge status-in-stock">in stock</span></td>
              <td>
                <a href="#" class="action-btn edit-btn">Edit</a>
                <a href="#" class="action-btn remove-btn">Remove</a>
              </td>
            </tr>
            <tr>
              <td>#INV-1003</td>
              <td>Disposable Latex Gloves</td>
              <td>Safety Gear</td>
              <td>42</td>
              <td>$12.50</td>
              <td>Store Room C</td>
              <td><span class="status-badge status-low-stock">Low stock</span></td>
              <td>
                <a href="#" class="action-btn edit-btn">Edit</a>
                <a href="#" class="action-btn remove-btn">Remove</a>
              </td>
            </tr>
            <tr>
              <td>#INV-1004</td>
              <td>Glass Cleaner Solution</td>
              <td>Cleaning Supplies</td>
              <td>89</td>
              <td>$8.75</td>
              <td>Warehouse A</td>
              <td><span class="status-badge status-in-stock">in stock</span></td>
              <td>
                <a href="#" class="action-btn edit-btn">Edit</a>
                <a href="#" class="action-btn remove-btn">Remove</a>
              </td>
            </tr>
            <tr>
              <td>#INV-1005</td>
              <td>Carpet Shampooer</td>
              <td>Equipment</td>
              <td>0</td>
              <td>$499.99</td>
              <td>Warehouse B</td>
              <td><span class="status-badge status-out-of-stock">Out of Stock</span></td>
              <td>
                <a href="#" class="action-btn edit-btn">Edit</a>
                <a href="#" class="action-btn remove-btn">Remove</a>
              </td>
            </tr>
          </tbody>
        </table>
        
        <div class="table-footer">
          <div>Showing 1 to 5 of 42 entries</div>
          <div>
            <!-- Pagination would go here -->
          </div>
        </div>
      </div>
      
      <!-- Dashboard Cards -->
      <div class="dashboard-cards">
        <div class="dashboard-card">
          <h3>Total Items</h3>
          <div class="stat-value">1,248</div>
          <div class="stat-label">All inventory items</div>
        </div>
        
        <div class="dashboard-card">
          <h3>Low Stock</h3>
          <div class="stat-value">24</div>
          <div class="stat-label">Items needing restock</div>
        </div>
        
        <div class="dashboard-card">
          <h3>Out of Stock</h3>
          <div class="stat-value">8</div>
          <div class="stat-label">Items unavailable</div>
        </div>
        
        <div class="dashboard-card">
          <h3>Total Value</h3>
          <div class="stat-value">$124,582</div>
          <div class="stat-label">Inventory worth</div>
        </div>
      </div>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Category Distribution -->
        <div class="category-distribution">
          <h3>
            Category Distribution
            <span style="font-size: 14px; font-weight: normal;">Last 30 Days</span>
          </h3>
          
          <div class="category-item">
            <div class="category-name">
              <span>Cleaning Supplies</span>
              <span>42%</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 42%;"></div>
            </div>
          </div>
          
          <div class="category-item">
            <div class="category-name">
              <span>Equipment</span>
              <span>28%</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 28%;"></div>
            </div>
          </div>
          
          <div class="category-item">
            <div class="category-name">
              <span>Chemicals</span>
              <span>15%</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 15%;"></div>
            </div>
          </div>
          
          <div class="category-item">
            <div class="category-name">
              <span>Safety Gear</span>
              <span>10%</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 10%;"></div>
            </div>
          </div>
          
          <div class="category-item">
            <div class="category-name">
              <span>Tools</span>
              <span>5%</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 5%;"></div>
            </div>
          </div>
        </div>
        
        <!-- Recent Activities -->
        <div class="recent-activities">
          <h3>Recent Activities</h3>
          
          <div class="activity-item">
            <p><strong>Added 50 units of Premium Floor Cleaner</strong></p>
            <p class="activity-meta">By Mark Wilson - Today, 10:23 AM</p>
          </div>
          
          <div class="activity-item">
            <p><strong>Updated price of Industrial Vacuum Cleaner</strong></p>
            <p class="activity-meta">By Sarah Johnson - Yesterday, 3:45 PM</p>
          </div>
          
          <div class="activity-item">
            <p><strong>Removed 5 expired Disinfectant Sprays</strong></p>
            <p class="activity-meta">By Robert Davis - Yesterday, 11:30 AM</p>
          </div>
          
          <div class="activity-item">
            <p><strong>Marked Carpet Shampooer as Out of Stock</strong></p>
            <p class="activity-meta">By Jennifer Lee - 2 days ago</p>
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
        if (path.endsWith('admin_manage_inventory.php')) {
          a.classList.add('active');
        }
      });
    });
  </script>
</body>
</html>