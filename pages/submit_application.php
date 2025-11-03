<?php
session_start();
require_once '../includes/db_connect.php';
?>
<!DOCTYPE html>
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
            overflow: hidden;
        }


        .container {
            display: flex;
            height: 100vh;
        }


        /* Sidebar */
       


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
            overflow-y: auto;
            height: 100vh;
        }


        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }


        .header-content h1 {
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


        /* Application Form Styles */
        .application-form {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }


        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }


        .form-section:last-of-type {
            border-bottom: none;
        }


        .form-section h3 {
            font-size: 20px;
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }


        .form-section h3 i {
            margin-right: 10px;
            color: var(--secondary);
        }


        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }


        .form-group {
            margin-bottom: 20px;
        }


        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary);
        }


        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }


        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--secondary);
            outline: none;
        }


        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }


        .required::after {
            content: " *";
            color: var(--accent);
        }


        .document-list {
            list-style: none;
            margin-top: 10px;
        }


        .document-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }


        .document-list li:last-child {
            border-bottom: none;
        }


        .file-upload {
            border: 2px dashed #ddd;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            margin-top: 10px;
            transition: border 0.3s;
        }


        .file-upload:hover {
            border-color: var(--secondary);
        }


        .file-upload input {
            display: none;
        }


        .file-upload label {
            display: block;
            cursor: pointer;
            color: var(--secondary);
            font-weight: 500;
        }


        .file-info {
            font-size: 14px;
            color: var(--gray);
            margin-top: 10px;
        }


        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
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


        /* Table Styles */
        .applications-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }


        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }


        .table-header h2 {
            color: var(--primary);
        }


        .table-controls {
            display: flex;
            gap: 10px;
        }


        .search-box {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 250px;
        }


        table {
            width: 100%;
            border-collapse: collapse;
        }


        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }


        th {
            background-color: #f8f9fa;
            color: var(--primary);
            font-weight: 600;
        }


        tr:hover {
            background-color: #f5f7fa;
        }


        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }


        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }


        .status-approved {
            background-color: #d1ecf1;
            color: #0c5460;
        }


        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }


        .name-link {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }


        .name-link:hover {
            text-decoration: underline;
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
           
            .form-actions {
                flex-direction: column;
                gap: 10px;
            }
           
            .form-actions .btn {
                width: 100%;
            }
           
            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
           
            .table-controls {
                width: 100%;
            }
           
            .search-box {
                width: 100%;
            }
           
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include '../partials/barangay_sidebar.php'; ?>


        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-content">
                    <div class="welcome-message" data-first-name="<?php echo htmlspecialchars($_SESSION['first_name']); ?>" data-last-name="<?php echo htmlspecialchars($_SESSION['last_name']); ?>"></div>
                </div>
                <div class="header-actions">
                    <button class="btn"><i class="fas fa-bell"></i> Notifications</button>
                    <a href="new_application.php" class="btn"><i class="fas fa-plus"></i> Add Application</a>
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


            <!-- Page Title -->
            <div class="page-title">
                <p>Manage PWD and Senior Citizen applications for your barangay. View applications, verify documents, and track the status of applications.</p>
            </div>


            <!-- Applications Table -->
            <div class="applications-table">
                <div class="table-header">
                    <h2>Applications</h2>
                    <div class="table-controls">
                        <input type="text" class="search-box" placeholder="Search applications...">
                        <select id="applicationTypeFilter" class="btn">
                            <option value="">All Types</option>
                            <option value="pwd">PWD</option>
                            <option value="senior">Senior</option>
                        </select>
                        <select id="statusFilter" class="btn">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <button class="btn" id="applyFilterBtn"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
               
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Application Type</th>
                            <th>Birth Date</th>
                            <th>Contact Number</th>
                            <th>Date Submitted</th>
                            <th>Status</th>
                            <th>Complete Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="applicationsTableBody">
                        <tr><td colspan="8">Loading applications...</td></tr>
                    </tbody>
                </table>
            </div>


            <!-- Footer -->
            <div class="footer">
                <p>Centralized Profiling and Record Authentication System | Barangay Pinagbuhatan &copy; 2024</p>
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
            const tableBody = document.querySelector('#applicationsTableBody');
            tableBody.addEventListener('click', function(event) {
                const clickedElement = event.target.closest('.name-link');
                if (clickedElement) {
                    const appId = clickedElement.dataset.id;
                    openApplicationModal(appId);
                }
            });

            const closeModalBtn = document.querySelector('#applicationModal .close-modal');
            closeModalBtn.addEventListener('click', () => {
                document.getElementById('applicationModal').style.display = 'none';
            });

            // Update welcome message based on time of day
            const welcomeMessage = document.querySelector('.welcome-message');
            if (welcomeMessage) {
                const firstName = welcomeMessage.dataset.firstName;
                const lastName = welcomeMessage.dataset.lastName;
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
            }
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

        function deleteApplication(appId) {
            if (confirm('Are you sure you want to delete this application?')) {
                fetch('../api/delete_application.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${appId}`,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Application deleted successfully!');
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        const searchInput = document.querySelector('.search-box');
        const applicationTypeFilter = document.querySelector('#applicationTypeFilter');
        const statusFilter = document.querySelector('#statusFilter');
        const applyFilterBtn = document.querySelector('#applyFilterBtn');

        function fetchApplications() {
            const query = searchInput.value;
            const type = applicationTypeFilter.value;
            const status = statusFilter.value;

            let url = `../api/search_applications.php?query=${query}`;
            if (type) {
                url += `&type=${type}`;
            }
            if (status) {
                url += `&status=${status}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(applications => {
                    const tableBody = document.querySelector('#applicationsTableBody');
                    tableBody.innerHTML = ''; // Clear existing rows

                    if (applications.length > 0) {
                        applications.forEach(app => {
                            const row = `
                                <tr>
                                    <td><a href="#" class="name-link" data-id="${app.id}">${app.full_name}</a></td>
                                    <td>${app.application_type}</td>
                                    <td>${app.birth_date}</td>
                                    <td>${app.contact_number}</td>
                                    <td>${app.date_submitted}</td>
                                    <td><span class="status-badge status-${app.status}">${app.status}</span></td>
                                    <td>${app.complete_address}</td>
                                    <td><class="btn btn-danger btn-small" onclick="deleteApplication(${app.id})">Delete</button></td>
                                </tr>
                            `;
                            tableBody.innerHTML += row;
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="8">No applications found.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching applications:', error);
                    const tableBody = document.querySelector('#applicationsTableBody');
                    tableBody.innerHTML = '<tr><td colspan="8">Error loading applications.</td></tr>';
                });
        }

        // Event Listeners
        searchInput.addEventListener('keyup', fetchApplications);
        applicationTypeFilter.addEventListener('change', fetchApplications);
        statusFilter.addEventListener('change', fetchApplications);
        applyFilterBtn.addEventListener('click', fetchApplications);

        // Initial load of applications
        fetchApplications();
    </script>


