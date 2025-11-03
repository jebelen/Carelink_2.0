<?php
session_start();
require_once '../includes/db_connect.php';
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

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
        }

        .modal-content {
            background-color: white;
            margin: 50px auto;
            width: 90%;
            max-width: 900px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid #e0e0e0;
        }

        .modal-header h2 {
            color: var(--primary);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray);
        }

        .close-modal:hover {
            color: var(--accent);
        }

        .modal-body {
            padding: 30px;
            max-height: 70vh;
            overflow-y: auto;
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

            <!-- Documents List -->
            <div class="card">
                <h3><i class="fas fa-file-alt"></i> Documents Queue</h3>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Applicant Name</th>
                                <th>Type</th>
                                <th>Barangay</th>
                                <th>Date Submitted</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM applications";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($result as $row) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['application_type']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['barangay']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['date_submitted']) . "</td>";
                                echo "<td><span class=\"status-badge status-" . htmlspecialchars($row['status']) . "\">" . htmlspecialchars($row['status']) . "</span></td>";
                                echo "<td><button class=\"btn btn-small btn-primary view-details-btn\" data-id=\"" . htmlspecialchars($row['id']) . "\">View Details</button></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Detail Modal -->
    <div id="applicationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Application Details</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Application details will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event to navigation items
            const menuItems = document.querySelectorAll('.sidebar-menu li');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Event delegation for View Details buttons
            const tableBody = document.querySelector('.table tbody');
            tableBody.addEventListener('click', function(event) {
                if (event.target.classList.contains('view-details-btn')) {
                    const appId = event.target.dataset.id;
                    openApplicationModal(appId);
                }
            });

            const closeModalBtn = document.querySelector('#applicationModal .close-modal');
            closeModalBtn.addEventListener('click', () => {
                document.getElementById('applicationModal').style.display = 'none';
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

        function openApplicationModal(appId) {
            const modalBody = document.querySelector('#applicationModal .modal-body');
            modalBody.innerHTML = '<p>Loading...</p>';
            document.getElementById('applicationModal').style.display = 'block';

            fetch(`../api/get_application_details.php?id=${appId}`)
                .then(response => response.json())
                .then(application => {
                    if (application.error) {
                        modalBody.innerHTML = `<p>Error: ${application.error}</p>`;
                        return;
                    }

                    if (!application) {
                        modalBody.innerHTML = '<p>Error loading application details.</p>';
                        return;
                    }

                    let documentsHtml = '';
                    if (application.has_birth_certificate) documentsHtml += `<li><a href="../api/get_document.php?id=${appId}&doc_type=birth_certificate" target="_blank">Birth Certificate</a></li>`;
                    if (application.has_medical_certificate) documentsHtml += `<li><a href="../api/get_document.php?id=${appId}&doc_type=medical_certificate" target="_blank">Medical Certificate</a></li>`;
                    if (application.has_client_identification) documentsHtml += `<li><a href="../api/get_document.php?id=${appId}&doc_type=client_identification" target="_blank">Client Identification</a></li>`;
                    if (application.has_proof_of_address) documentsHtml += `<li><a href="../api/get_document.php?id=${appId}&doc_type=proof_of_address" target="_blank">Proof of Address</a></li>`;
                    if (application.has_id_image) documentsHtml += `<li><a href="../api/get_document.php?id=${appId}&doc_type=id_image" target="_blank">ID Image</a></li>`;

                    modalBody.innerHTML = `
                        <div class="card">
                            <h3><i class="fas fa-user"></i> Applicant Information</h3>
                            <p><strong>Full Name:</strong> ${application.full_name}</p>
                            <p><strong>Application Type:</strong> ${application.application_type}</p>
                            <p><strong>Birth Date:</strong> ${application.birth_date}</p>
                            <p><strong>Contact Number:</strong> ${application.contact_number}</p>
                            <p><strong>Address:</strong> ${application.complete_address}</p>
                        </div>
                        <div class="card">
                            <h3><i class="fas fa-file-alt"></i> Uploaded Documents</h3>
                            <ul>${documentsHtml}</ul>
                        </div>
                        <div class="card">
                            <h3><i class="fas fa-check-circle"></i> Verification</h3>
                            <div class="actions">
                                <button type="button" class="btn btn-success" onclick="updateStatus(${appId}, 'approved')">Approve</button>
                                <button type="button" class="btn btn-danger" onclick="updateStatus(${appId}, 'rejected')">Reject</button>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    modalBody.innerHTML = '<p>Error loading application details. Please check the console for more information.</p>';
                });
        }

        function updateStatus(appId, status) {
            if (confirm(`Are you sure you want to ${status} this application?`)) {
                fetch(`../api/admin_${status}_application.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${appId}`,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Application ${status} successfully!`);
                        location.reload();
                    }
                });
            }
        }
    </script>
</body>
</html>
