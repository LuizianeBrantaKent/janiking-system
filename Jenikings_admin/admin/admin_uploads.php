<?php
// admin_uploads.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Janiking - Upload Files</title>
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

    /* Tabs */
    .tabs {
      display: flex;
      margin-bottom: 25px;
      border-bottom: 1px solid #ddd;
    }

    .tab {
      padding: 12px 20px;
      cursor: pointer;
      font-weight: 600;
      color: #6c757d;
      border-bottom: 3px solid transparent;
      transition: all 0.3s;
    }

    .tab.active {
      color: var(--brand-blue);
      border-bottom: 3px solid var(--brand-blue);
    }

    .tab:hover {
      color: var(--brand-blue);
    }

    /* Two-column layout */
    .two-column-layout {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
      margin-bottom: 30px;
    }

    /* Upload Sections */
    .upload-section {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .section-title {
      color: var(--brand-blue);
      font-size: 20px;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
      display: flex;
      align-items: center;
      gap: 10px;
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

    /* File Upload */
    .file-upload {
      border: 2px dashed #ced4da;
      border-radius: 8px;
      padding: 30px;
      text-align: center;
      margin-bottom: 20px;
      cursor: pointer;
      transition: border-color 0.3s;
    }

    .file-upload:hover {
      border-color: var(--brand-blue);
    }

    .file-upload i {
      font-size: 36px;
      color: var(--brand-blue);
      margin-bottom: 15px;
    }

    .file-upload p {
      margin-bottom: 15px;
      color: #6c757d;
    }

    .browse-btn {
      color: var(--brand-blue);
      font-weight: 600;
      text-decoration: underline;
      cursor: pointer;
    }

    .file-note {
      font-size: 13px;
      color: #6c757d;
      text-align: center;
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
      width: 100%;
      justify-content: center;
    }

    .btn-primary:hover {
      background: #003b73;
    }

    /* Uploads Table */
    .uploads-table {
      width: 100%;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }

    .uploads-table table {
      width: 100%;
      border-collapse: collapse;
    }

    .uploads-table th {
      background-color: var(--brand-blue);
      color: white;
      text-align: left;
      padding: 15px;
    }

    .uploads-table td {
      padding: 15px;
      border-bottom: 1px solid #f1f1f1;
    }

    .uploads-table tr:last-child td {
      border-bottom: none;
    }

    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }

    .status-complete {
      background: #e8f5e9;
      color: #2e7d32;
    }

    .status-processing {
      background: #fff3e0;
      color: #ef6c00;
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
      margin-left: 5px;
    }

    .delete-btn {
      background: #ffebee;
      color: #c62828;
      border-color: #ffcdd2;
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

    /* Current Upload */
    .current-upload {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .upload-progress {
      margin-bottom: 15px;
    }

    .progress-bar {
      height: 10px;
      background: #e9ecef;
      border-radius: 5px;
      overflow: hidden;
      margin-bottom: 10px;
    }

    .progress-fill {
      height: 100%;
      background: var(--brand-blue);
      width: 38%;
    }

    .progress-info {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      color: #6c757d;
    }

    .upload-file-name {
      font-weight: 600;
      margin-bottom: 5px;
    }

    .upload-estimate {
      font-size: 13px;
      color: #6c757d;
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
      .uploads-table {
        overflow-x: auto;
        display: block;
      }
      
      .action-btn {
        margin-bottom: 5px;
      }
    }
    
    @media (max-width: 576px){
      .main-content{ margin-left:0; }
      .tabs {
        flex-wrap: wrap;
      }
      
      .tab {
        flex: 1;
        text-align: center;
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
      <h1 class="page-title">Upload Files</h1>
      <p class="page-subtitle">Upload training materials and documents for your organization</p>

      <!-- Tabs -->
      <div class="tabs">
        <div class="tab active">Training Files</div>
        <div class="tab">Documents</div>
      </div>

      <!-- Upload Forms -->
      <div class="two-column-layout">
        <!-- Training File Upload -->
        <div class="upload-section">
          <h2 class="section-title">
            <i class="fas fa-file-video"></i> Upload Training File
          </h2>
          
          <div class="form-group">
            <label for="training-title">Title</label>
            <input type="text" class="form-control" id="training-title" placeholder="Enter file title">
          </div>
          
          <div class="form-group">
            <label for="training-category">Category</label>
            <select class="form-control" id="training-category">
              <option>Safety Training</option>
              <option>Customer Service</option>
              <option>Technical Skills</option>
              <option>Compliance</option>
              <option>Software Training</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="training-version">Version</label>
            <input type="text" class="form-control" id="training-version" placeholder="1.0" value="1.0">
          </div>
          
          <div class="file-upload">
            <i class="fas fa-cloud-upload-alt"></i>
            <p>Drag and drop files here or <span class="browse-btn">Browse Files</span></p>
            <div class="file-note">Supported formats: MP4, MOV, AVI (Max size: 500MB)</div>
          </div>
          
          <button class="btn btn-primary">
            <i class="fas fa-upload"></i> Upload File
          </button>
        </div>
        
        <!-- Document Upload -->
        <div class="upload-section">
          <h2 class="section-title">
            <i class="fas fa-file-alt"></i> Upload Document
          </h2>
          
          <div class="form-group">
            <label for="document-title">Title</label>
            <input type="text" class="form-control" id="document-title" placeholder="Enter document title">
          </div>
          
          <div class="form-group">
            <label for="document-category">Category</label>
            <select class="form-control" id="document-category">
              <option>Policies</option>
              <option>Manuals</option>
              <option>Procedures</option>
              <option>Forms</option>
              <option>Guidelines</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="document-version">Version</label>
            <input type="text" class="form-control" id="document-version" placeholder="1.0" value="1.0">
          </div>
          
          <div class="file-upload">
            <i class="fas fa-cloud-upload-alt"></i>
            <p>Drag and drop files here or <span class="browse-btn">Browse Files</span></p>
            <div class="file-note">Supported formats: PDF, DOCX, XLSX (Max size: 50MB)</div>
          </div>
          
          <button class="btn btn-primary">
            <i class="fas fa-upload"></i> Upload Document
          </button>
        </div>
      </div>

      <!-- Recent Uploads Table -->
      <div class="uploads-table">
        <h2 class="section-title">Recent Uploads</h2>
        
        <table>
          <thead>
            <tr>
              <th>File Name</th>
              <th>Type</th>
              <th>Category</th>
              <th>Version</th>
              <th>Upload Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Safety Protocols 2023</td>
              <td>Training</td>
              <td>Safety Training</td>
              <td>2.1</td>
              <td>Oct 15, 2023</td>
              <td><span class="status-badge status-complete">Complete</span></td>
              <td>
                <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i> View</a>
                <a href="#" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                <a href="#" class="action-btn delete-btn"><i class="fas fa-trash"></i> Delete</a>
              </td>
            </tr>
            <tr>
              <td>Equipment Manual - Floor Scrubber</td>
              <td>Document</td>
              <td>Manuals</td>
              <td>1.0</td>
              <td>Oct 12, 2023</td>
              <td><span class="status-badge status-complete">Complete</span></td>
              <td>
                <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i> View</a>
                <a href="#" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                <a href="#" class="action-btn delete-btn"><i class="fas fa-trash"></i> Delete</a>
              </td>
            </tr>
            <tr>
              <td>Customer Interaction Guidelines</td>
              <td>Training</td>
              <td>Customer Service</td>
              <td>1.5</td>
              <td>Oct 10, 2023</td>
              <td><span class="status-badge status-complete">Complete</span></td>
              <td>
                <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i> View</a>
                <a href="#" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                <a href="#" class="action-btn delete-btn"><i class="fas fa-trash"></i> Delete</a>
              </td>
            </tr>
            <tr>
              <td>New Employee Handbook</td>
              <td>Document</td>
              <td>Policies</td>
              <td>3.2</td>
              <td>Oct 8, 2023</td>
              <td><span class="status-badge status-complete">Complete</span></td>
              <td>
                <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i> View</a>
                <a href="#" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                <a href="#" class="action-btn delete-btn"><i class="fas fa-trash"></i> Delete</a>
              </td>
            </tr>
            <tr>
              <td>Chemical Safety Training</td>
              <td>Training</td>
              <td>Safety Training</td>
              <td>1.1</td>
              <td>Oct 5, 2023</td>
              <td><span class="status-badge status-processing">Processing</span></td>
              <td>
                <a href="#" class="action-btn view-btn"><i class="fas fa-eye"></i> View</a>
                <a href="#" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                <a href="#" class="action-btn delete-btn"><i class="fas fa-trash"></i> Delete</a>
              </td>
            </tr>
          </tbody>
        </table>
        
        <div class="table-footer">
          <div>Showing 5 of 24 uploads</div>
          <div>
            <!-- Pagination would go here -->
          </div>
        </div>
      </div>

      <!-- Current Upload Progress -->
      <div class="current-upload">
        <h2 class="section-title">Current Upload</h2>
        
        <div class="upload-file-name">Uploading: Quarterly Compliance Update.mp4 (45MB / 120MB)</div>
        
        <div class="upload-progress">
          <div class="progress-bar">
            <div class="progress-fill"></div>
          </div>
          <div class="progress-info">
            <span>38% Complete</span>
            <span>Estimated time: 2 minutes</span>
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
        if (path.endsWith('admin_uploads.php')) {
          a.classList.add('active');
        }
      });

      // Tab switching functionality
      const tabs = document.querySelectorAll('.tab');
      tabs.forEach(tab => {
        tab.addEventListener('click', () => {
          tabs.forEach(t => t.classList.remove('active'));
          tab.classList.add('active');
        });
      });

      // File upload drag and drop functionality
      const fileUploads = document.querySelectorAll('.file-upload');
      fileUploads.forEach(upload => {
        upload.addEventListener('dragover', (e) => {
          e.preventDefault();
          upload.style.borderColor = 'var(--brand-blue)';
          upload.style.backgroundColor = '#f8f9fa';
        });

        upload.addEventListener('dragleave', () => {
          upload.style.borderColor = '#ced4da';
          upload.style.backgroundColor = 'transparent';
        });

        upload.addEventListener('drop', (e) => {
          e.preventDefault();
          upload.style.borderColor = '#ced4da';
          upload.style.backgroundColor = 'transparent';
          // Handle file upload here
          console.log('File dropped');
        });
      });
    });
  </script>
</body>
</html>