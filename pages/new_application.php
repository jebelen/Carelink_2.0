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
    $emailAddress = filter_input(INPUT_POST, 'emailAddress', FILTER_VALIDATE_EMAIL);
    $completeAddress = filter_input(INPUT_POST, 'completeAddress', FILTER_SANITIZE_STRING);
    $emergencyContact = filter_input(INPUT_POST, 'emergencyContact', FILTER_SANITIZE_STRING) ?? '';
    $emergencyContactName = filter_input(INPUT_POST, 'emergencyContactName', FILTER_SANITIZE_STRING) ?? '';
    $medicalConditions = filter_input(INPUT_POST, 'medicalConditions', FILTER_SANITIZE_STRING);
    $additionalNotes = filter_input(INPUT_POST, 'additionalNotes', FILTER_SANITIZE_STRING);
    $barangay = $_SESSION['barangay']; // Assuming barangay is stored in session

    $religion = filter_input(INPUT_POST, 'religion', FILTER_SANITIZE_STRING);
    $sex = filter_input(INPUT_POST, 'sex', FILTER_SANITIZE_STRING);
    $civilStatus = filter_input(INPUT_POST, 'civilStatus', FILTER_SANITIZE_STRING);
    $bloodType = filter_input(INPUT_POST, 'bloodType', FILTER_SANITIZE_STRING);

    $disabilityType = isset($_POST['disabilityType']) ? implode(', ', $_POST['disabilityType']) : null;
    $disabilityCause = isset($_POST['disabilityCause']) ? implode(', ', $_POST['disabilityCause']) : null;
    $educationalAttainment = filter_input(INPUT_POST, 'educationalAttainment', FILTER_SANITIZE_STRING);
    $employmentStatus = filter_input(INPUT_POST, 'employmentStatus', FILTER_SANITIZE_STRING);
    $occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_STRING);
    $sssNo = filter_input(INPUT_POST, 'sssNo', FILTER_SANITIZE_STRING);
    $gsisNo = filter_input(INPUT_POST, 'gsisNo', FILTER_SANITIZE_STRING);
    $pagibigNo = filter_input(INPUT_POST, 'pagibigNo', FILTER_SANITIZE_STRING);
    $philhealthNo = filter_input(INPUT_POST, 'philhealthNo', FILTER_SANITIZE_STRING);
    $fatherName = filter_input(INPUT_POST, 'fatherName', FILTER_SANITIZE_STRING);
    $motherName = filter_input(INPUT_POST, 'motherName', FILTER_SANITIZE_STRING);

    $placeOfBirth = filter_input(INPUT_POST, 'placeOfBirth', FILTER_SANITIZE_STRING);
    $yearsInPasig = filter_input(INPUT_POST, 'yearsInPasig', FILTER_SANITIZE_NUMBER_INT);
    $citizenship = filter_input(INPUT_POST, 'citizenship', FILTER_SANITIZE_STRING);

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
    $stmt = $conn->prepare("INSERT INTO applications (full_name, application_type, birth_date, contact_number, email_address, complete_address, emergency_contact, emergency_contact_name, medical_conditions, additional_notes, barangay, birth_certificate, birth_certificate_type, medical_certificate, medical_certificate_type, client_identification, client_identification_type, proof_of_address, proof_of_address_type, id_image, id_image_type, lastName, firstName, middleName, suffix, religion, sex, civilStatus, bloodType, disabilityType, disabilityCause, educationalAttainment, employmentStatus, occupation, sssNo, gsisNo, pagibigNo, philhealthNo, fatherName, motherName, placeOfBirth, yearsInPasig, citizenship) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$fullName, $applicationType, $birthDate, $contactNumber, $emailAddress, $completeAddress, $emergencyContact, $emergencyContactName, $medicalConditions, $additionalNotes, $barangay, $birthCertificate, $birthCertificateType, $medicalCertificate, $medicalCertificateType, $clientIdentification, $clientIdentificationType, $proofOfAddress, $proofOfAddressType, $idImage, $idImageType, $lastName, $firstName, $middleName, $suffix, $religion, $sex, $civilStatus, $bloodType, $disabilityType, $disabilityCause, $educationalAttainment, $employmentStatus, $occupation, $sssNo, $gsisNo, $pagibigNo, $philhealthNo, $fatherName, $motherName, $placeOfBirth, $yearsInPasig, $citizenship])) {
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

                    <div id="senior-fields" style="display: none;">
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
