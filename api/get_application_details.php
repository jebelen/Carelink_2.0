<?php
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

try {
    $appId = $_GET['id'];

    $sql = "SELECT id, full_name, application_type, birth_date, contact_number, email_address, complete_address, emergency_contact, emergency_contact_name, medical_conditions, date_submitted, status, additional_notes, barangay, (birth_certificate IS NOT NULL) as has_birth_certificate, (medical_certificate IS NOT NULL) as has_medical_certificate, (client_identification IS NOT NULL) as has_client_identification, (proof_of_address IS NOT NULL) as has_proof_of_address, (id_image IS NOT NULL) as has_id_image FROM applications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$appId]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($application);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$stmt = null;
$conn = null;
?>