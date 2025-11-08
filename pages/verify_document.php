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
        <?php include '../partials/department_sidebar.php'; ?>

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
                                                                        <input type="text" id="lastName" name="lastName" value="${application.lastName || ''}" required>                            </div>
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
                        <button type="button" class="btn btn-success" onclick="updateStatus(document.getElementById('applicationId').value, 'approved')"><i class="fas fa-check"></i> Approve</button>
                        <button type="button" class="btn btn-danger" onclick="updateStatus(document.getElementById('applicationId').value, 'rejected')"><i class="fas fa-times"></i> Reject</button>
                        <button type="button" class="btn btn-accent" onclick="exportApplicationDetails(document.getElementById('applicationId').value)"><i class="fas fa-download"></i> Export</button>
                    </div>
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
                                <button type="button" class="btn btn-success" onclick="updateStatus(${appId}, 'approved')"><i class="fas fa-check"></i> Approve</button>
                                <button type="button" class="btn btn-danger" onclick="updateStatus(${appId}, 'rejected')"><i class="fas fa-times"></i> Reject</button>
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
                                // Optionally refresh the table or update the specific row
                                location.reload(); // Reload the page to reflect changes
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
            const verb = status === 'approved' ? 'approve' : 'reject';
            if (confirm(`Are you sure you want to ${verb} this application?`)) {
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

        function exportApplicationDetails(appId) {
            window.open(`../api/export_application_pdf.php?id=${appId}`, '_blank');
        }
    </script>
</body>
</html>
