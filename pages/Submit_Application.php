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
        <div class="sidebar">
            <div class="logo">
                <div class="logo-image">
                    <img src="../images/LOGO.jpg" alt="Barangay Pinagbuhatan Logo" class="logo-image" onerror="this.style.display='none'; document.getElementById('fallback-logo').style.display='flex';">
                </div>
                <h1>CARELINK</h1>
            </div>
            <ul class="nav-links">
                <li><a href="Barangay_Dash.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="active"><a href="Submit_Application.php"><i class="fas fa-user-plus"></i> Submit Application</a></li>
                <li><a href="Barangay_Records.php"><i class="fas fa-database"></i> Records</a></li>
                <li><a href="Settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="../index.php" onclick="/* implement logout */"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>


        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-content">
                    <div class="welcome-message">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></strong>!</div>
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


            <!-- Page Title -->
            <div class="page-title">
                <p>Manage PWD and Senior Citizen applications for your barangay. View applications, verify documents, and track the status of applications.</p>
            </div>


            <!-- Applications Table -->
            <div class="applications-table">
                <div class="table-header">
                    <h2>Applications from Google Form</h2>
                    <div class="table-controls">
                        <input type="text" class="search-box" placeholder="Search applications...">
                        <button class="btn"><i class="fas fa-filter"></i> Filter</button>
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
                        </tr>
                    </thead>
                    <tbody id="applicationsTableBody">
                        <!-- Applications will be populated by JavaScript -->
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
                <!-- Application Form -->
                <div class="application-form">
                    <h2 style="margin-bottom: 20px; color: var(--primary);">Application Form</h2>
                    <p style="margin-bottom: 30px; color: var(--gray);">View application details for <span id="applicantName" style="font-weight: 600;"></span></p>
                   
                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Basic Information</h3>
                       
                        <div class="form-row">
                            <div class="form-group">
                                <label for="modalFullName" class="required">Full Name</label>
                                <input type="text" id="modalFullName" placeholder="Enter full name" readonly>
                            </div>
                           
                            <div class="form-group">
                                <label for="modalApplicationType" class="required">Application Type</label>
                                <select id="modalApplicationType" disabled>
                                    <option value="">Select application type</option>
                                    <option value="pwd">PWD (Person With Disability)</option>
                                    <option value="senior">Senior Citizen</option>
                                </select>
                            </div>
                        </div>
                       
                        <div class="form-row">
                            <div class="form-group">
                                <label for="modalBirthDate" class="required">Birth Date</label>
                                <input type="date" id="modalBirthDate" readonly>
                            </div>
                           
                            <div class="form-group">
                                <label for="modalContactNumber" class="required">Contact Number</label>
                                <input type="tel" id="modalContactNumber" placeholder="0805-0000-0000" readonly>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label for="modalCompleteAddress" class="required">Complete Address</label>
                            <textarea id="modalCompleteAddress" placeholder="Enter complete address" readonly></textarea>
                        </div>
                       
                        <div class="form-row">
                            <div class="form-group">
                                <label for="modalEmailAddress">Email Address (Optional)</label>
                                <input type="email" id="modalEmailAddress" placeholder="email@example.com" readonly>
                            </div>
                           
                            <div class="form-group">
                                <label for="modalEmergencyContact" class="required">Emergency Contact Number</label>
                                <input type="tel" id="modalEmergencyContact" placeholder="0805-0000-0000" readonly>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label for="modalEmergencyContactName">Emergency Contact Name</label>
                            <input type="text" id="modalEmergencyContactName" placeholder="Enter emergency contact name" readonly>
                        </div>
                       
                        <div class="form-group">
                            <label for="modalMedicalConditions">Medical Conditions (Optional)</label>
                            <textarea id="modalMedicalConditions" placeholder="Any relevant medical conditions" readonly></textarea>
                        </div>
                    </div>
                   
                    <!-- Required Documents Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-file-alt"></i> Required Documents</h3>
                       
                        <div class="form-group">
                            <label class="required">Status of documents being submitted</label>
                            <ul class="document-list">
                                <li>
                                    <input type="checkbox" id="modalBirthCertificate" disabled>
                                    <label for="modalBirthCertificate">Birth Certificate</label>
                                    <span id="modalBirthCertificateResult" style="margin-left:10px; font-weight:500;"></span>
                                </li>
                                <li>
                                    <input type="checkbox" id="modalMedicalCertificate" disabled>
                                    <label for="modalMedicalCertificate">Medical Certificate</label>
                                    <span id="modalMedicalCertificateResult" style="margin-left:10px; font-weight:500;"></span>
                                </li>
                                <li>
                                    <input type="checkbox" id="modalClientIdentification" disabled>
                                    <label for="modalClientIdentification">Client Identification</label>
                                    <span id="modalClientIdentificationResult" style="margin-left:10px; font-weight:500;"></span>
                                </li>
                                <li>
                                    <input type="checkbox" id="modalProofOfAddress" disabled>
                                    <label for="modalProofOfAddress">Proof of Address</label>
                                    <span id="modalProofOfAddressResult" style="margin-left:10px; font-weight:500;"></span>
                                </li>
                                <li>
                                    <input type="checkbox" id="modalIdImage" disabled>
                                    <label for="modalIdImage">Updated ID Image</label>
                                    <span id="modalIdImageResult" style="margin-left:10px; font-weight:500;"></span>
                                </li>
                            </ul>
                        </div>
                       
                        <div class="form-group">
                            <label for="modalValidId" class="required">Validated ID for verification</label>
                            <div class="file-upload">
                                <div class="file-info" id="modalValidIdInfo">No file uploaded</div>
                                <div id="modalValidIdResult" style="margin-top:10px; color: var(--success); font-weight: 500;"></div>
                            </div>
                        </div>
                    </div>
                   
                    <!-- Additional Information Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-info-circle"></i> Additional Information</h3>
                       
                        <div class="form-group">
                            <label for="modalAdditionalNotes">Additional Notes (Optional)</label>
                            <textarea id="modalAdditionalNotes" placeholder="Any additional information or special circumstances" readonly></textarea>
                        </div>
                    </div>
                   
                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button class="btn btn-accent" id="closeModalBtn"><i class="fas fa-times"></i> Close</button>
                        <button class="btn" id="approveBtn"><i class="fas fa-check"></i> Approve Application</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Sample data from Google Form (in a real app, this would come from an API)
        const applications = [
            {
                id: 1,
                fullName: "Juan Dela Cruz",
                applicationType: "pwd",
                birthDate: "1985-05-15",
                contactNumber: "0917-123-4567",
                emailAddress: "juan.delacruz@example.com",
                completeAddress: "123 Main Street, Barangay Pinagbuhatan, Pasig City",
                emergencyContact: "0918-765-4321",
                emergencyContactName: "Maria Dela Cruz",
                medicalConditions: "Mild visual impairment",
                documents: {
                    birthCertificate: true,
                    medicalCertificate: true,
                    clientIdentification: true,
                    proofOfAddress: true,
                    idImage: true
                },
                dateSubmitted: "2024-01-15",
                status: "pending",
                additionalNotes: "Applicant has been a resident for 10 years."
            },
            {
                id: 2,
                fullName: "Maria Santos",
                applicationType: "senior",
                birthDate: "1955-11-22",
                contactNumber: "0922-987-6543",
                emailAddress: "maria.santos@example.com",
                completeAddress: "456 Oak Avenue, Barangay Pinagbuhatan, Pasig City",
                emergencyContact: "0917-555-1234",
                emergencyContactName: "Pedro Santos",
                medicalConditions: "Hypertension, arthritis",
                documents: {
                    birthCertificate: true,
                    medicalCertificate: true,
                    clientIdentification: true,
                    proofOfAddress: true,
                    idImage: false
                },
                dateSubmitted: "2024-01-18",
                status: "pending",
                additionalNotes: "Applicant needs assistance with mobility."
            },
            {
                id: 3,
                fullName: "Roberto Garcia",
                applicationType: "pwd",
                birthDate: "1978-03-10",
                contactNumber: "0933-456-7890",
                emailAddress: "roberto.garcia@example.com",
                completeAddress: "789 Pine Road, Barangay Pinagbuhatan, Pasig City",
                emergencyContact: "0920-111-2222",
                emergencyContactName: "Elena Garcia",
                medicalConditions: "Hearing impairment",
                documents: {
                    birthCertificate: true,
                    medicalCertificate: true,
                    clientIdentification: false,
                    proofOfAddress: true,
                    idImage: true
                },
                dateSubmitted: "2024-01-20",
                status: "approved",
                additionalNotes: "Applicant works as a carpenter."
            },
            {
                id: 4,
                fullName: "Lourdes Reyes",
                applicationType: "senior",
                birthDate: "1952-07-30",
                contactNumber: "0945-333-4444",
                emailAddress: "lourdes.reyes@example.com",
                completeAddress: "321 Elm Street, Barangay Pinagbuhatan, Pasig City",
                emergencyContact: "0916-777-8888",
                emergencyContactName: "Jose Reyes",
                medicalConditions: "Diabetes, high blood pressure",
                documents: {
                    birthCertificate: true,
                    medicalCertificate: true,
                    clientIdentification: true,
                    proofOfAddress: true,
                    idImage: true
                },
                dateSubmitted: "2024-01-22",
                status: "rejected",
                additionalNotes: "Incomplete documentation submitted."
            }
        ];


        // Populate the applications table
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.getElementById('applicationsTableBody');
           
            applications.forEach(app => {
                const row = document.createElement('tr');
               
                // Format date for display
                const formattedDate = new Date(app.dateSubmitted).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
               
                // Format birth date for display
                const formattedBirthDate = new Date(app.birthDate).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
               
                // Status badge
                let statusClass = '';
                let statusText = '';
               
                switch(app.status) {
                    case 'pending':
                        statusClass = 'status-pending';
                        statusText = 'Pending';
                        break;
                    case 'approved':
                        statusClass = 'status-approved';
                        statusText = 'Approved';
                        break;
                    case 'rejected':
                        statusClass = 'status-rejected';
                        statusText = 'Rejected';
                        break;
                }
               
                row.innerHTML = `
                    <td><span class="name-link" data-id="${app.id}">${app.fullName}</span></td>
                    <td>${app.applicationType === 'pwd' ? 'PWD' : 'Senior Citizen'}</td>
                    <td>${formattedBirthDate}</td>
                    <td>${app.contactNumber}</td>
                    <td>${formattedDate}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                `;
               
                tableBody.appendChild(row);
            });
           
            // Add click event to name links
            const nameLinks = document.querySelectorAll('.name-link');
            nameLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const appId = parseInt(this.getAttribute('data-id'));
                    openApplicationModal(appId);
                });
            });
           
            // Modal functionality
            const modal = document.getElementById('applicationModal');
            const closeModalBtn = document.querySelector('.close-modal');
            const closeModalBtn2 = document.getElementById('closeModalBtn');
            const approveBtn = document.getElementById('approveBtn');
           
            closeModalBtn.addEventListener('click', closeModal);
            closeModalBtn2.addEventListener('click', closeModal);
           
            approveBtn.addEventListener('click', function() {
                const appId = parseInt(this.getAttribute('data-id'));
                approveApplication(appId);
            });
           
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });
           
            // Add click event to navigation items
            const navItems = document.querySelectorAll('.nav-links li');
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    navItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
       
        function openApplicationModal(appId) {
            const application = applications.find(app => app.id === appId);
           
            if (!application) return;
           
            // Populate modal with application data
            document.getElementById('applicantName').textContent = application.fullName;
            document.getElementById('modalFullName').value = application.fullName;
            document.getElementById('modalApplicationType').value = application.applicationType;
            document.getElementById('modalBirthDate').value = application.birthDate;
            document.getElementById('modalContactNumber').value = application.contactNumber;
            document.getElementById('modalCompleteAddress').value = application.completeAddress;
            document.getElementById('modalEmailAddress').value = application.emailAddress || '';
            document.getElementById('modalEmergencyContact').value = application.emergencyContact;
            document.getElementById('modalEmergencyContactName').value = application.emergencyContactName || '';
            document.getElementById('modalMedicalConditions').value = application.medicalConditions || '';
            document.getElementById('modalAdditionalNotes').value = application.additionalNotes || '';
           
            // Set document checkboxes
            document.getElementById('modalBirthCertificate').checked = application.documents.birthCertificate;
            document.getElementById('modalMedicalCertificate').checked = application.documents.medicalCertificate;
            document.getElementById('modalClientIdentification').checked = application.documents.clientIdentification;
            document.getElementById('modalProofOfAddress').checked = application.documents.proofOfAddress;
            document.getElementById('modalIdImage').checked = application.documents.idImage;
           
            // Set document verification results
            document.getElementById('modalBirthCertificateResult').textContent = application.documents.birthCertificate ? 'Verified ✅' : 'Not Verified ❌';
            document.getElementById('modalBirthCertificateResult').style.color = application.documents.birthCertificate ? 'var(--success)' : 'var(--accent)';
           
            document.getElementById('modalMedicalCertificateResult').textContent = application.documents.medicalCertificate ? 'Verified ✅' : 'Not Verified ❌';
            document.getElementById('modalMedicalCertificateResult').style.color = application.documents.medicalCertificate ? 'var(--success)' : 'var(--accent)';
           
            document.getElementById('modalClientIdentificationResult').textContent = application.documents.clientIdentification ? 'Verified ✅' : 'Not Verified ❌';
            document.getElementById('modalClientIdentificationResult').style.color = application.documents.clientIdentification ? 'var(--success)' : 'var(--accent)';
           
            document.getElementById('modalProofOfAddressResult').textContent = application.documents.proofOfAddress ? 'Verified ✅' : 'Not Verified ❌';
            document.getElementById('modalProofOfAddressResult').style.color = application.documents.proofOfAddress ? 'var(--success)' : 'var(--accent)';
           
            document.getElementById('modalIdImageResult').textContent = application.documents.idImage ? 'Verified ✅' : 'Not Verified ❌';
            document.getElementById('modalIdImageResult').style.color = application.documents.idImage ? 'var(--success)' : 'var(--accent)';
           
            // Set validated ID info
            document.getElementById('modalValidIdInfo').textContent = 'ID uploaded and verified';
            document.getElementById('modalValidIdResult').textContent = 'Document is LEGIT ✅';
            document.getElementById('modalValidIdResult').style.color = 'var(--success)';
           
            // Set data-id for approve button
            document.getElementById('approveBtn').setAttribute('data-id', appId);
           
            // Show modal
            document.getElementById('applicationModal').style.display = 'block';
        }
       
        function closeModal() {
            document.getElementById('applicationModal').style.display = 'none';
        }
       
        function approveApplication(appId) {
            if (confirm('Are you sure you want to approve this application?')) {
                // In a real app, this would make an API call to update the status
                const application = applications.find(app => app.id === appId);
                if (application) {
                    application.status = 'approved';
                    alert('Application approved successfully!');
                    closeModal();
                    // Refresh the table to show updated status
                    location.reload();
                }
            }
        }
    </script>
</body>
</html>

