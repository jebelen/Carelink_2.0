<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="logo">
        <div class="logo-image">
            <img src="../images/LOGO.jpg" alt="Barangay <?php echo $barangayName; ?> Logo" class="logo-image" onerror="this.style.display='none'; document.getElementById('fallback-logo').style.display='flex';">
        </div>
        <h1>CARELINK</h1>
    </div>
    <ul class="nav-links">
        <li class="<?php echo ($current_page == 'barangay_dash.php') ? 'active' : ''; ?>"><a href="barangay_dash.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li class="<?php echo ($current_page == 'submit_application.php') ? 'active' : ''; ?>"><a href="submit_application.php"><i class="fas fa-user-plus"></i> Submit Application</a></li>
        <li class="<?php echo ($current_page == 'barangay_records.php') ? 'active' : ''; ?>"><a href="barangay_records.php"><i class="fas fa-database"></i> Records</a></li>
        <li class="<?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>"><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
        <li class="logout-item"><a href="../index.php?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
