<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/db_connect.php';

// Authenticate and authorize
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'barangay_staff' || !isset($_SESSION['barangay'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$barangay = $_SESSION['barangay'];
$response = [
    'success' => true,
    'data' => [
        'stats' => [],
        'monthly' => [],
        'notifications' => []
    ]
];

try {
    // 1. Get data for Status Distribution Chart
    $statusStmt = $conn->prepare("
        SELECT 
            status, 
            COUNT(*) as count 
        FROM applications 
        WHERE barangay = :barangay 
        GROUP BY status
    ");
    $statusStmt->execute(['barangay' => $barangay]);
    $response['data']['stats'] = $statusStmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Get data for Monthly Applications Chart (last 12 months)
    $monthlyStmt = $conn->prepare("
        SELECT 
            MONTHNAME(date_submitted) as month,
            application_type,
            COUNT(*) as count
        FROM applications
        WHERE barangay = :barangay AND date_submitted >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY MONTH(date_submitted), MONTHNAME(date_submitted), application_type
        ORDER BY MIN(date_submitted)
    ");
    $monthlyStmt->execute(['barangay' => $barangay]);
    $response['data']['monthly'] = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Get recent applications for notifications list
    $notifStmt = $conn->prepare("
        SELECT 
            id,
            full_name,
            application_type,
            status,
            date_submitted
        FROM applications
        WHERE barangay = :barangay
        ORDER BY date_submitted DESC
        LIMIT 5
    ");
    $notifStmt->execute(['barangay' => $barangay]);
    $response['data']['notifications'] = $notifStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = 'Database error: ' . $e->getMessage();
    error_log("API Error in barangay_dashboard_data.php: " . $e->getMessage());
}

echo json_encode($response);
?>