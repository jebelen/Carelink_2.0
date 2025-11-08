<?php
session_start(); // Start the session to access $_SESSION variables
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

$search_query = isset($_GET['query']) ? $_GET['query'] : '';
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_barangay = isset($_GET['barangay']) ? $_GET['barangay'] : ''; // Allow explicit barangay filter

// Enforce barangay filter for barangay_staff
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'barangay_staff') {
    $filter_barangay = $_SESSION['barangay'];
}

$sql = "SELECT id, full_name, application_type, birth_date, contact_number, date_submitted, status, complete_address FROM applications";
$params = [];
$where_clauses = [];

if (!empty($search_query)) {
    $where_clauses[] = "(full_name LIKE ? OR application_type LIKE ? OR complete_address LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

if (!empty($filter_type)) {
    $where_clauses[] = "application_type = ?";
    $params[] = $filter_type;
}

if (!empty($filter_status)) {
    $where_clauses[] = "status = ?";
    $params[] = $filter_status;
}

if (!empty($filter_barangay)) {
    $where_clauses[] = "barangay = ?";
    $params[] = $filter_barangay;
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY date_submitted DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($applications);
?>