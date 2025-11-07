<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appId = filter_input(INPUT_POST, 'applicationId', FILTER_SANITIZE_NUMBER_INT);

    if (!$appId) {
        echo json_encode(['success' => false, 'message' => 'Application ID is missing.']);
        exit();
    }

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

    // Fetch existing application data to retain current document paths if no new file is uploaded
    $stmt = $conn->prepare("SELECT birth_certificate, medical_certificate, client_identification, proof_of_address, id_image FROM applications WHERE id = ?");
    $stmt->execute([$appId]);
    $existingApplication = $stmt->fetch(PDO::FETCH_ASSOC);

    $birthCertificate = $existingApplication['birth_certificate'];
    $birthCertificateType = null;
    if (isset($_FILES['birthCertificate']) && $_FILES['birthCertificate']['error'] == 0) {
        $birthCertificate = file_get_contents($_FILES['birthCertificate']['tmp_name']);
        $birthCertificateType = $_FILES['birthCertificate']['type'];
    }

    $medicalCertificate = $existingApplication['medical_certificate'];
    $medicalCertificateType = null;
    if (isset($_FILES['medicalCertificate']) && $_FILES['medicalCertificate']['error'] == 0) {
        $medicalCertificate = file_get_contents($_FILES['medicalCertificate']['tmp_name']);
        $medicalCertificateType = $_FILES['medicalCertificate']['type'];
    }

    $clientIdentification = $existingApplication['client_identification'];
    $clientIdentificationType = null;
    if (isset($_FILES['clientIdentification']) && $_FILES['clientIdentification']['error'] == 0) {
        $clientIdentification = file_get_contents($_FILES['clientIdentification']['tmp_name']);
        $clientIdentificationType = $_FILES['clientIdentification']['type'];
    }

    $proofOfAddress = $existingApplication['proof_of_address'];
    $proofOfAddressType = null;
    if (isset($_FILES['proofOfAddress']) && $_FILES['proofOfAddress']['error'] == 0) {
        $proofOfAddress = file_get_contents($_FILES['proofOfAddress']['tmp_name']);
        $proofOfAddressType = $_FILES['proofOfAddress']['type'];
    }

    $idImage = $existingApplication['id_image'];
    $idImageType = null;
    if (isset($_FILES['idImage']) && $_FILES['idImage']['error'] == 0) {
        $idImage = file_get_contents($_FILES['idImage']['tmp_name']);
        $idImageType = $_FILES['idImage']['type'];
    }

    // Prepare and bind for update
    $stmt = $conn->prepare("UPDATE applications SET full_name = ?, application_type = ?, birth_date = ?, contact_number = ?, email_address = ?, complete_address = ?, emergency_contact = ?, emergency_contact_name = ?, medical_conditions = ?, additional_notes = ?, birth_certificate = ?, birth_certificate_type = ?, medical_certificate = ?, medical_certificate_type = ?, client_identification = ?, client_identification_type = ?, proof_of_address = ?, proof_of_address_type = ?, id_image = ?, id_image_type = ?, lastName = ?, firstName = ?, middleName = ?, suffix = ?, religion = ?, sex = ?, civilStatus = ?, bloodType = ?, disabilityType = ?, disabilityCause = ?, educationalAttainment = ?, employmentStatus = ?, occupation = ?, sssNo = ?, gsisNo = ?, pagibigNo = ?, philhealthNo = ?, fatherName = ?, motherName = ?, placeOfBirth = ?, yearsInPasig = ?, citizenship = ? WHERE id = ?");
    
    if ($stmt->execute([$fullName, $applicationType, $birthDate, $contactNumber, $emailAddress, $completeAddress, $emergencyContact, $emergencyContactName, $medicalConditions, $additionalNotes, $birthCertificate, $birthCertificateType, $medicalCertificate, $medicalCertificateType, $clientIdentification, $clientIdentificationType, $proofOfAddress, $proofOfAddressType, $idImage, $idImageType, $lastName, $firstName, $middleName, $suffix, $religion, $sex, $civilStatus, $bloodType, $disabilityType, $disabilityCause, $educationalAttainment, $employmentStatus, $occupation, $sssNo, $gsisNo, $pagibigNo, $philhealthNo, $fatherName, $motherName, $placeOfBirth, $yearsInPasig, $citizenship, $appId])) {
        echo json_encode(['success' => true, 'message' => 'Application updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating application: ' . $stmt->errorInfo()[2]]);
    }

    $stmt = null;
    $conn = null;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>