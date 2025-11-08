<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/barangays_list.php';

header('Content-Type: application/json');

//
// 1. SECURITY AND INITIALIZATION
//

// Check if the user is logged in and has the correct role.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'department_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Generate a CSRF token if one doesn't exist for the session.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


//
// 2. REQUEST ROUTING (GET vs POST)
//

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // --- HANDLE GET REQUEST: Fetch user data ---
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'No user ID specified.']);
        exit;
    }
    
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    
    try {
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, username, role, barangay, profile_picture FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Add full path for profile picture for easier use on the client-side
            $profilePic = $user['profile_picture'] ?? 'default.jpg';
            $profilePicPath = '../images/profile_pictures/' . $profilePic;
            if (!file_exists($profilePicPath) || is_dir($profilePicPath)) {
                $profilePicPath = '../images/profile_pictures/default.jpg';
            }
            $user['profile_picture_path'] = $profilePicPath;
            
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
        }
    } catch (PDOException $e) {
        error_log("ERROR: edit_user.php (GET): " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error fetching user data.']);
    }

} elseif ($method === 'POST') {
    // --- HANDLE POST REQUEST: Update user data ---
    
    // CSRF token validation.
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'CSRF token validation failed. Please try again.']);
        exit;
    }

    $id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT) : null;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'User ID not provided.']);
        exit;
    }

    // Fetch the user first to ensure they exist
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Cannot update a user that does not exist.']);
        exit;
    }

    // Sanitize and validate form inputs.
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $username = trim($_POST['username'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $barangay = ($role === 'barangay_staff') ? trim($_POST['barangay'] ?? '') : null;
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    // --- Form Validation ---
    if (empty($firstName) || empty($lastName) || empty($username) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }
    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address format.']);
        exit;
    }
    if ($role === 'barangay_staff' && (empty($barangay) || !in_array($barangay, $barangays_list))) {
        echo json_encode(['success' => false, 'message' => 'A valid barangay is required for Barangay Staff.']);
        exit;
    }

    // Check for duplicate username or email (excluding the current user).
    $stmt = $conn->prepare("SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id");
    $stmt->execute(['username' => $username, 'email' => $email, 'id' => $id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username or email already exists for another user.']);
        exit;
    }

    // Validate password change.
    if (!empty($newPassword) && $newPassword !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'New password and confirm password do not match.']);
        exit;
    }

    // --- Profile Picture Upload Handling ---
    $profilePictureFileName = $user['profile_picture']; // Keep the old one by default.
    $uploadError = null;

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file['size'] > 5000000) { // 5MB limit
            $uploadError = 'Profile picture file is too large (Max 5MB).';
        } elseif (!in_array($fileExtension, $allowedExtensions)) {
            $uploadError = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
        } else {
            $uploadDir = '../images/profile_pictures/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $newFileName = md5(time() . $file['name']) . '.' . $fileExtension;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $profilePictureFileName = $newFileName;
            } else {
                $uploadError = 'Failed to move uploaded profile picture.';
            }
        }
        
        if ($uploadError) {
            echo json_encode(['success' => false, 'message' => $uploadError]);
            exit;
        }
    }

    // --- Database Update ---
    try {
        $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, username = :username, role = :role, barangay = :barangay, profile_picture = :profile_picture";
        
        $params = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'username' => $username,
            'role' => $role,
            'barangay' => $barangay,
            'profile_picture' => $profilePictureFileName,
            'id' => $id
        ];

        if (!empty($newPassword)) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute($params)) {
            // Regenerate CSRF token on successful update.
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            echo json_encode(['success' => true, 'message' => 'User updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to execute database update.']);
        }

    } catch (PDOException $e) {
        error_log("ERROR: edit_user.php (POST): " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error during update.']);
    }

} else {
    // --- HANDLE INVALID REQUEST METHOD ---
    echo json_encode(['success' => false, 'message' => 'Invalid request method. Only GET and POST are supported.']);
}

?>