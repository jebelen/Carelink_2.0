<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="../images/LOGO.jpg" alt="Logo" class="logo-image">
            <h1 class="logo-text">CARELINK</h1>
        </div>
    </div>
    <div class="sidebar-menu">
        <ul>
            <li class="<?php echo ($current_page == 'department_dashboard.php') ? 'active' : ''; ?>"><a href="department_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="<?php echo ($current_page == 'user_management.php' || $current_page == 'edit_user.php' || $current_page == 'signup.php') ? 'active' : ''; ?>"><a href="user_management.php"><i class="fas fa-user-cog"></i> User Management</a></li>
            <li class="<?php echo ($current_page == 'department_records.php') ? 'active' : ''; ?>"><a href="department_records.php"><i class="fas fa-database"></i> Records</a></li>
            <li class="<?php echo ($current_page == 'verify_document.php') ? 'active' : ''; ?>"><a href="verify_document.php"><i class="fas fa-check-circle"></i> Verify Documents</a></li>
            <li class="<?php echo ($current_page == 'system_settings.php') ? 'active' : ''; ?>"><a href="system_settings.php"><i class="fas fa-cog"></i> System Settings</a></li>
            <li class="logout-item"><a href="../index.php?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</div>
