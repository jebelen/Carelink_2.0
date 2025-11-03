<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $fullName = filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_STRING);
    $applicationType = filter_input(INPUT_POST, 'applicationType', FILTER_SANITIZE_STRING);
    $birthDate = filter_input(INPUT_POST, 'birthDate', FILTER_SANITIZE_STRING);
    $contactNumber = filter_input(INPUT_POST, 'contactNumber', FILTER_SANITIZE_STRING);
    $emailAddress = filter_input(INPUT_POST, 'emailAddress', FILTER_VALIDATE_EMAIL);
    $completeAddress = filter_input(INPUT_POST, 'completeAddress', FILTER_SANITIZE_STRING);
    $emergencyContact = filter_input(INPUT_POST, 'emergencyContact', FILTER_SANITIZE_STRING);
    $emergencyContactName = filter_input(INPUT_POST, 'emergencyContactName', FILTER_SANITIZE_STRING);
    $medicalConditions = filter_input(INPUT_POST, 'medicalConditions', FILTER_SANITIZE_STRING);
    $additionalNotes = filter_input(INPUT_POST, 'additionalNotes', FILTER_SANITIZE_STRING);
    $barangay = $_SESSION['barangay']; // Assuming barangay is stored in session

    $birthCertificate = isset($_FILES['birthCertificate']) && $_FILES['birthCertificate']['error'] == 0 ? file_get_contents($_FILES['birthCertificate']['tmp_name']) : null;
    $birthCertificateType = isset($_FILES['birthCertificate']) && $_FILES['birthCertificate']['error'] == 0 ? $_FILES['birthCertificate']['type'] : null;
    $medicalCertificate = isset($_FILES['medicalCertificate']) && $_FILES['medicalCertificate']['error'] == 0 ? file_get_contents($_FILES['medicalCertificate']['tmp_name']) : null;
    $medicalCertificateType = isset($_FILES['medicalCertificate']) && $_FILES['medicalCertificate']['error'] == 0 ? $_FILES['medicalCertificate']['type'] : null;
    $clientIdentification = isset($_FILES['clientIdentification']) && $_FILES['clientIdentification']['error'] == 0 ? file_get_contents($_FILES['clientIdentification']['tmp_name']) : null;
    $clientIdentificationType = isset($_FILES['clientIdentification']) && $_FILES['clientIdentification']['error'] == 0 ? $_FILES['clientIdentification']['type'] : null;
    $proofOfAddress = isset($_FILES['proofOfAddress']) && $_FILES['proofOfAddress']['error'] == 0 ? file_get_contents($_FILES['proofOfAddress']['tmp_name']) : null;
    $proofOfAddressType = isset($_FILES['proofOfAddress']) && $_FILES['proofOfAddress']['error'] == 0 ? $_FILES['proofOfAddress']['type'] : null;
    $idImage = isset($_FILES['idImage']) && $_FILES['idImage']['error'] == 0 ? file_get_contents($_FILES['idImage']['tmp_name']) : null;
    $idImageType = isset($_FILES['idImage']) && $_FILES['idImage']['error'] == 0 ? $_FILES['idImage']['type'] : null;

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO applications (full_name, application_type, birth_date, contact_number, email_address, complete_address, emergency_contact, emergency_contact_name, medical_conditions, additional_notes, barangay, birth_certificate, birth_certificate_type, medical_certificate, medical_certificate_type, client_identification, client_identification_type, proof_of_address, proof_of_address_type, id_image, id_image_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$fullName, $applicationType, $birthDate, $contactNumber, $emailAddress, $completeAddress, $emergencyContact, $emergencyContactName, $medicalConditions, $additionalNotes, $barangay, $birthCertificate, $birthCertificateType, $medicalCertificate, $medicalCertificateType, $clientIdentification, $clientIdentificationType, $proofOfAddress, $proofOfAddressType, $idImage, $idImageType])) {
        header("Location: Submit_Application.php?success=1");
        exit();
    } else {
        $errorMessage = "Error: " . $stmt->errorInfo()[2];
    }

    $stmt = null;
    $conn = null;
}
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPRAS - New Application</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/barangay-sidebar.css">
    <style>
        /* Using styles from Submit_Application.php */
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
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .application-form {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            font-size: 20px;
            color: var(--primary);
            margin-bottom: 20px;
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
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            background: var(--secondary);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include '../partials/barangay_sidebar.php'; ?>
        <div class="main-content">
            <div class="application-form">
                <form method="POST" action="new_application.php" enctype="multipart/form-data">
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Basic Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullName">Full Name</label>
                                <input type="text" id="fullName" name="fullName" required>
                            </div>
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
                                <label for="emergencyContact">Emergency Contact</label>
                                <input type="text" id="emergencyContact" name="emergencyContact" required>
                            </div>
                        </div>
                         <div class="form-group">
                            <label for="emergencyContactName">Emergency Contact Name</label>
                            <input type="text" id="emergencyContactName" name="emergencyContactName" required>
                        </div>
                        <div class="form-group">
                            <label for="medicalConditions">Medical Conditions</label>
                            <textarea id="medicalConditions" name="medicalConditions"></textarea>
                        </div>
                    </div>
                    <div class="form-section">
                        <h3><i class="fas fa-file-alt"></i> Required Documents</h3>
                        <div class="form-group">
                            <label for="birthCertificate">Birth Certificate</label>
                            <input type="file" id="birthCertificate" name="birthCertificate">
                        </div>
                        <div class="form-group">
                            <label for="medicalCertificate">Medical Certificate</label>
                            <input type="file" id="medicalCertificate" name="medicalCertificate">
                        </div>
                        <div class="form-group">
                            <label for="clientIdentification">Client Identification</label>
                            <input type="file" id="clientIdentification" name="clientIdentification">
                        </div>
                        <div class="form-group">
                            <label for="proofOfAddress">Proof of Address</label>
                            <input type="file" id="proofOfAddress" name="proofOfAddress">
                        </div>
                        <div class="form-group">
                            <label for="idImage">Updated ID Image</label>
                            <input type="file" id="idImage" name="idImage">
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
                        <button type="submit" class="btn">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
