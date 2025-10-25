<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPRAS Dashboard - Barangay Pinagbuhatan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/barangay-sidebar.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }


        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --success: #2ecc71;
            --warning: #f39c12;
            --light: #ecf0f1;
            --dark: #34495e;
            --gray: #95a5a6;
        }


        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            height: 100vh;
            overflow: auto;
        }


        /* layout handled by shared sidebar CSS (assets/css/barangay-sidebar.css) */


        .logo {
            display: flex;
            align-items: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .logo-image {
            height: 45px;
            width: auto;
            margin-right: 12px;
            border-radius: 5px;
            object-fit: contain;
        }
        
        .logo h1 {
            font-size: 18px;
            font-weight: 600;
            color: white;
        }


        .nav-links {
            list-style: none;
        }


        .nav-links li {
            padding: 12px 20px;
            transition: all 0.3s;
        }


        .nav-links li:hover {
            background: rgba(255, 255, 255, 0.1);
        }


        .nav-links li.active {
            background: var(--secondary);
            border-left: 4px solid white;
        }


        .nav-links a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }


        .nav-links i {
            margin-right: 10px;
            font-size: 18px;
        }


        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
        }


        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }


        .header-content {
            display: flex;
            flex-direction: column;
        }


        .welcome-message {
            font-size: 1.2rem;
            color: var(--gray);
            margin-bottom: 5px;
        }


        .header h1 {
            color: var(--primary);
            font-size: 1.8rem;
        }


        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }


        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background: white;
            border-radius: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }


        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }


        .user-details {
            display: flex;
            flex-direction: column;
        }


        .user-details h2 {
            font-size: 14px;
            margin-bottom: 2px;
            color: var(--primary);
        }


        .user-details p {
            color: var(--gray);
            font-size: 12px;
        }


        .btn {
            display: inline-block;
            background: var(--secondary);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }


        .btn:hover {
            background: #2980b9;
        }


        .btn-accent {
            background: var(--accent);
        }


        .btn-accent:hover {
            background: #c0392b;
        }


        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }


        /* Two Panel Layout */
        .dashboard-panels {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }


        .left-panel, .right-panel {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }


        /* Charts Section */
        .charts-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }


        .chart-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }


        .chart-card h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--primary);
            display: flex;
            align-items: center;
        }


        .chart-card h3 i {
            margin-right: 10px;
            color: var(--secondary);
        }


        .chart-wrapper {
            position: relative;
            height: 250px;
            width: 100%;
        }


        /* Notifications Section */
        .notifications-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            height: 100%;
        }


        .notifications-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }


        .notifications-header h3 {
            font-size: 18px;
            color: var(--primary);
            display: flex;
            align-items: center;
        }


        .notifications-header h3 i {
            margin-right: 10px;
            color: var(--secondary);
        }


        .notification-badge {
            background: var(--accent);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }


        .notifications-list {
            max-height: 450px;
            overflow-y: auto;
        }


        .notification-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }


        .notification-item:last-child {
            border-bottom: none;
        }


        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }


        .notification-info {
            flex: 1;
        }


        .notification-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--primary);
        }


        .notification-message {
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 5px;
        }


        .notification-time {
            font-size: 12px;
            color: var(--gray);
        }


        .notification-urgent .notification-icon {
            background: rgba(231, 76, 60, 0.2);
            color: var(--accent);
        }


        .notification-info .notification-title {
            color: var(--accent);
        }


        .notification-warning .notification-icon {
            background: rgba(243, 156, 18, 0.2);
            color: var(--warning);
        }


        .notification-success .notification-icon {
            background: rgba(46, 204, 113, 0.2);
            color: var(--success);
        }


        .notification-info .notification-title {
            color: var(--success);
        }


        .notification-default .notification-icon {
            background: rgba(52, 152, 219, 0.2);
            color: var(--secondary);
        }


        /* Records Section */
        .records-section {
            margin-bottom: 30px;
        }


        .records-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }


        .records-actions {
            display: flex;
            gap: 10px;
        }


        .search-box {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 5px;
            padding: 8px 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }


        .search-box input {
            border: none;
            outline: none;
            padding: 5px;
            width: 200px;
        }


        .records-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }


        .table-header {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr;
            padding: 15px 20px;
            background: var(--primary);
            color: white;
            font-weight: 600;
        }


        .table-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            transition: background 0.3s;
        }


        .table-row:hover {
            background: #f8f9fa;
        }


        .table-row:last-child {
            border-bottom: none;
        }


        .table-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
        }


        .pagination-controls {
            display: flex;
            gap: 10px;
        }


        .pagination-btn {
            padding: 5px 10px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }


        .pagination-btn:hover {
            background: var(--secondary);
            color: white;
        }


        .application-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }


        .status-pending {
            background: #fff3cd;
            color: var(--warning);
        }


        .status-verified {
            background: #d1edff;
            color: var(--secondary);
        }


        .status-rejected {
            background: #ffebee;
            color: var(--accent);
        }


        .status-sent {
            background: #d4edda;
            color: var(--success);
        }


        /* Responsibilities */
        .responsibilities {
            margin-bottom: 30px;
        }


        .steps {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }


        .step {
            flex: 1;
            min-width: 200px;
            text-align: center;
            padding: 20px;
            margin: 10px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }


        .step-number {
            width: 40px;
            height: 40px;
            background: var(--secondary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-weight: bold;
        }


        .step h4 {
            margin-bottom: 10px;
            color: var(--primary);
        }


        .step p {
            color: var(--gray);
            font-size: 14px;
        }


        /* System Features */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }


        .feature {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }


        .feature i {
            font-size: 24px;
            color: var(--secondary);
            margin-bottom: 15px;
        }


        .feature h4 {
            margin-bottom: 10px;
            color: var(--primary);
        }


        .feature p {
            color: var(--gray);
            font-size: 14px;
        }


        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 30px;
            color: var(--gray);
            font-size: 14px;
            border-top: 1px solid #e0e0e0;
        }


        /* Responsive */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 10px;
                height: auto;
            }
            
            .nav-links {
                display: flex;
                overflow-x: auto;
            }
            
            .nav-links li {
                white-space: nowrap;
            }
            
            .main-content {
                height: auto;
                overflow-y: visible;
            }
            
            .dashboard-panels {
                grid-template-columns: 1fr;
            }
        }


        @media (max-width: 768px) {
            .steps {
                flex-direction: column;
            }
            
            .step {
                min-width: 100%;
            }
            
            .charts-container {
                grid-template-columns: 1fr;
            }
            
            .chart-card {
                min-width: 100%;
            }
            
            .records-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .table-header, .table-row {
                grid-template-columns: 1fr 1fr;
                font-size: 14px;
            }
            
            .table-header div:nth-child(3),
            .table-header div:nth-child(4),
            .table-row div:nth-child(3),
            .table-row div:nth-child(4) {
                display: none;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .header-actions {
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <div class="logo-image">
                    <img src="../images/LOGO.jpg" alt="Barangay Pinagbuhatan Logo" class="logo-image" onerror="this.style.display='none'; document.getElementById('fallback-logo').style.display='flex';">
                </div>
                <h1>CARELINK</h1>
            </div>
            <ul class="nav-links">
                <li class="active"><a href="Barangay_Dash.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="Submit_Application.php"><i class="fas fa-user-plus"></i> Submit Application</a></li>
                <li><a href="Barangay_Records.php"><i class="fas fa-database"></i> Records</a></li>
                <li><a href="Settings.html"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>


        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-content">
                    <div class="welcome-message" data-first-name="<?php echo htmlspecialchars($_SESSION['first_name']); ?>" data-last-name="<?php echo htmlspecialchars($_SESSION['last_name']); ?>" data-role="<?php echo htmlspecialchars($_SESSION['role']); ?>"></div>
                    <h1>Barangay Pinagbuhatan Dashboard</h1>
                </div>
                <div class="header-actions">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-details">
                            <h2><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h2>
                            <p><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $_SESSION['role']))); ?></p>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Two Panel Layout -->
            <div class="dashboard-panels">
                <!-- Left Panel - Charts -->
                <div class="left-panel">
                    <h2 style="color: var(--primary);">Application Statistics</h2>
                    <div class="charts-container">
                        <div class="chart-card">
                            <h3><i class="fas fa-chart-pie"></i> Application Status Distribution</h3>
                            <div class="chart-wrapper">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-card">
                            <h3><i class="fas fa-chart-bar"></i> Monthly Applications</h3>
                            <div class="chart-wrapper">
                                <canvas id="monthlyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Right Panel - Notifications -->
                <div class="right-panel">
                    <div class="notifications-card">
                        <div class="notifications-header">
                            <h3><i class="fas fa-bell"></i> Department Notifications</h3>
                            <div class="notification-badge">5</div>
                        </div>
                        <div class="notifications-list">
                            <div class="notification-item notification-urgent">
                                <div class="notification-icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div class="notification-info">
                                    <div class="notification-title">Urgent: Missing Documents</div>
                                    <div class="notification-message">5 applications from last week are missing required documents. Please review and complete.</div>
                                    <div class="notification-time">2 hours ago</div>
                                </div>
                            </div>
                            <div class="notification-item notification-warning">
                                <div class="notification-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="notification-info">
                                    <div class="notification-title">Pending Verification</div>
                                    <div class="notification-message">12 applications are awaiting your verification before submission to City Hall.</div>
                                    <div class="notification-time">Yesterday</div>
                                </div>
                            </div>
                            <div class="notification-item notification-success">
                                <div class="notification-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="notification-info">
                                    <div class="notification-title">Applications Approved</div>
                                    <div class="notification-message">8 applications have been approved by the Department and are ready for pickup.</div>
                                    <div class="notification-time">2 days ago</div>
                                </div>
                            </div>
                            <div class="notification-item notification-default">
                                <div class="notification-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="notification-info">
                                    <div class="notification-title">System Update</div>
                                    <div class="notification-message">The CNN verification system will undergo maintenance this weekend.</div>
                                    <div class="notification-time">3 days ago</div>
                                </div>
                            </div>
                            <div class="notification-item notification-default">
                                <div class="notification-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="notification-info">
                                    <div class="notification-title">Monthly Report Due</div>
                                    <div class="notification-message">Monthly application report is due by the end of this week.</div>
                                    <div class="notification-time">1 week ago</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Responsibilities -->
            <div class="responsibilities">
                <h2 style="margin-bottom: 15px; color: var(--primary);">Your Responsibilities</h2>
                
                <div class="steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h4>Collect Applications</h4>
                        <p>Gather all required documents from applicants</p>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <h4>Input Information</h4>
                        <p>Enter applicant details into the system</p>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <h4>Verify Documents</h4>
                        <p>Use CNN system to validate ID authenticity</p>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <h4>Submit to Department</h4>
                        <p>Send verified applications for final approval</p>
                    </div>
                </div>
            </div>


            <!-- System Features -->
            <h2 style="margin-bottom: 15px; color: var(--primary);">System Features</h2>
            <div class="features">
                <div class="feature">
                    <i class="fas fa-robot"></i>
                    <h4>CNN Verification</h4>
                    <p>AI-powered ID authentication prevents fraud</p>
                </div>
                <div class="feature">
                    <i class="fas fa-copy"></i>
                    <h4>Duplicate Detection</h4>
                    <p>Automatic checking for existing records</p>
                </div>
                <div class="feature">
                    <i class="fas fa-chart-line"></i>
                    <h4>Real-time Tracking</h4>
                    <p>Monitor application status throughout process</p>
                </div>
            </div>


            <!-- Footer -->
            <div class="footer">
                <p>Centralized Profiling and Record Authentication System | Barangay Pinagbuhatan &copy; 2024</p>
            </div>
        </div>
    </div>


    <script>
        // Simple script for interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event to navigation items
            const navItems = document.querySelectorAll('.nav-links li');
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    navItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Initialize charts
            initializeCharts();
            
            // Add functionality to table action buttons
            const viewButtons = document.querySelectorAll('.btn-small');
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('.table-row');
                    const name = row.children[0].textContent;
                    const type = row.children[1].textContent;
                    alert(`Viewing details for: ${name} (${type})`);
                });
            });
            
            // Pagination functionality
            const paginationBtns = document.querySelectorAll('.pagination-btn');
            paginationBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!this.classList.contains('active')) {
                        paginationBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                    }
                });
            });
            
            // Update welcome message based on time of day
            const welcomeMessage = document.querySelector('.welcome-message');
            const firstName = welcomeMessage.dataset.firstName;
            const lastName = welcomeMessage.dataset.lastName;
            const role = welcomeMessage.dataset.role;
            const hour = new Date().getHours();
            let greeting;
            
            if (hour < 12) {
                greeting = "Good morning";
            } else if (hour < 18) {
                greeting = "Good afternoon";
            } else {
                greeting = "Good evening";
            }
            
            welcomeMessage.innerHTML = `${greeting}, <strong>${firstName} ${lastName}</strong>!`;
        });


        function initializeCharts() {
            // Application Status Distribution Chart (Doughnut)
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending Review', 'Verified', 'Rejected', 'Sent to City Hall', 'Under Final Review'],
                    datasets: [{
                        data: [12, 8, 3, 15, 5],
                        backgroundColor: [
                            '#f39c12',
                            '#3498db',
                            '#e74c3c',
                            '#2ecc71',
                            '#9b59b6'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });


            // Monthly Applications Chart (Bar)
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'PWD Applications',
                        data: [12, 19, 15, 17, 14, 16, 18, 15, 20, 22, 18, 24],
                        backgroundColor: '#3498db',
                        borderColor: '#2980b9',
                        borderWidth: 1
                    }, {
                        label: 'Senior Citizen Applications',
                        data: [8, 12, 10, 14, 11, 13, 15, 12, 16, 18, 15, 20],
                        backgroundColor: '#2ecc71',
                        borderColor: '#27ae60',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Applications'
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>



