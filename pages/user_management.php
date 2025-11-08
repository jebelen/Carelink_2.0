<?php
session_start();
require_once '../includes/db_connect.php';

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'department_admin') {
    // Redirect to login page or show an error
    header('Location: ../index.php');
    exit;
}

$message = '';
$error = '';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../includes/barangays_list.php';

// Handle Add User Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addUser'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'CSRF token validation failed.';
    } else {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $role = $_POST['role'];
        $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : null;
        error_log("DEBUG: POST[barangay] = " . print_r($_POST['barangay'], true));
        error_log("DEBUG: Initial \$barangay = " . print_r($barangay, true));
        $password = $_POST['password'];
        $profilePicture = 'default.jpg'; // Default profile picture

        if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($role) || empty($password)) {
            $error = 'Please fill in all required fields.';
        } else {
            // Validate role
            $allowedRoles = ['department_admin', 'barangay_staff'];
            if (!in_array($role, $allowedRoles)) {
                $error = 'Invalid role selected.';
            }

            // Validate barangay based on the selected role
            if ($role === 'barangay_staff') {
                if (empty($barangay)) {
                    $error = 'Barangay is required for Barangay Staff.';
                } elseif (!in_array($barangay, $barangays_list)) {
                    $error = 'Invalid barangay selected.';
                }
            } else { // role is department_admin
                $barangay = null; // Ensure barangay is null for department admins
            }

            if (empty($error)) {
                // Handle profile picture upload
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
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

                if (empty($error)) {
                    // Hash the password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Check for duplicate username
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
                    $stmt->execute(['username' => $username]);
                    if ($stmt->fetchColumn() > 0) {
                        $error = 'Username already exists. Please choose a different one.';
                    } else {
                        // Check for duplicate email
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
                        $stmt->execute(['email' => $email]);
                        if ($stmt->fetchColumn() > 0) {
                            $error = 'Email already exists. Please use a different one.';
                        } else {
                            try {
                                $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, username, role, barangay, password, profile_picture) VALUES (:first_name, :last_name, :email, :username, :role, :barangay, :password, :profile_picture)");
                                $stmt->execute([
                                    'first_name' => $firstName,
                                    'last_name' => $lastName,
                                    'email' => $email,
                                    'username' => $username,
                                    'role' => $role,
                                    'barangay' => $barangay,
                                    'password' => $hashedPassword,
                                    'profile_picture' => $profilePicture
                                ]);
                                $message = 'User added successfully!';
                                // Regenerate CSRF token after successful submission
                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                            } catch (PDOException $e) {
                                $error = "Error: " . $e->getMessage();
                            }
                        }
                    }
                }
            }
        }
    }
}

// Fetch users from the database
try {
    // For department admin, show all users, or filter by a selected barangay
    $barangayFilter = isset($_GET['barangay']) ? $_GET['barangay'] : 'all';
    
    if ($barangayFilter === 'all') {
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role, barangay, profile_picture FROM users");
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role, barangay, profile_picture FROM users WHERE barangay = :barangay");
        $stmt->execute(['barangay' => $barangayFilter]);
    }
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
    $users = [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CARELINK — User Management</title>
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
            height: auto;
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
                    <div class="welcome-message" data-first-name="<?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : ''; ?>" data-last-name="<?php echo htmlspecialchars($_SESSION['last_name']) ? htmlspecialchars($_SESSION['last_name']) : ''; ?>" data-role="<?php echo isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : ''; ?>"></div>
                    <h1>User Management</h1>
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

            <!-- Add User Card -->
            <div class="card">
                <h3><i class="fas fa-user-plus"></i> Add New User</h3>
                <form id="addUserForm" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; 
?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" placeholder="Enter first name" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" placeholder="Enter last name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Enter email address" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" placeholder="Enter username" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="password-input-container">
                                <input type="password" id="password" name="password" placeholder="Create a password" required>
                                <span class="toggle-password"><i class="fas fa-eye"></i></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="">Select role</option>
                                <option value="department_admin">Administrator</option>
                                <option value="barangay_staff">Barangay Staff</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="barangay">Barangay</label>
                            <select id="barangay" name="barangay">
                                <option value="">Select barangay</option>
                                <?php foreach ($barangays_list as $b): ?>
                                    <option value="<?php echo htmlspecialchars($b); ?>"><?php echo htmlspecialchars($b); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="profile_picture">Profile Picture (optional)</label>
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                            <img id="profile_picture_preview" class="profile-picture-preview" src="../images/profile_pictures/default.jpg" alt="Profile Picture Preview">
                        </div>
                    </div>
                    <div class="actions">
                        <button type="submit" name="addUser" class="btn btn-success">Add User</button>
                        <button type="reset" class="btn">Reset</button>
                    </div>
                </form>
            </div>

            <!-- Users List Card -->
            <div class="card">
                <h3><i class="fas fa-users"></i> Users List</h3>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Barangay</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="6" style="text-align:center;">No users found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <?php
                                                $userProfilePic = isset($user['profile_picture']) ? $user['profile_picture'] : 'default.jpg';
                                                $userProfilePicPath = '../images/profile_pictures/' . $userProfilePic;
                                                if (!file_exists($userProfilePicPath) || is_dir($userProfilePicPath)) {
                                                    $userProfilePicPath = '../images/profile_pictures/default.jpg'; // Fallback to default if file doesn't exist
                                                }
                                            ?>
                                            <img src="<?php echo $userProfilePicPath; ?>" alt="Profile Picture" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                                        </td>
                                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars(str_replace('_', ' ', ucwords($user['role']))); ?></td>
                                        <td><?php echo htmlspecialchars($user['barangay']); ?></td>
                                        <td>
                                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-small btn-warning">Edit</a>
                                            <form action="delete_user.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <button type="submit" name="deleteUser" class="btn btn-small btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/sidebar-toggle.js"></script>
    <script>


        document.addEventListener('DOMContentLoaded', function() {
            const welcomeMessage = document.querySelector('.welcome-message');
            const firstName = welcomeMessage.dataset.firstName;
            const lastName = welcomeMessage.dataset.lastName;
            const role = welcomeMessage.dataset.role;
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
            welcomeMessage.innerHTML = `${greeting}, <strong>${firstName} ${lastName}</strong>!`;

            const togglePassword = document.querySelector('.toggle-password');
            const passwordInput = document.querySelector('#password');

            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Profile picture preview
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
                    profilePicturePreview.src = '../images/profile_pictures/default.jpg';
                }
            });
        });
    </script>
</body>
</html>