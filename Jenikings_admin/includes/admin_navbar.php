<div class="admin-navbar">
    <ul class="nav-menu">
        <li class="nav-item">
            <a href="../admin/admin_dash.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='admin_dash.php') echo 'active'; ?>">
                <i class="fas fa-th-large"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="../admin/admin_manage_appointments.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='admin_manage_appointments.php') echo 'active'; ?>">
                <i class="fas fa-calendar-check"></i>
                <span class="nav-text">Manage Appointments</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="../admin/admin_manage_inventory.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='admin_manage_inventory.php') echo 'active'; ?>">
                <i class="fas fa-boxes"></i>
                <span class="nav-text">Manage Inventory</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="../admin/admin_manage_users.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='admin_manage_users.php') echo 'active'; ?>">
                <i class="fas fa-users"></i>
                <span class="nav-text">Manage Users</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="../admin/admin_announcements.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='admin_announcements.php') echo 'active'; ?>">
                <i class="fas fa-bullhorn"></i>
                <span class="nav-text">Communication</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="../admin/admin_reports.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='admin_reports.php') echo 'active'; ?>">
                <i class="fas fa-chart-bar"></i>
                <span class="nav-text">Reports</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="../admin/admin_uploads.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='admin_uploads.php') echo 'active'; ?>">
                <i class="fas fa-file-upload"></i>
                <span class="nav-text">Upload Training & Documents</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="../admin/admin_profile.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='admin_profile.php') echo 'active'; ?>">
                <i class="fas fa-user-cog"></i>
                <span class="nav-text">Profile / Settings</span>
            </a>
        </li>
    </ul>

    <div class="logout-section">
        <a href="#" class="nav-link">
            <i class="fas fa-sign-out-alt"></i>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>

<style>
/* Navbar Styles */
.admin-navbar {
    background-color: #fff;
    width: 200px;
    height: calc(100vh - 75px); /* still below header */
    padding: 20px 0;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    position: fixed;
    left: 0;
    top: 75px; /* match header height */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    z-index: 900;
}


.nav-menu { list-style: none; padding: 0 15px; }
.nav-item { margin-bottom: 5px; }
.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    text-decoration: none;
    color: #000;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.nav-link.active, .nav-link:hover { background-color: #d1d6dc; }
.nav-link i { color: #004990; width: 25px; font-size: 18px; margin-right: 12px; }
.nav-text { font-size: 15px; font-weight: 500; }

.logout-section { padding: 15px 20px 0; border-top: 1px solid #d1d6dc; }

/* Main Content Offset */
.main-content {
    margin-left: 200px; /* width of navbar */
    padding: 30px;
}

/* Responsive */
@media(max-width:992px){
    .admin-navbar { width: 80px; }
    .main-content { margin-left: 80px; }
    .nav-text { display: none; }
}
@media(max-width:576px){
    .admin-navbar {
        width: 100%;
        height: auto;
        position: relative;
        flex-direction: row;
        justify-content: flex-start;
    }
    .main-content { margin-left: 0; padding: 20px 15px; }
    .nav-text { display: block; font-size: 12px; }
    .logout-section { display: none; }
}
</style>

<script>
// Active link highlighting
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>
