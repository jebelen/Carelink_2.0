<?php
session_start();
require_once '../includes/db_connect.php';

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'department_admin') {
    header('Location: ../index.php');
    exit;
}

$user = null;
$message = '';
$error = '';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SESSION['role'] !== 'department_admin') {
    require_once '../includes/barangays_list.php';
}

// Fetch user data if ID is provided
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    if ($id) {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $error = 'User not found.';
            }
        } catch (PDOException $e) {
            $error = "Error fetching user: " . $e->getMessage();
        }
    } else {
        $error = 'Invalid user ID.';
    }
} else {
    $error = 'No user ID provided.';
}

// Handle form submission for updating user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateUser'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'CSRF token validation failed.';
    } else {
                $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
                $firstName = $_POST['firstName'];
                $lastName = $_POST['lastName'];
                $email = $_POST['email'];
                $username = $_POST['username'];
                $role = $_POST['role'];
                $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : null;
                $newPassword = $_POST['newPassword'];
                $confirmPassword = $_POST['confirmPassword'];
                $profilePicture = $user['profile_picture']; // Keep existing if not updated
        
                if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($role)) {
                    $error = 'Please fill in all required fields.';
                } else {
                    // Validate barangay only if the current user is not a department_admin and a barangay is provided
                    if ($_SESSION['role'] !== 'department_admin' && !empty($barangay)) {
                        if (!in_array($barangay, $barangays_list)) {
                            $error = 'Invalid barangay selected.';
                        }
                    } else if ($_SESSION['role'] !== 'department_admin' && empty($barangay) && $role === 'barangay_staff') {
                        $error = 'Barangay is required for Barangay Staff.';
                    } else if ($_SESSION['role'] === 'department_admin') {
                        $barangay = null; // Set barangay to null for department admins
                    }
                    // Check for duplicate username (excluding current user)
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username AND id != :id");
                    $stmt->execute(['username' => $username, 'id' => $id]);
                    if ($stmt->fetchColumn() > 0) {
                        $error = 'Username already exists. Please choose a different one.';
                    } else {
                        // Check for duplicate email (excluding current user)
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :id");
                        $stmt->execute(['email' => $email, 'id' => $id]);
                        if ($stmt->fetchColumn() > 0) {
                            $error = 'Email already exists. Please use a different one.';
                        } else {
                            // Password validation
                            if (!empty($newPassword)) {
                                if ($newPassword !== $confirmPassword) {
                                    $error = 'New password and confirm password do not match.';
                                }
                            } // Closes if ($newPassword !== $confirmPassword)
                        } // Closes if (!empty($newPassword))

                        // Handle profile picture upload only if no other errors yet
                        if (empty($error) && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
                            $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
                            $fileName = $_FILES['profile_picture']['name'];
                            $fileSize = $_FILES['profile_picture']['size'];
                            $fileType = $_FILES['profile_picture']['type'];
                            $fileNameCmps = explode(".", $fileName);
                            $fileExtension = strtolower(end($fileNameCmps));

                            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
                            if (in_array($fileExtension, $allowedfileExtensions)) {
                                $uploadFileDir = '../images/profile_pictures/';
                                if (!is_dir($uploadFileDir)) {
                                    mkdir($uploadFileDir, 0777, true);
                                }
                                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                                $dest_path = $uploadFileDir . $newFileName;

                                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                                    $profilePicture = $newFileName;
                                } else {
                                    $error = "There was an error moving the uploaded profile picture file.";
                                }
                            } else {
                                $error = "Invalid profile picture file type. Only JPG, JPEG, PNG, GIF are allowed.";
                            }
                        }

                        // Proceed with database update only if no errors
                        if (empty($error)) {
                            try {
                                $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, username = :username, role = :role, barangay = :barangay, profile_picture = :profile_picture";
                                $params = [
                                    'first_name' => $firstName,
                                    'last_name' => $lastName,
                                    'email' => $email,
                                    'username' => $username,
                                    'role' => $role,
                                    'barangay' => $barangay,
                                    'profile_picture' => $profilePicture,
                                    'id' => $id
                                ];

                                if (!empty($newPassword)) {
                                    $sql .= ", password = :password";
                                    $params['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                                }

                                $sql .= " WHERE id = :id";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute($params);

                                $message = 'User updated successfully!';
                                // Re-fetch user data to display updated info immediately
                                $stmt = $conn->prepare('SELECT * FROM users WHERE id = :id');
                                $stmt->execute(['id' => $id]);
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                // Regenerate CSRF token after successful submission
                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                            } catch (PDOException $e) {
                                $error = "Error updating user: " . $e->getMessage();
                            }
                        }
                    } 
                            } 
                        } 
                    }
                ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CARELINK — Edit User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/department-sidebar.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --success: #2ecc71;
            --warning: #f39c12;
            --light: #ecf0f1;
            --dark: #34495e;
            --gray: #95a5a6;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            height: 100vh;
            overflow: auto;
        }

        .main-content {
            padding: 20px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .header-content {
            display: flex;
            flex-direction: column;
        }

        .welcome-message {
            font-size: 1.2rem;
            color: var(--gray);
            margin-bottom: 5px;
        }

        .header h1 {
            color: var(--primary);
            font-size: 1.8rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--primary);
            display: flex;
            align-items: center;
        }

        .card h3 i {
            margin-right: 10px;
            color: var(--secondary);
        }

        .btn {
            display: inline-block;
            background: var(--secondary);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-success {
            background: var(--success);
        }
        
        .btn-danger {
            background: var(--accent);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .table th {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: var(--primary);
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .message, .error {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .message {
            background: var(--success);
            color: white;
        }
        
        .error {
            background: var(--accent);
            color: white;
        }

        .password-input-container {
            position: relative;
            width: 100%;
        }

        .password-input-container input[type="password"],
        .password-input-container input[type="text"] {
            padding-right: 40px; /* Space for the toggle button */
        }

        .password-input-container .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--gray);
        }

        .password-input-container .toggle-password:hover {
            color: var(--primary);
        }

        .profile-picture-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-top: 10px;
            border: 2px solid #ddd;
        }
    </style>
</head>
<body>
   <div class="container">
        <?php include '../partials/department_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-content">
                    <div class="welcome-message">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></strong>!</div>
                    <h1>Edit User</h1>
                </div>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php
                            $profilePic = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default.jpg';
                            $profilePicPath = '../images/profile_pictures/' . $profilePic;
                            if (!file_exists($profilePicPath) || is_dir($profilePicPath)) {
                                $profilePicPath = '../images/profile_pictures/default.jpg'; // Fallback to default if file doesn't exist
                            }
                        ?>
                        <img src="<?php echo $profilePicPath; ?>" alt="Profile Picture" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    </div>
                    <div class="user-details">
                        <h2><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h2>
                        <p><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $_SESSION['role']))); ?></p>
                    </div>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="card">
                <?php if ($user): ?>
                <form id="editUserForm" method="post" action="" enctype="multipart/form-data">
                <h3><i class="fas fa-user-edit"></i> Edit User: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="">Select role</option>
                                <option value="department_admin" <?php echo ($user['role'] == 'department_admin') ? 'selected' : ''; ?>>Administrator</option>
                                <option value="barangay_staff" <?php echo ($user['role'] == 'barangay_staff') ? 'selected' : ''; ?>>Barangay Staff</option>
                            </select>
                        </div>
                        <?php if ($_SESSION['role'] !== 'department_admin'): ?>
                        <div class="form-group">
                            <label for="barangay">Barangay</label>
                            <select id="barangay" name="barangay" required>
                                <option value="">Select barangay</option>
                                <?php foreach ($barangays_list as $b): ?>
                                    <option value="<?php echo htmlspecialchars($b); ?>" <?php echo ($user['barangay'] == $b) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="profile_picture">Profile Picture (optional)</label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                        <?php
                            $currentProfilePic = isset($user['profile_picture']) ? $user['profile_picture'] : 'default.jpg';
                            $currentProfilePicPath = '../images/profile_pictures/' . $currentProfilePic;
                            if (!file_exists($currentProfilePicPath) || is_dir($currentProfilePicPath)) {
                                $currentProfilePicPath = '../images/profile_pictures/default.jpg'; // Fallback to default if file doesn't exist
                            }
                        ?>
                        <img id="profile_picture_preview" class="profile-picture-preview" src="<?php echo $currentProfilePicPath; ?>" alt="Profile Picture Preview">
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password (leave blank to keep current)</label>
                        <div class="password-input-container">
                            <input type="password" id="newPassword" name="newPassword" placeholder="Enter new password">
                            <span class="toggle-password" onclick="togglePasswordVisibility('newPassword')"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <div class="password-input-container">
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password">
                            <span class="toggle-password" onclick="togglePasswordVisibility('confirmPassword')"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="submit" name="updateUser" class="btn btn-success">Update User</button>
                        <a href="User_Management.php" class="btn">Cancel</a>
                    </div>
                </form>
                <?php else: ?>
                    <p>User not found or invalid ID.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Function to toggle password visibility
        window.togglePasswordVisibility = function(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const icon = passwordInput.nextElementSibling.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const welcomeMessage = document.querySelector('.welcome-message');
            const hour = new Date().getHours();
            let greeting;
            if (hour < 12) {
                greeting = "Good morning";
            } else if (hour < 18) {
                greeting = "Good afternoon";
            }
            else {
                greeting = "Good evening";
            }
            welcomeMessage.innerHTML = `${greeting}, <strong><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></strong>!`;

            // Profile picture preview for edit user
            const profilePictureInput = document.getElementById('profile_picture');
            const profilePicturePreview = document.getElementById('profile_picture_preview');

            profilePictureInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profilePicturePreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    // If no file is selected, revert to the current profile picture or default
                    profilePicturePreview.src = '<?php echo $currentProfilePicPath; ?>';
                }
            });
        });
    </script>
    <script src="../assets/js/sidebar-toggle.js"></script>
</body>
</html>