<?php
// admin_reports.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Janiking - Reports</title>
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

    /* Generate Report Section */
    .generate-section {
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

    .checkbox-group {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }

    .checkbox-group input {
      margin-right: 10px;
    }

    .date-range {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-bottom: 20px;
    }

    .btn {
      padding: 10px 20px;
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
      margin-right: 10px;
    }

    .btn-outline:hover {
      background: #f8f9fa;
    }

    /* Reports Table */
    .reports-table {
      width: 100%;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .reports-table table {
      width: 100%;
      border-collapse: collapse;
    }

    .reports-table th {
      background-color: var(--brand-blue);
      color: white;
      text-align: left;
      padding: 15px;
    }

    .reports-table td {
      padding: 15px;
      border-bottom: 1px solid #f1f1f1;
    }

    .reports-table tr:last-child td {
      border-bottom: none;
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

    .download-btn {
      background: #e8f5e9;
      color: #2e7d32;
      border-color: #c8e6c9;
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

    /* Responsive adjustments */
    @media (max-width: 992px){
      :root{ --nav-w: 80px; }
      .main-content{ padding:20px 15px; }
    }
    
    @media (max-width: 768px){
      .date-range {
        grid-template-columns: 1fr;
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
      .reports-table {
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
      <h1 class="page-title">Reports</h1>
      <p class="page-subtitle">Generate and manage reports for your business</p>

      <!-- Generate New Report Section -->
      <div class="generate-section">
        <h2 class="section-title">Generate New Report</h2>
        
        <div class="form-group">
          <label for="report-type">Report Type</label>
          <select class="form-control" id="report-type">
            <option>Select Report Type</option>
            <option>Sales Report</option>
            <option>Inventory Report</option>
            <option>User Activity Report</option>
            <option>Appointments Report</option>
            <option>Financial Report</option>
            <option>Performance Report</option>
          </select>
        </div>
        
        <div class="checkbox-group">
          <input type="checkbox" id="include-charts">
          <label for="include-charts">Include Charts</label>
        </div>
        
        <div class="checkbox-group">
          <input type="checkbox" id="include-summary">
          <label for="include-summary">Include Executive Summary</label>
        </div>
        
        <div class="date-range">
          <div class="form-group">
            <label for="start-date">Start Date</label>
            <input type="text" class="form-control" id="start-date" placeholder="MM/DD/YYYY">
          </div>
          
          <div class="form-group">
            <label for="end-date">End Date</label>
            <input type="text" class="form-control" id="end-date" placeholder="MM/DD/YYYY">
          </div>
        </div>
        
        <div class="checkbox-group">
          <input type="checkbox" id="export-pdf">
          <label for="export-pdf">Export as PDF</label>
        </div>
        
        <button class="btn btn-primary">
          <i class="fas fa-file-alt"></i> Generate Report
        </button>
      </div>

      <!-- Generated Reports Table -->
      <div class="reports-table">
        <h2 class="section-title">Generated Reports</h2>
        
        <table>
          <thead>
            <tr>
              <th>Report Name</th>
              <th>Type</th>
              <th>Date Range</th>
              <th>Generated On</th>
              <th>Generated By</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Q4 Sales Summary</td>
              <td>Sales Report</td>
              <td>10/01/2023 - 12/31/2023</td>
              <td>01/05/2024</td>
              <td>Michael Rodriguez</td>
              <td>
                <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i></a>
                <a href="#" class="action-btn download-btn"><i class="fas fa-download"></i></a>
              </td>
            </tr>
            <tr>
              <td>Monthly Inventory Status</td>
              <td>Inventory Report</td>
              <td>12/01/2023 - 12/31/2023</td>
              <td>01/03/2024</td>
              <td>Sarah Johnson</td>
              <td>
                <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i></a>
                <a href="#" class="action-btn download-btn"><i class="fas fa-download"></i></a>
              </td>
            </tr>
            <tr>
              <td>Annual User Activity</td>
              <td>User Activity Report</td>
              <td>01/01/2023 - 12/31/2023</td>
              <td>01/02/2024</td>
              <td>Jennifer Williams</td>
              <td>
                <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i></a>
                <a href="#" class="action-btn download-btn"><i class="fas fa-download"></i></a>
              </td>
            </tr>
            <tr>
              <td>Appointment Analytics</td>
              <td>Appointments Report</td>
              <td>11/01/2023 - 12/31/2023</td>
              <td>01/01/2024</td>
              <td>Robert Thompson</td>
              <td>
                <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i></a>
                <a href="#" class="action-btn download-btn"><i class="fas fa-download"></i></a>
              </td>
            </tr>
            <tr>
              <td>Financial Performance Q3</td>
              <td>Financial Report</td>
              <td>07/01/2023 - 09/30/2023</td>
              <td>10/15/2023</td>
              <td>Amanda Garcia</td>
              <td>
                <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i></a>
                <a href="#" class="action-btn download-btn"><i class="fas fa-download"></i></a>
              </td>
            </tr>
          </tbody>
        </table>
        
        <div class="table-footer">
          <div>Showing 5 of 24 reports</div>
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
        if (path.endsWith('admin_reports.php')) {
          a.classList.add('active');
        }
      });

      // Date picker initialization (would need a proper date picker library)
      const startDateInput = document.getElementById('start-date');
      const endDateInput = document.getElementById('end-date');
      
      if (startDateInput && endDateInput) {
        // This is where you would initialize a date picker
        // For example: new Datepicker(startDateInput, { format: 'mm/dd/yyyy' });
        console.log('Date pickers would be initialized here');
      }
    });
  </script>
</body>
</html>