<?php
// admin_manage_appointments.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Janiking - Manage Appointments</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root{
      --header-h: 80px;
      --nav-w: 220px;
      --brand-blue: #004990;
    }
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;}
    body{background:#f8f9fa;color:#333;height:100vh;overflow:hidden;}

    .main-container{display:flex;height:100vh;padding-top:var(--header-h);}
    .admin-navbar{
      position:fixed;top:var(--header-h);left:0;
      width:var(--nav-w);height:calc(100vh - var(--header-h));
      overflow-y:auto;background:#fff;border-right:1px solid #ddd;
    }
    .main-content{
      flex:1;padding:30px;overflow-y:auto;
      background:#e9ecef;
    }

    .page-header{
      display:flex;justify-content:space-between;align-items:center;
      margin-bottom:20px;
    }
    .page-header h1{font-size:24px;color:var(--brand-blue);}
    .btn{
      background:var(--brand-blue);color:#fff;padding:8px 14px;
      border:none;border-radius:6px;cursor:pointer;
      font-size:14px;transition:.3s;
    }
    .btn:hover{opacity:.9;}
    .btn-secondary{background:#6c757d;}

    /* Filters */
    .filter-bar{
      display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;
      background:#fff;padding:15px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.05);
    }
    .filter-bar input,.filter-bar select{
      padding:8px;border:1px solid #ccc;border-radius:6px;font-size:14px;
    }

    /* Table */
    .table-container{
      background:#fff;border-radius:10px;overflow:hidden;
      box-shadow:0 2px 8px rgba(0,0,0,0.05);margin-bottom:25px;
    }
    table{width:100%;border-collapse:collapse;}
    th,td{padding:14px;border-bottom:1px solid #eee;text-align:left;font-size:14px;}
    th{background:#f8f9fa;font-weight:600;}
    td .status{font-weight:600;padding:4px 10px;border-radius:12px;font-size:12px;}
    .status.confirmed{background:#e8f5e9;color:#2e7d32;}
    .status.pending{background:#fff3e0;color:#ef6c00;}
    .status.cancelled{background:#ffebee;color:#c62828;}
    .status.completed{background:#e3f2fd;color:#1565c0;}
    .actions button{
      margin-right:5px;padding:5px 10px;font-size:12px;border:none;
      border-radius:6px;cursor:pointer;
    }
    .actions .change{background:#17a2b8;color:#fff;}
    .actions .reschedule{background:#ffc107;color:#fff;}
    .actions .cancel{background:#dc3545;color:#fff;}

    /* Pagination */
    .pagination{display:flex;gap:5px;justify-content:flex-end;padding:10px;}
    .pagination button{
      border:1px solid #ddd;background:#fff;padding:6px 10px;cursor:pointer;border-radius:4px;
    }
    .pagination button.active{background:var(--brand-blue);color:#fff;}

    /* Stats + Upcoming + Recent */
    .dashboard-widgets{
      display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
      gap:20px;margin-top:20px;
    }
    .widget{
      background:#fff;padding:20px;border-radius:10px;
      box-shadow:0 2px 8px rgba(0,0,0,0.05);
    }
    .widget h3{font-size:16px;margin-bottom:15px;color:var(--brand-blue);}
    .pie-chart-placeholder{
      height:180px;background:#f8f9fa;border-radius:8px;
      display:flex;align-items:center;justify-content:center;color:#6c757d;
    }
    .list-item{margin-bottom:12px;font-size:14px;}
    .list-item strong{color:var(--brand-blue);}
  </style>
</head>
<body>
  <?php
    include('../includes/header.php');
    include('../includes/admin_navbar.php');
  ?>

  <div class="main-container">
    <main class="main-content">
      <!-- Header -->
      <div class="page-header">
        <h1>Manage Appointments</h1>
        <div>
          <button class="btn">New Appointment</button>
          <button class="btn btn-secondary">Export</button>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="filter-bar">
        <input type="text" placeholder="Search appointments...">
        <select>
          <option>Status</option>
          <option>Confirmed</option>
          <option>Pending</option>
          <option>Cancelled</option>
          <option>Completed</option>
        </select>
        <input type="date">
        <input type="date">
        <button class="btn">Apply Filters</button>
      </div>

      <!-- Appointment Table -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Appointment ID</th>
              <th>Name</th>
              <th>Date</th>
              <th>Address</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#APT-1001</td>
              <td>Sarah Johnson<br><small>sj@example.com</small></td>
              <td>Oct 15, 2023<br>09:00 AM - 11:30 AM</td>
              <td>Downtown Office<br>123 Business Ave</td>
              <td><span class="status confirmed">Confirmed</span></td>
              <td class="actions">
                <button class="change">Change status</button>
                <button class="reschedule">Reschedule</button>
                <button class="cancel">Cancel</button>
              </td>
            </tr>
            <tr>
              <td>#APT-1002</td>
              <td>Michael Chen<br><small>m.chen@example.com</small></td>
              <td>Oct 16, 2023<br>01:00 PM - 03:00 PM</td>
              <td>Chen Enterprises<br>455 Market St</td>
              <td><span class="status pending">Pending</span></td>
              <td class="actions">
                <button class="change">Change status</button>
                <button class="reschedule">Reschedule</button>
                <button class="cancel">Cancel</button>
              </td>
            </tr>
            <tr>
              <td>#APT-1003</td>
              <td>Emily Rodriguez<br><small>e.rodriguez@example.com</small></td>
              <td>Oct 17, 2023<br>10:00 AM - 12:00 PM</td>
              <td>Rodriguez Law Firm<br>789 Legal Blvd</td>
              <td><span class="status cancelled">Cancelled</span></td>
              <td class="actions">
                <button class="change">Change status</button>
                <button class="reschedule">Reschedule</button>
                <button class="cancel">Cancel</button>
              </td>
            </tr>
            <tr>
              <td>#APT-1004</td>
              <td>Robert Williams<br><small>r.williams@example.com</small></td>
              <td>Oct 18, 2023<br>08:00 AM - 02:00 PM</td>
              <td>Williams & Co<br>101 Finance St</td>
              <td><span class="status confirmed">Confirmed</span></td>
              <td class="actions">
                <button class="change">Change status</button>
                <button class="reschedule">Reschedule</button>
                <button class="cancel">Cancel</button>
              </td>
            </tr>
            <tr>
              <td>#APT-1005</td>
              <td>Amanda Thompson<br><small>a.thompson@example.com</small></td>
              <td>Oct 19, 2023<br>03:00 PM - 05:00 PM</td>
              <td>Thompson Retail<br>222 Shopping Ave</td>
              <td><span class="status completed">Completed</span></td>
              <td class="actions">
                <button class="change">Change status</button>
                <button class="reschedule">Reschedule</button>
                <button class="cancel">Cancel</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="pagination">
          <button>1</button>
          <button class="active">2</button>
          <button>3</button>
          <button>4</button>
          <button>5</button>
        </div>
      </div>

      <!-- Widgets -->
      <div class="dashboard-widgets">
        <div class="widget">
          <h3>Appointment Statistics</h3>
          <div class="pie-chart-placeholder">Pie Chart</div>
          <p><span class="status confirmed">Confirmed (24)</span></p>
          <p><span class="status pending">Pending (8)</span></p>
          <p><span class="status cancelled">Cancelled (5)</span></p>
          <p><span class="status completed">Completed (5)</span></p>
        </div>

        <div class="widget">
          <h3>Upcoming Today</h3>
          <div class="list-item"><strong>Office Cleaning</strong> - Sarah Johnson<br>09:00 AM - 11:30 AM, 123 Business Ave</div>
          <div class="list-item"><strong>Carpet Cleaning</strong> - Michael Chen<br>01:00 PM - 03:00 PM, 455 Market St</div>
          <div class="list-item"><strong>Window Cleaning</strong> - Emily Rodriguez<br>03:30 PM - 05:30 PM, 789 Legal Blvd</div>
        </div>

        <div class="widget">
          <h3>Recent Activities</h3>
          <div class="list-item">Appointment <strong>#APT-2023-1458</strong> confirmed <small>10 min ago</small></div>
          <div class="list-item">Appointment <strong>#APT-2023-1459</strong> rescheduled <small>45 min ago</small></div>
          <div class="list-item">Appointment <strong>#APT-2023-1460</strong> cancelled <small>1 hr ago</small></div>
          <div class="list-item">Appointment <strong>#APT-2023-1462</strong> created <small>2 hrs ago</small></div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
