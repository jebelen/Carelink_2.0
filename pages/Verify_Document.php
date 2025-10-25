<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CARELINK — Verify Documents</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/department-sidebar.css">
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

        /* Main Content */
        .main-content {
            padding: 20px;
            overflow-y: auto;
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

        /* Cards */
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--primary);
            display: flex;
            align-items: center;
        }

        .card h3 i {
            margin-right: 10px;
            color: var(--secondary);
        }

        /* Buttons */
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
            font-size: 14px;
        }

        .btn:hover {
            background: #2980b9;
        }

        .btn-success {
            background: var(--success);
        }

        .btn-success:hover {
            background: #27ae60;
        }

        .btn-warning {
            background: var(--warning);
        }

        .btn-warning:hover {
            background: #e67e22;
        }

        .btn-danger {
            background: var(--accent);
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-small {
            padding: 8px 16px;
            font-size: 12px;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .table th {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-verified {
            background: #d4edda;
            color: var(--success);
        }

        .status-pending {
            background: #fff3cd;
            color: var(--warning);
        }

        .status-rejected {
            background: #f8d7da;
            color: var(--accent);
        }

        /* Form */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: var(--primary);
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--secondary);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Actions */
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .stat-card h4 {
            font-size: 2rem;
            color: var(--secondary);
            margin-bottom: 5px;
        }

        .stat-card p {
            color: var(--gray);
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .header-actions {
                align-self: flex-end;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <!-- Logo image with fallback -->
                    <img src="../images/LOGO.jpg" alt="Barangay Pinagbuhatan Logo" class="logo-image" onerror="this.style.display='none'; document.getElementById('fallback-logo').style.display='flex';">
                    <div id="fallback-logo" class="logo-image" style="display: none; background: var(--secondary); width: 40px; height: 40px; border-radius: 8px; align-items: center; justify-content: center; font-weight: bold; color: white; font-size: 0.9rem;">BP</div>
                    <h1 class="logo-text">CARELINK</h1>
                </div>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="Department_Dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="User_Management.php"><i class="fas fa-user-cog"></i> User Management</a></li>
                    <li><a href="Department_Records.php"><i class="fas fa-database"></i> Records</a></li>
                    <li class="active"><a href="Verify_Document.php"><i class="fas fa-check-circle"></i> Verify Documents</a></li>
                    <li><a href="System_Settings.php"><i class="fas fa-cog"></i> System Settings</a></li>
                    <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-content">
                    <div class="welcome-message" data-first-name="<?php echo htmlspecialchars($_SESSION['first_name']); ?>" data-last-name="<?php echo htmlspecialchars($_SESSION['last_name']); ?>" data-role="<?php echo htmlspecialchars($_SESSION['role']); ?>"></div>
                    <h1>Verify Documents</h1>
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

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h4>24</h4>
                    <p>Pending Verification</p>
                        </div>
                <div class="stat-card">
                    <h4>156</h4>
                    <p>Verified Today</p>
                    </div>
                <div class="stat-card">
                    <h4>8</h4>
                    <p>Rejected</p>
                </div>
                <div class="stat-card">
                    <h4>98.5%</h4>
                    <p>Success Rate</p>
                        </div>
                    </div>
                    
            <!-- Verification Form -->
            <div class="card">
                <h3><i class="fas fa-search"></i> Document Verification</h3>
                <form id="verifyForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="documentId">Document ID</label>
                            <input type="text" id="documentId" placeholder="Enter document ID" required>
                            </div>
                        <div class="form-group">
                            <label for="applicantName">Applicant Name</label>
                            <input type="text" id="applicantName" placeholder="Enter applicant name" required>
                            </div>
                            </div>
                    <div class="form-group">
                        <label for="documentType">Document Type</label>
                        <select id="documentType" required>
                            <option value="">Select document type</option>
                            <option value="id">Government ID</option>
                            <option value="birth">Birth Certificate</option>
                            <option value="marriage">Marriage Certificate</option>
                            <option value="death">Death Certificate</option>
                            <option value="other">Other</option>
                        </select>
                            </div>
                    <div class="form-group">
                        <label for="verificationNotes">Verification Notes</label>
                        <textarea id="verificationNotes" rows="3" placeholder="Enter verification notes"></textarea>
                            </div>
                    <div class="actions">
                        <button type="button" class="btn btn-success" onclick="verifyDocument('approved')">Approve</button>
                        <button type="button" class="btn btn-danger" onclick="verifyDocument('rejected')">Reject</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        </div>
                </form>
                        </div>
                        
            <!-- Documents List -->
            <div class="card">
                <h3><i class="fas fa-file-alt"></i> Documents Queue</h3>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Document ID</th>
                                <th>Applicant</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>DOC-001</td>
                                <td>John Doe</td>
                                <td>Government ID</td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td>2024-01-15</td>
                                <td>
                                    <button class="btn btn-small btn-success">Verify</button>
                                    <button class="btn btn-small btn-warning">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>DOC-002</td>
                                <td>Jane Smith</td>
                                <td>Birth Certificate</td>
                                <td><span class="status-badge status-verified">Verified</span></td>
                                <td>2024-01-14</td>
                                <td>
                                    <button class="btn btn-small btn-warning">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>DOC-003</td>
                                <td>Mike Johnson</td>
                                <td>Marriage Certificate</td>
                                <td><span class="status-badge status-rejected">Rejected</span></td>
                                <td>2024-01-13</td>
                                <td>
                                    <button class="btn btn-small btn-warning">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                                </div>
                            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event to navigation items
            const navItems = document.querySelectorAll('.sidebar-menu li');
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    navItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
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

        function verifyDocument(status) {
            const documentId = document.getElementById('documentId').value;
            const applicantName = document.getElementById('applicantName').value;
            
            if (!documentId || !applicantName) {
                alert('Please fill in Document ID and Applicant Name');
                return;
            }
            
            const action = status === 'approved' ? 'approved' : 'rejected';
            alert(`Document ${action} successfully!`);
            document.getElementById('verifyForm').reset();
        }
    </script>
</body>
</html>