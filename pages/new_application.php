<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
    $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
    $middleName = filter_input(INPUT_POST, 'middleName', FILTER_SANITIZE_STRING);
    $suffix = filter_input(INPUT_POST, 'suffix', FILTER_SANITIZE_STRING);
    $fullName = trim($firstName . ' ' . $middleName . ' ' . $lastName . ' ' . $suffix);
    $applicationType = filter_input(INPUT_POST, 'applicationType', FILTER_SANITIZE_STRING);
    $birthDate = filter_input(INPUT_POST, 'birthDate', FILTER_SANITIZE_STRING);
    $contactNumber = filter_input(INPUT_POST, 'contactNumber', FILTER_SANITIZE_STRING);
    $completeAddress = filter_input(INPUT_POST, 'completeAddress', FILTER_SANITIZE_STRING);
    $emergencyContact = filter_input(INPUT_POST, 'emergencyContact', FILTER_SANITIZE_STRING) ?? '';
    $emergencyContactName = filter_input(INPUT_POST, 'emergencyContactName', FILTER_SANITIZE_STRING) ?? '';
    $barangay = $_SESSION['barangay']; // Assuming barangay is stored in session

    $disabilityType = isset($_POST['disabilityType']) ? implode(', ', $_POST['disabilityType']) : null;
    $idNumber = filter_input(INPUT_POST, 'idNumber', FILTER_SANITIZE_STRING);
    $pwdIdIssueDate = filter_input(INPUT_POST, 'pwdIdIssueDate', FILTER_SANITIZE_STRING);
    $pwdIdExpiryDate = filter_input(INPUT_POST, 'pwdIdExpiryDate', FILTER_SANITIZE_STRING);

    $proofOfAddress = isset($_FILES['proofOfAddress']) && $_FILES['proofOfAddress']['error'] == 0 ? file_get_contents($_FILES['proofOfAddress']['tmp_name']) : null;
    $proofOfAddressType = isset($_FILES['proofOfAddress']) && $_FILES['proofOfAddress']['error'] == 0 ? $_FILES['proofOfAddress']['type'] : null;
    $idImage = isset($_FILES['idImage']) && $_FILES['idImage']['error'] == 0 ? file_get_contents($_FILES['idImage']['tmp_name']) : null;
    $idImageType = isset($_FILES['idImage']) && $_FILES['idImage']['error'] == 0 ? $_FILES['idImage']['type'] : null;

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO applications (full_name, application_type, birth_date, contact_number, complete_address, emergency_contact, emergency_contact_name, barangay, disabilityType, id_number, pwd_id_issue_date, pwd_id_expiry_date, proof_of_address, proof_of_address_type, id_image, id_image_type, lastName, firstName, middleName, suffix) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt->execute([$fullName, $applicationType, $birthDate, $contactNumber, $completeAddress, $emergencyContact, $emergencyContactName, $barangay, $disabilityType, $idNumber, $pwdIdIssueDate, $pwdIdExpiryDate, $proofOfAddress, $proofOfAddressType, $idImage, $idImageType, $lastName, $firstName, $middleName, $suffix])) {
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
</head>
<body>
    <div class="container">
        <?php include '../partials/barangay_sidebar.php'; ?>
        <div class="main-content">
            <div class="header">
                <h1>New Application</h1>
            </div>
            <div class="application-form">
                <form method="POST" action="new_application.php" enctype="multipart/form-data">
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
                            <div class="form-group">
                                <label for="idNumber">ID Number</label>
                                <input type="text" id="idNumber" name="idNumber">
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
                                <label for="emergencyContactName">Emergency Contact Name</label>
                                <input type="text" id="emergencyContactName" name="emergencyContactName">
                            </div>
                            <div class="form-group">
                                <label for="emergencyContact">Emergency Contact Number</label>
                                <input type="text" id="emergencyContact" name="emergencyContact">
                            </div>
                        </div>
                    </div>

                    <div id="pwd-fields">
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
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="pwdIdIssueDate">ID Issue Date</label>
                                    <input type="date" id="pwdIdIssueDate" name="pwdIdIssueDate">
                                </div>
                                <div class="form-group">
                                    <label for="pwdIdExpiryDate">ID Expiry Date</label>
                                    <input type="date" id="pwdIdExpiryDate" name="pwdIdExpiryDate">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="senior-fields" style="display: none;">
                        <!-- Senior Citizen specific fields are covered by Basic Information as per user's request -->
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-file-alt"></i> Required Documents</h3>
                        <div class="form-group">
                            <label for="proofOfAddress">Proof of Address</label>
                            <input type="file" id="proofOfAddress" name="proofOfAddress">
                        </div>
                        <div class="form-group">
                            <label for="idImage">ID Image</label>
                            <input type="file" id="idImage" name="idImage">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../assets/js/sidebar-toggle.js"></script>
</body>
<script>
    document.getElementById('applicationType').addEventListener('change', function () {
        if (this.value === 'pwd') {
            document.getElementById('pwd-fields').style.display = 'block';
            document.getElementById('senior-fields').style.display = 'none';
        } else if (this.value === 'senior') {
            document.getElementById('pwd-fields').style.display = 'none';
            document.getElementById('senior-fields').style.display = 'block';
        }
    });
</script>
</html> 