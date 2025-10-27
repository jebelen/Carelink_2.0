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
        $barangay = $_POST['barangay']; // Assuming barangay is selected from a form
        $password = $_POST['password'];

        if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($role) || empty($barangay) || empty($password)) {
            $error = 'Please fill in all fields.';
        } else {
            // Validate role
            $allowedRoles = ['department_admin', 'barangay_staff'];
            if (!in_array($role, $allowedRoles)) {
                $error = 'Invalid role selected.';
            }

            // Validate barangay
            if (!in_array($barangay, $barangays_list)) {
                $error = 'Invalid barangay selected.';
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
                        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, username, role, barangay, password) VALUES (:first_name, :last_name, :email, :username, :role, :barangay, :password)");
                        $stmt->execute([
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'email' => $email,
                            'username' => $username,
                            'role' => $role,
                            'barangay' => $barangay,
                            'password' => $hashedPassword
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

// Fetch users from the database
try {
    // For department admin, show all users, or filter by a selected barangay
    $barangayFilter = isset($_GET['barangay']) ? $_GET['barangay'] : 'all';
    
    if ($barangayFilter === 'all') {
        $stmt = $conn->prepare("SELECT * FROM users");
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE barangay = :barangay");
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
    </style>
</head>
<body>
   <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="../images/LOGO.jpg" alt="Logo" class="logo-image">
                    <h1 class="logo-text">CARELINK</h1>
                </div>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="Department_Dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="active"><a href="User_Management.php"><i class="fas fa-user-cog"></i> User Management</a></li>
                    <li><a href="Department_Records.php"><i class="fas fa-database"></i> Records</a></li>
                    <li><a href="Verify_Document.php"><i class="fas fa-check-circle"></i> Verify Documents</a></li>
                    <li><a href="System_Settings.php"><i class="fas fa-cog"></i> System Settings</a></li>
                    <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-content">
                    <div class="welcome-message" data-first-name="<?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : ''; ?>" data-last-name="<?php echo isset($_SESSION['last_name']) ? htmlspecialchars($_SESSION['last_name']) : ''; ?>" data-role="<?php echo isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : ''; ?>"></div>
                    <h1>User Management</h1>
                </div>
                <div class="user-info">
                    <div class="user-avatar">AD</div>
                    <span>Administrator</span>
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
                <form id="addUserForm" method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
                            <select id="barangay" name="barangay" required>
                                <option value="">Select barangay</option>
                                <?php foreach ($barangays_list as $b): ?>
                                    <option value="<?php echo htmlspecialchars($b); ?>"><?php echo htmlspecialchars($b); ?></option>
                                <?php endforeach; ?>
                            </select>
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
                                    <td colspan="5" style="text-align:center;">No users found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars(str_replace('_', ' ', ucwords($user['role']))); ?></td>
                                        <td><?php echo htmlspecialchars($user['barangay']); ?></td>
                                        <td>
                                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-small btn-warning">Edit</a>
                                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
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
            } else {
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
        });
    </script>
</body>
</html>