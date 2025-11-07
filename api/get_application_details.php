<?php
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

try {
    $appId = $_GET['id'];

    $sql = "SELECT id, full_name, application_type, birth_date, contact_number, email_address, complete_address, emergency_contact, emergency_contact_name, medical_conditions, date_submitted, status, additional_notes, barangay, (birth_certificate IS NOT NULL) as has_birth_certificate, (medical_certificate IS NOT NULL) as has_medical_certificate, (client_identification IS NOT NULL) as has_client_identification, (proof_of_address IS NOT NULL) as has_proof_of_address, (id_image IS NOT NULL) as has_id_image, lastName, firstName, middleName, suffix, religion, sex, civilStatus, bloodType, disabilityType, disabilityCause, educationalAttainment, employmentStatus, occupation, sssNo, gsisNo, pagibigNo, philhealthNo, fatherName, motherName, placeOfBirth, yearsInPasig, citizenship FROM applications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$appId]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    // Convert comma-separated strings to arrays for disability types and causes
    if ($application && isset($application['disabilityType']) && $application['disabilityType']) {
        $application['disabilityType'] = explode(',', $application['disabilityType']);
    } else {
        $application['disabilityType'] = [];
    }
    if ($application && isset($application['disabilityCause']) && $application['disabilityCause']) {
        $application['disabilityCause'] = explode(',', $application['disabilityCause']);
    } else {
        $application['disabilityCause'] = [];
    }

    echo json_encode($application);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$stmt = null;
$conn = null;
?>