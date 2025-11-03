<?php
require_once '../includes/db_connect.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM applications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete application.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No application ID provided.']);
}
?>