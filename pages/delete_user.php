<?php
session_start();
require_once '../includes/db_connect.php';

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'department_admin') {
    header('Location: ../index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    if ($id) {
        try {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = 'User deleted successfully!';
            } else {
                $_SESSION['error'] = 'User not found or could not be deleted.';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = 'Invalid user ID.';
    }
} else {
    $_SESSION['error'] = 'No user ID provided.';
}

header('Location: User_Management.php');
exit;
?>