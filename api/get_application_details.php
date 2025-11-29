<?php
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

try {
    $appId = $_GET['id'];

    $sql = "SELECT id_number, full_name, application_type, birth_date, contact_number, complete_address, emergency_contact, emergency_contact_name, date_submitted, status, barangay, disability_type, (proof_of_address IS NOT NULL) as has_proof_of_address, (id_image IS NOT NULL) as has_id_image, lastName, firstName, middleName, suffix FROM applications WHERE id_number = ?";
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