<?php
header('Content-Type: application/json');
session_start();

require_once '../includes/db_connect.php';

$response = [
    'status' => 'success',
    'timestamp' => date('Y-m-d H:i:s'),
    'data' => [
        'total_applications' => 150,
        'pending_applications' => 25,
        'verified_applications' => 100,
        'rejected_applications' => 10,
        'notifications' => [
            ['id' => 1, 'message' => 'New application received from Barangay Maybunga.', 'time' => '5 minutes ago', 'type' => 'new_application'],
            ['id' => 2, 'message' => 'Document verification pending for 3 applications.', 'time' => '1 hour ago', 'type' => 'pending_verification'],
        ]
    ]
];

// In a real application, you would fetch actual data from the database here.
// Example:
// try {
//     $stmt = $conn->prepare("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
//     $stmt->execute();
//     $pending_applications = $stmt->fetchColumn();
//     $response['data']['pending_applications'] = $pending_applications;
// } catch (PDOException $e) {
//     $response['status'] = 'error';
//     $response['message'] = 'Database error: ' . $e->getMessage();
// }

echo json_encode($response);
?>