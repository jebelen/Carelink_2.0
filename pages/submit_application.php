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
                    <button class="btn" id="importBtn"><i class="fas fa-upload"></i> Import Applications</button>
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php
                                $profilePic = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default.jpg';
                                $profilePicPath = '../images/profile_pictures/' . $profilePic;
                                if (!file_exists($profilePicPath) || is_dir($profilePicPath)) {
                                    $profilePicPath = '../images/profile_pictures/default.jpg'; // Fallback to default if file doesn't exist
                                }
                            ?>
                            <img src="<?php echo $profilePicPath; ?>" alt="Profile Picture" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
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
                <form id="applicationDetailForm" method="POST" action="../api/update_application.php" enctype="multipart/form-data">
                    <input type="hidden" id="applicationId" name="applicationId">
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Basic Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="applicationType">Application Type</label>
                                <select id="applicationType" name="applicationType" required>
                                    <option value="pwd">PWD</option>
                                    <option value="senior">Senior Citizen</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" id="lastName" name="lastName" required>
                            </div>
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" id="firstName" name="firstName" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="middleName">Middle Name</label>
                                <input type="text" id="middleName" name="middleName">
                            </div>
                            <div class="form-group">
                                <label for="suffix">Suffix</label>
                                <input type="text" id="suffix" name="suffix">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="birthDate">Birth Date</label>
                                <input type="date" id="birthDate" name="birthDate" required>
                            </div>
                            <div class="form-group">
                                <label for="contactNumber">Contact Number</label>
                                <input type="text" id="contactNumber" name="contactNumber" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="completeAddress">Complete Address</label>
                            <textarea id="completeAddress" name="completeAddress" required></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="emailAddress">Email Address</label>
                                <input type="email" id="emailAddress" name="emailAddress">
                            </div>
                            <div class="form-group">
                                <label for="religion">Religion</label>
                                <input type="text" id="religion" name="religion">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sex">Sex</label>
                                <select id="sex" name="sex">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="civilStatus">Civil Status</label>
                                <select id="civilStatus" name="civilStatus">
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widow/er">Widow/er</option>
                                    <option value="Cohabitation (Live-in)">Cohabitation (Live-in)</option>
                                </select>
                            </div>
                        </div>
                         <div class="form-row">
                            <div class="form-group">
                                <label for="bloodType">Blood Type</label>
                                <input type="text" id="bloodType" name="bloodType">
                            </div>
                        </div>
                    </div>

                    <div id="pwd-fields-modal">
                        <div class="form-section">
                            <h3><i class="fas fa-wheelchair"></i> PWD Specific Information</h3>
                            <div class="form-group">
                                <label>Type of Disability</label>
                                <div>
                                    <input type="checkbox" name="disabilityType[]" value="Deaf/Hard of Hearing"> Deaf/Hard of Hearing<br>
                                    <input type="checkbox" name="disabilityType[]" value="Intellectual Disability"> Intellectual Disability<br>
                                    <input type="checkbox" name="disabilityType[]" value="Learning Disability"> Learning Disability<br>
                                    <input type="checkbox" name="disabilityType[]" value="Mental Disability"> Mental Disability<br>
                                    <input type="checkbox" name="disabilityType[]" value="Orthopedic"> Orthopedic<br>
                                    <input type="checkbox" name="disabilityType[]" value="Physical Disability"> Physical Disability<br>
                                    <input type="checkbox" name="disabilityType[]" value="Psychosocial Disability"> Psychosocial Disability<br>
                                    <input type="checkbox" name="disabilityType[]" value="Speech and Language Impairment"> Speech and Language Impairment<br>
                                    <input type="checkbox" name="disabilityType[]" value="Visual Disability"> Visual Disability<br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Cause of Disability</label>
                                <div>
                                    <input type="checkbox" name="disabilityCause[]" value="Acquired"> Acquired<br>
                                    <input type="checkbox" name="disabilityCause[]" value="Cancer"> Cancer<br>
                                    <input type="checkbox" name="disabilityCause[]" value="Chronic Illness"> Chronic Illness<br>
                                    <input type="checkbox" name="disabilityCause[]" value="Congenital/Inborn"> Congenital/Inborn<br>
                                    <input type="checkbox" name="disabilityCause[]" value="Injury"> Injury<br>
                                    <input type="checkbox" name="disabilityCause[]" value="Autism"> Autism<br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Educational Attainment</label>
                                <div>
                                    <input type="radio" name="educationalAttainment" value="None"> None<br>
                                    <input type="radio" name="educationalAttainment" value="Elementary Education"> Elementary Education<br>
                                    <input type="radio" name="educationalAttainment" value="High School Education"> High School Education<br>
                                    <input type="radio" name="educationalAttainment" value="College"> College<br>
                                    <input type="radio" name="educationalAttainment" value="Post Graduate Program"> Post Graduate Program<br>
                                    <input type="radio" name="educationalAttainment" value="Non-Formal Education"> Non-Formal Education<br>
                                    <input type="radio" name="educationalAttainment" value="Vocational"> Vocational<br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Status of Employment</label>
                                <div>
                                    <input type="radio" name="employmentStatus" value="Employed"> Employed<br>
                                    <input type="radio" name="employmentStatus" value="Unemployed"> Unemployed<br>
                                    <input type="radio" name="employmentStatus" value="Self-employed"> Self-employed<br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="occupation">Occupation</label>
                                <select id="occupation" name="occupation">
                                    <option value="Managers">Managers</option>
                                    <option value="Professionals">Professionals</option>
                                    <option value="Technician and Associate Professionals">Technician and Associate Professionals</option>
                                    <option value="Clerical Support Workers">Clerical Support Workers</option>
                                    <option value="Service and Sales Workers">Service and Sales Workers</option>
                                    <option value="Skilled Agricultural, Forestry & Fishery Workers">Skilled Agricultural, Forestry & Fishery Workers</option>
                                    <option value="Plant and Machine Operators & Assemblers">Plant and Machine Operators & Assemblers</option>
                                    <option value="Elementary Occupations">Elementary Occupations</option>
                                    <option value="Armed Forces Occupations">Armed Forces Occupations</option>
                                    <option value="Others">Others, specify</option>
                                </select>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="sssNo">SSS No.</label>
                                    <input type="text" id="sssNo" name="sssNo">
                                </div>
                                <div class="form-group">
                                    <label for="gsisNo">GSIS No.</label>
                                    <input type="text" id="gsisNo" name="gsisNo">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="pagibigNo">Pag-ibig No.</label>
                                    <input type="text" id="pagibigNo" name="pagibigNo">
                                </div>
                                <div class="form-group">
                                    <label for="philhealthNo">Philhealth No.</label>
                                    <input type="text" id="philhealthNo" name="philhealthNo">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="fatherName">Father's Name</label>
                                <input type="text" id="fatherName" name="fatherName">
                            </div>
                            <div class="form-group">
                                <label for="motherName">Mother's Name</label>
                                <input type="text" id="motherName" name="motherName">
                            </div>
                        </div>
                    </div>

                    <div id="senior-fields-modal" style="display: none;">
                        <div class="form-section">
                            <h3><i class="fas fa-user-friends"></i> Senior Citizen Specific Information</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="placeOfBirth">Place of Birth</label>
                                    <input type="text" id="placeOfBirth" name="placeOfBirth">
                                </div>
                                <div class="form-group">
                                    <label for="yearsInPasig">No. of Years in Pasig</label>
                                    <input type="number" id="yearsInPasig" name="yearsInPasig">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="citizenship">Citizenship</label>
                                <input type="text" id="citizenship" name="citizenship">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-file-alt"></i> Required Documents</h3>
                        <div class="form-group">
                            <label for="birthCertificate">Birth Certificate</label>
                            <input type="file" id="birthCertificate" name="birthCertificate">
                            <p id="currentBirthCertificate"></p>
                        </div>
                        <div class="form-group">
                            <label for="medicalCertificate">Medical Certificate</label>
                            <input type="file" id="medicalCertificate" name="medicalCertificate">
                            <p id="currentMedicalCertificate"></p>
                        </div>
                        <div class="form-group">
                            <label for="clientIdentification">Client Identification</label>
                            <input type="file" id="clientIdentification" name="clientIdentification">
                            <p id="currentClientIdentification"></p>
                        </div>
                        <div class="form-group">
                            <label for="proofOfAddress">Proof of Address</label>
                            <input type="file" id="proofOfAddress" name="proofOfAddress">
                            <p id="currentProofOfAddress"></p>
                        </div>
                        <div class="form-group">
                            <label for="idImage">Updated ID Image</label>
                            <input type="file" id="idImage" name="idImage">
                            <p id="currentIdImage"></p>
                        </div>
                    </div>
                    <div class="form-section">
                        <h3><i class="fas fa-info-circle"></i> Additional Information</h3>
                        <div class="form-group">
                            <label for="additionalNotes">Additional Notes</label>
                            <textarea id="additionalNotes" name="additionalNotes"></textarea>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn"><i class="fas fa-save"></i> Save Changes</button>
                        <button type="button" class="btn btn-accent" onclick="exportApplicationDetails(document.getElementById('applicationId').value)"><i class="fas fa-download"></i> Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Applications Modal -->
    <div id="importModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Import Applications from CSV</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Download the responses from your Google Form as a CSV file and and upload it here to import the applications.</p>
                <div class="message">
                    <?php
                        if (isset($_SESSION['import_message'])) {
                            echo $_SESSION['import_message'];
                            unset($_SESSION['import_message']);
                        }
                    ?>
                </div>
                <form action="../api/import_applications.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="csv_file">Select CSV File</label>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn">Import Applications</button>
                </form>
            </div>
        </div>
    </div>


    <script src="../assets/js/sidebar-toggle.js"></script>
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

            const importBtn = document.getElementById('importBtn');
            const importModal = document.getElementById('importModal');
            const closeImportModalBtn = document.querySelector('#importModal .close-modal');

            importBtn.addEventListener('click', () => {
                importModal.style.display = 'block';
            });

            closeImportModalBtn.addEventListener('click', () => {
                importModal.style.display = 'none';
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

                    // Re-render the form with fetched data
                    modalBody.innerHTML = `
                        <form id="applicationDetailForm" method="POST" action="../api/update_application.php" enctype="multipart/form-data">
                            <input type="hidden" id="applicationId" name="applicationId" value="${application.id}">
                            <div class="form-section">
                                <h3><i class="fas fa-user"></i> Basic Information</h3>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="applicationType">Application Type</label>
                                        <select id="applicationType" name="applicationType" required>
                                            <option value="pwd" ${application.application_type === 'pwd' ? 'selected' : ''}>PWD</option>
                                            <option value="senior" ${application.application_type === 'senior' ? 'selected' : ''}>Senior Citizen</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="lastName">Last Name</label>
                                        <input type="text" id="lastName" name="lastName" value="${application.lastName || ''}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="firstName">First Name</label>
                                        <input type="text" id="firstName" name="firstName" value="${application.firstName || ''}" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="middleName">Middle Name</label>
                                        <input type="text" id="middleName" name="middleName" value="${application.middleName || ''}">
                                    </div>
                                    <div class="form-group">
                                        <label for="suffix">Suffix</label>
                                        <input type="text" id="suffix" name="suffix" value="${application.suffix || ''}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="birthDate">Birth Date</label>
                                        <input type="date" id="birthDate" name="birthDate" value="${application.birth_date || ''}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contactNumber">Contact Number</label>
                                        <input type="text" id="contactNumber" name="contactNumber" value="${application.contact_number || ''}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="completeAddress">Complete Address</label>
                                    <textarea id="completeAddress" name="completeAddress" required>${application.complete_address || ''}</textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="emailAddress">Email Address</label>
                                        <input type="email" id="emailAddress" name="emailAddress" value="${application.email_address || ''}">
                                    </div>
                                    <div class="form-group">
                                        <label for="religion">Religion</label>
                                        <input type="text" id="religion" name="religion" value="${application.religion || ''}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="sex">Sex</label>
                                        <select id="sex" name="sex">
                                            <option value="Male" ${application.sex === 'Male' ? 'selected' : ''}>Male</option>
                                            <option value="Female" ${application.sex === 'Female' ? 'selected' : ''}>Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="civilStatus">Civil Status</label>
                                        <select id="civilStatus" name="civilStatus">
                                            <option value="Single" ${application.civilStatus === 'Single' ? 'selected' : ''}>Single</option>
                                            <option value="Married" ${application.civilStatus === 'Married' ? 'selected' : ''}>Married</option>
                                            <option value="Widow/er" ${application.civilStatus === 'Widow/er' ? 'selected' : ''}>Widow/er</option>
                                            <option value="Cohabitation (Live-in)" ${application.civilStatus === 'Cohabitation (Live-in)' ? 'selected' : ''}>Cohabitation (Live-in)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="bloodType">Blood Type</label>
                                        <input type="text" id="bloodType" name="bloodType" value="${application.bloodType || ''}">
                                    </div>
                                </div>
                            </div>

                            <div id="pwd-fields-modal" style="display: ${application.application_type === 'pwd' ? 'block' : 'none'}">
                                <div class="form-section">
                                    <h3><i class="fas fa-wheelchair"></i> PWD Specific Information</h3>
                                    <div class="form-group">
                                        <label>Type of Disability</label>
                                        <div>
                                            <input type="checkbox" name="disabilityType[]" value="Deaf/Hard of Hearing" ${application.disabilityType && application.disabilityType.includes('Deaf/Hard of Hearing') ? 'checked' : ''}> Deaf/Hard of Hearing<br>
                                            <input type="checkbox" name="disabilityType[]" value="Intellectual Disability" ${application.disabilityType && application.disabilityType.includes('Intellectual Disability') ? 'checked' : ''}> Intellectual Disability<br>
                                            <input type="checkbox" name="disabilityType[]" value="Learning Disability" ${application.disabilityType && application.disabilityType.includes('Learning Disability') ? 'checked' : ''}> Learning Disability<br>
                                            <input type="checkbox" name="disabilityType[]" value="Mental Disability" ${application.disabilityType && application.disabilityType.includes('Mental Disability') ? 'checked' : ''}> Mental Disability<br>
                                            <input type="checkbox" name="disabilityType[]" value="Orthopedic" ${application.disabilityType && application.disabilityType.includes('Orthopedic') ? 'checked' : ''}> Orthopedic<br>
                                            <input type="checkbox" name="disabilityType[]" value="Physical Disability" ${application.disabilityType && application.disabilityType.includes('Physical Disability') ? 'checked' : ''}> Physical Disability<br>
                                            <input type="checkbox" name="disabilityType[]" value="Psychosocial Disability" ${application.disabilityType && application.disabilityType.includes('Psychosocial Disability') ? 'checked' : ''}> Psychosocial Disability<br>
                                            <input type="checkbox" name="disabilityType[]" value="Speech and Language Impairment" ${application.disabilityType && application.disabilityType.includes('Speech and Language Impairment') ? 'checked' : ''}> Speech and Language Impairment<br>
                                            <input type="checkbox" name="disabilityType[]" value="Visual Disability" ${application.disabilityType && application.disabilityType.includes('Visual Disability') ? 'checked' : ''}> Visual Disability<br>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Cause of Disability</label>
                                        <div>
                                            <input type="checkbox" name="disabilityCause[]" value="Acquired" ${application.disabilityCause && application.disabilityCause.includes('Acquired') ? 'checked' : ''}> Acquired<br>
                                            <input type="checkbox" name="disabilityCause[]" value="Cancer" ${application.disabilityCause && application.disabilityCause.includes('Cancer') ? 'checked' : ''}> Cancer<br>
                                            <input type="checkbox" name="disabilityCause[]" value="Chronic Illness" ${application.disabilityCause && application.disabilityCause.includes('Chronic Illness') ? 'checked' : ''}> Chronic Illness<br>
                                            <input type="checkbox" name="disabilityCause[]" value="Congenital/Inborn" ${application.disabilityCause && application.disabilityCause.includes('Congenital/Inborn') ? 'checked' : ''}> Congenital/Inborn<br>
                                            <input type="checkbox" name="disabilityCause[]" value="Injury" ${application.disabilityCause && application.disabilityCause.includes('Injury') ? 'checked' : ''}> Injury<br>
                                            <input type="checkbox" name="disabilityCause[]" value="Autism" ${application.disabilityCause && application.disabilityCause.includes('Autism') ? 'checked' : ''}> Autism<br>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Educational Attainment</label>
                                        <div>
                                            <input type="radio" name="educationalAttainment" value="None" ${application.educationalAttainment === 'None' ? 'checked' : ''}> None<br>
                                            <input type="radio" name="educationalAttainment" value="Elementary Education" ${application.educationalAttainment === 'Elementary Education' ? 'checked' : ''}> Elementary Education<br>
                                            <input type="radio" name="educationalAttainment" value="High School Education" ${application.educationalAttainment === 'High School Education' ? 'checked' : ''}> High School Education<br>
                                            <input type="radio" name="educationalAttainment" value="College" ${application.educationalAttainment === 'College' ? 'checked' : ''}> College<br>
                                            <input type="radio" name="educationalAttainment" value="Post Graduate Program" ${application.educationalAttainment === 'Post Graduate Program' ? 'checked' : ''}> Post Graduate Program<br>
                                            <input type="radio" name="educationalAttainment" value="Non-Formal Education" ${application.educationalAttainment === 'Non-Formal Education' ? 'checked' : ''}> Non-Formal Education<br>
                                            <input type="radio" name="educationalAttainment" value="Vocational" ${application.educationalAttainment === 'Vocational' ? 'checked' : ''}> Vocational<br>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Status of Employment</label>
                                        <div>
                                            <input type="radio" name="employmentStatus" value="Employed" ${application.employmentStatus === 'Employed' ? 'checked' : ''}> Employed<br>
                                            <input type="radio" name="employmentStatus" value="Unemployed" ${application.employmentStatus === 'Unemployed' ? 'checked' : ''}> Unemployed<br>
                                            <input type="radio" name="employmentStatus" value="Self-employed" ${application.employmentStatus === 'Self-employed' ? 'checked' : ''}> Self-employed<br>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="occupation">Occupation</label>
                                        <select id="occupation" name="occupation">
                                            <option value="Managers" ${application.occupation === 'Managers' ? 'selected' : ''}>Managers</option>
                                            <option value="Professionals" ${application.occupation === 'Professionals' ? 'selected' : ''}>Professionals</option>
                                            <option value="Technician and Associate Professionals" ${application.occupation === 'Technician and Associate Professionals' ? 'selected' : ''}>Technician and Associate Professionals</option>
                                            <option value="Clerical Support Workers" ${application.occupation === 'Clerical Support Workers' ? 'selected' : ''}>Clerical Support Workers</option>
                                            <option value="Service and Sales Workers" ${application.occupation === 'Service and Sales Workers' ? 'selected' : ''}>Service and Sales Workers</option>
                                            <option value="Skilled Agricultural, Forestry & Fishery Workers" ${application.occupation === 'Skilled Agricultural, Forestry & Fishery Workers' ? 'selected' : ''}>Skilled Agricultural, Forestry & Fishery Workers</option>
                                            <option value="Plant and Machine Operators & Assemblers" ${application.occupation === 'Plant and Machine Operators & Assemblers' ? 'selected' : ''}>Plant and Machine Operators & Assemblers</option>
                                            <option value="Elementary Occupations" ${application.occupation === 'Elementary Occupations' ? 'selected' : ''}>Elementary Occupations</option>
                                            <option value="Armed Forces Occupations" ${application.occupation === 'Armed Forces Occupations' ? 'selected' : ''}>Armed Forces Occupations</option>
                                            <option value="Others" ${application.occupation === 'Others' ? 'selected' : ''}>Others, specify</option>
                                        </select>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="sssNo">SSS No.</label>
                                            <input type="text" id="sssNo" name="sssNo" value="${application.sssNo || ''}">
                                        </div>
                                        <div class="form-group">
                                            <label for="gsisNo">GSIS No.</label>
                                            <input type="text" id="gsisNo" name="gsisNo" value="${application.gsisNo || ''}">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="pagibigNo">Pag-ibig No.</label>
                                            <input type="text" id="pagibigNo" name="pagibigNo" value="${application.pagibigNo || ''}">
                                        </div>
                                        <div class="form-group">
                                            <label for="philhealthNo">Philhealth No.</label>
                                            <input type="text" id="philhealthNo" name="philhealthNo" value="${application.philhealthNo || ''}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="fatherName">Father's Name</label>
                                        <input type="text" id="fatherName" name="fatherName" value="${application.fatherName || ''}">
                                    </div>
                                    <div class="form-group">
                                        <label for="motherName">Mother's Name</label>
                                        <input type="text" id="motherName" name="motherName" value="${application.motherName || ''}">
                                    </div>
                                </div>
                            </div>

                            <div id="senior-fields-modal" style="display: ${application.application_type === 'senior' ? 'block' : 'none'}">
                                <div class="form-section">
                                    <h3><i class="fas fa-user-friends"></i> Senior Citizen Specific Information</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="placeOfBirth">Place of Birth</label>
                                            <input type="text" id="placeOfBirth" name="placeOfBirth" value="${application.placeOfBirth || ''}">
                                        </div>
                                        <div class="form-group">
                                            <label for="yearsInPasig">No. of Years in Pasig</label>
                                            <input type="number" id="yearsInPasig" name="yearsInPasig" value="${application.yearsInPasig || ''}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="citizenship">Citizenship</label>
                                        <input type="text" id="citizenship" name="citizenship" value="${application.citizenship || ''}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3><i class="fas fa-file-alt"></i> Required Documents</h3>
                                <div class="form-group">
                                    <label for="birthCertificate">Birth Certificate</label>
                                    <input type="file" id="birthCertificate" name="birthCertificate">
                                    <p id="currentBirthCertificate">${application.has_birth_certificate ? `<a href="../api/get_document.php?id=${appId}&doc_type=birth_certificate" target="_blank">View Current Birth Certificate</a>` : 'No document uploaded'}</p>
                                </div>
                                <div class="form-group">
                                    <label for="medicalCertificate">Medical Certificate</label>
                                    <input type="file" id="medicalCertificate" name="medicalCertificate">
                                    <p id="currentMedicalCertificate">${application.has_medical_certificate ? `<a href="../api/get_document.php?id=${appId}&doc_type=medical_certificate" target="_blank">View Current Medical Certificate</a>` : 'No document uploaded'}</p>
                                </div>
                                <div class="form-group">
                                    <label for="clientIdentification">Client Identification</label>
                                    <input type="file" id="clientIdentification" name="clientIdentification">
                                    <p id="currentClientIdentification">${application.has_client_identification ? `<a href="../api/get_document.php?id=${appId}&doc_type=client_identification" target="_blank">View Current Client Identification</a>` : 'No document uploaded'}</p>
                                </div>
                                <div class="form-group">
                                    <label for="proofOfAddress">Proof of Address</label>
                                    <input type="file" id="proofOfAddress" name="proofOfAddress">
                                    <p id="currentProofOfAddress">${application.has_proof_of_address ? `<a href="../api/get_document.php?id=${appId}&doc_type=proof_of_address" target="_blank">View Current Proof of Address</a>` : 'No document uploaded'}</p>
                                </div>
                                <div class="form-group">
                                    <label for="idImage">Updated ID Image</label>
                                    <input type="file" id="idImage" name="idImage">
                                    <p id="currentIdImage">${application.has_id_image ? `<a href="../api/get_document.php?id=${appId}&doc_type=id_image" target="_blank">View Current ID Image</a>` : 'No document uploaded'}</p>
                                </div>
                            </div>
                            <div class="form-section">
                                <h3><i class="fas fa-info-circle"></i> Additional Information</h3>
                                <div class="form-group">
                                    <label for="additionalNotes">Additional Notes</label>
                                    <textarea id="additionalNotes" name="additionalNotes">${application.additional_notes || ''}</textarea>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn"><i class="fas fa-save"></i> Save Changes</button>
                                <button type="button" class="btn btn-accent" onclick="exportApplicationDetails(${appId})"><i class="fas fa-download"></i> Export</button>
                            </div>
                        </form>
                    `;

                    // Add event listener for application type change within the modal
                    document.getElementById('applicationType').addEventListener('change', function () {
                        if (this.value === 'pwd') {
                            document.getElementById('pwd-fields-modal').style.display = 'block';
                            document.getElementById('senior-fields-modal').style.display = 'none';
                        } else if (this.value === 'senior') {
                            document.getElementById('pwd-fields-modal').style.display = 'none';
                            document.getElementById('senior-fields-modal').style.display = 'block';
                        }
                    });

                    // Handle form submission for updating application
                    document.getElementById('applicationDetailForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const form = e.target;
                        const formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                document.getElementById('applicationModal').style.display = 'none';
                                fetchApplications(); // Refresh the table
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error updating application:', error);
                            alert('An error occurred while updating the application.');
                        });
                    });

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

        function exportApplicationDetails(appId) {
            window.open(`../api/export_application_pdf.php?id=${appId}`, '_blank');
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
                                    <td><button class="btn btn-danger btn-small" onclick="deleteApplication(${app.id})">Delete</button></td>
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