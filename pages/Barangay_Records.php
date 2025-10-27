<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect if not logged in or not barangay staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'barangay_staff') {
    header('Location: ../index.php');
    exit;
}

$barangayName = htmlspecialchars($_SESSION['barangay'] ?? 'Unknown Barangay');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Records - Barangay <?php echo $barangayName; ?></title>
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

        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }

        .table-header {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr;
            padding: 15px 20px;
            background: var(--primary);
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
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

        /* Yearly Records Section */
        .yearly-records {
            margin-bottom: 30px;
        }

        .year-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
        }

        .year-btn {
            padding: 8px 15px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .year-btn.active {
            background: var(--secondary);
            color: white;
            border-color: var(--secondary);
        }

        .year-btn:hover:not(.active) {
            background: #f8f9fa;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-size: 14px;
            color: var(--dark);
            font-weight: 500;
        }

        .filter-group select, .filter-group input {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            min-width: 150px;
            transition: border-color 0.3s;
        }

        .filter-group select:focus, .filter-group input:focus {
            border-color: var(--secondary);
            outline: none;
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
        }

        @media (max-width: 768px) {
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
            
            .year-selector {
                flex-wrap: wrap;
            }
            
            .filter-section {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        .no-results {
            text-align: center;
            padding: 20px;
            color: var(--gray);
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <div class="logo-image">
                    <img src="../images/LOGO.jpg" alt="Barangay <?php echo $barangayName; ?> Logo" class="logo-image" onerror="this.style.display='none'; document.getElementById('fallback-logo').style.display='flex';">
                </div>
                <h1>CARELINK</h1>
            </div>
            <ul class="nav-links">
                <li><a href="Barangay_Dash.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="Submit_Application.php"><i class="fas fa-user-plus"></i> Submit Application</a></li>
                <li class="active"><a href="Barangay_Records.php"><i class="fas fa-database"></i> Records</a></li>
                <li><a href="Settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-content">
                    <div class="welcome-message">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></strong>!</div>
                    <h1>Barangay <?php echo $barangayName; ?> Records</h1>
                </div>
                <div class="header-actions">
                    <button class="btn"><i class="fas fa-bell"></i> Notifications</button>
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

            <!-- Yearly Records Section -->
            <div class="yearly-records">
                <div class="records-header">
                    <h2>Yearly Application Records</h2>
                    <div class="records-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search records...">
                        </div>
                        <button class="btn"><i class="fas fa-download"></i> Export</button>
                    </div>
                </div>
                
                <div class="year-selector">
                    <button class="year-btn" data-year="2020">2020</button>
                    <button class="year-btn" data-year="2021">2021</button>
                    <button class="year-btn" data-year="2022">2022</button>
                    <button class="year-btn" data-year="2023">2023</button>
                    <button class="year-btn active" data-year="2024">2024</button>
                </div>
            </div>

            <!-- Records Section -->
            <div class="records-section">
                <div class="records-header">
                    <h2>All Records for <span id="current-year">2024</span></h2>
                    <div class="records-actions">
                        <button class="btn btn-accent" id="filter-btn">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="filter-group">
                        <label for="id-filter">ID Number</label>
                        <input type="text" id="id-filter" placeholder="Enter ID number...">
                    </div>
                    <div class="filter-group">
                        <label for="type-filter">Application Type</label>
                        <select id="type-filter">
                            <option value="all">All Types</option>
                            <option value="PWD">PWD</option>
                            <option value="Senior Citizen">Senior Citizen</option>
                            <option value="Solo Parent">Solo Parent</option>
                            <option value="4Ps">4Ps</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="status-filter">Status</label>
                        <select id="status-filter">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="verified">Verified</option>
                            <option value="rejected">Rejected</option>
                            <option value="sent">Sent to City Hall</option>
                        </select>
                    </div>
                </div>
                
                <div class="records-table">
                    <div class="table-container">
                        <div class="table-header">
                            <div>Applicant Name</div>
                            <div>Application Type</div>
                            <div>Date Submitted</div>
                            <div>Status</div>
                            <div>ID Number</div>
                            <div>Actions</div>
                        </div>
                        
                        <div class="table-data">
                            <div class="table-row">
                                <div>Carlos Mendoza</div>
                                <div>PWD</div>
                                <div>01/05/2024</div>
                                <div><span class="application-status status-rejected">Rejected</span></div>
                                <div>PWD-004</div>
                                <div>
                                    <button class="btn btn-small"><i class="fas fa-eye"></i> View</button>
                                </div>
                            </div>
                            
                            <div class="table-row">
                                <div>Maria Santos</div>
                                <div>Senior Citizen</div>
                                <div>01/08/2024</div>
                                <div><span class="application-status status-pending">Pending</span></div>
                                <div>SC-012</div>
                                <div>
                                    <button class="btn btn-small"><i class="fas fa-eye"></i> View</button>
                                </div>
                            </div>
                            
                            <div class="table-row">
                                <div>Juan Dela Cruz</div>
                                <div>PWD</div>
                                <div>01/10/2024</div>
                                <div><span class="application-status status-sent">Sent to City Hall</span></div>
                                <div>PWD-007</div>
                                <div>
                                    <button class="btn btn-small"><i class="fas fa-eye"></i> View</button>
                                </div>
                            </div>
                            
                            <div class="table-row">
                                <div>Elena Garcia</div>
                                <div>Senior Citizen</div>
                                <div>01/12/2024</div>
                                <div><span class="application-status status-verified">Verified</span></div>
                                <div>SC-015</div>
                                <div>
                                    <button class="btn btn-small"><i class="fas fa-eye"></i> View</button>
                                </div>
                            </div>
                            
                            <div class="table-row">
                                <div>Roberto Santos</div>
                                <div>PWD</div>
                                <div>01/15/2024</div>
                                <div><span class="application-status status-pending">Pending</span></div>
                                <div>PWD-021</div>
                                <div>
                                    <button class="btn btn-small"><i class="fas fa-eye"></i> View</button>
                                </div>
                            </div>
                            
                            <div class="table-row">
                                <div>Ana Reyes</div>
                                <div>Solo Parent</div>
                                <div>01/18/2024</div>
                                <div><span class="application-status status-verified">Verified</span></div>
                                <div>SP-008</div>
                                <div>
                                    <button class="btn btn-small"><i class="fas fa-eye"></i> View</button>
                                </div>
                            </div>
                            
                            <div class="table-row">
                                <div>Luis Cruz</div>
                                <div>4Ps</div>
                                <div>01/20/2024</div>
                                <div><span class="application-status status-rejected">Rejected</span></div>
                                <div>4PS-003</div>
                                <div>
                                    <button class="btn btn-small"><i class="fas fa-eye"></i> View</button>
                                </div>
                            </div>
                            
                            <div class="table-row">
                                <div>Carmen Lopez</div>
                                <div>Senior Citizen</div>
                                <div>01/22/2024</div>
                                <div><span class="application-status status-sent">Sent to City Hall</span></div>
                                <div>SC-019</div>
                                <div>
                                    <button class="btn btn-small"><i class="fas fa-eye"></i> View</button>
                                </div>
                            </div>
                            
                            <div class="table-row">
                                <div>Pedro Gonzales</div>
                                <div>PWD</div>
                                <div>01/25/2024</div>
                                <div><span class="application-status status-pending">Pending</span></div>
                                <div>PWD-025</div>
                                <div>
                                    <button class="btn btn-small"><i class="fas fa-eye"></i> View</button>
                                </div>
                            </div>
                            
                            <div class="table-row">
                                <div>Sofia Ramirez</div>
                                <div>Others</div>
                                <div>01/28/2024</div>
                                <div><span class="application-status status-verified">Verified</span></div>
                                <div>OTH-002</div>
                                <div>
                                    <button class="btn btn-small"><i class="fas fa-eye"></i> View</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>Centralized Profiling and Record Authentication System | Barangay <?php echo $barangayName; ?> &copy; 2024</p>
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
            
            // Year selection functionality
            const yearBtns = document.querySelectorAll('.year-btn');
            const currentYearElement = document.getElementById('current-year');
            
            yearBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const year = this.getAttribute('data-year');
                    
                    // Update active year button
                    yearBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Update current year display
                    currentYearElement.textContent = year;
                    
                    // Update table data with new year records
                    updateTableData(year);
                });
            });
            
            // Auto-filter functionality
            const idFilter = document.getElementById('id-filter');
            const typeFilter = document.getElementById('type-filter');
            const statusFilter = document.getElementById('status-filter');
            
            // Add event listeners for automatic filtering
            idFilter.addEventListener('input', applyFilters);
            typeFilter.addEventListener('change', applyFilters);
            statusFilter.addEventListener('change', applyFilters);
            
            // Update welcome message based on time of day
            const welcomeMessage = document.querySelector('.welcome-message');
            const hour = new Date().getHours();
            let greeting;
            
            if (hour < 12) {
                greeting = "Good morning";
            } else if (hour < 18) {
                greeting = "Good afternoon";
            } else {
                greeting = "Good evening";
            }
            
            welcomeMessage.innerHTML = `${greeting}, <strong><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></strong>!`;
        });

        function updateTableData(year) {
            // In a real application, this would fetch data from a server
            // For this demo, we'll just update the year in the table header
            document.getElementById('current-year').textContent = year;
            
            // Show a message to indicate the data has been updated
            const tableRows = document.querySelectorAll('.table-row');
            tableRows.forEach(row => {
                // Update the date to reflect the selected year
                const dateCell = row.children[2];
                const currentDate = dateCell.textContent;
                const newDate = currentDate.replace(/\d{4}$/, year);
                dateCell.textContent = newDate;
            });
        }

        function applyFilters() {
            const idFilterValue = document.getElementById('id-filter').value.toLowerCase();
            const typeFilterValue = document.getElementById('type-filter').value;
            const statusFilterValue = document.getElementById('status-filter').value;
            const tableRows = document.querySelectorAll('.table-row');
            
            tableRows.forEach(row => {
                const idCell = row.children[4].textContent.toLowerCase();
                const typeCell = row.children[1].textContent;
                const statusCell = row.children[3].textContent;
                
                // Check if row matches all filter criteria
                const idMatch = idFilterValue === '' || idCell.includes(idFilterValue);
                const typeMatch = typeFilterValue === 'all' || typeCell === typeFilterValue;
                const statusMatch = statusFilterValue === 'all' || 
                    (statusFilterValue === 'pending' && statusCell.includes('Pending')) ||
                    (statusFilterValue === 'verified' && statusCell.includes('Verified')) ||
                    (statusFilterValue === 'rejected' && statusCell.includes('Rejected')) ||
                    (statusFilterValue === 'sent' && statusCell.includes('Sent'));
                
                if (idMatch && typeMatch && statusMatch) {
                    row.style.display = 'grid';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>