<?php
require_once '../includes/db_connect.php';
require_once '../includes/barangays_list.php';
require_once '../includes/password_validation.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = $_POST['role'];
    $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : null;

    if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($password) || empty($confirmPassword) || empty($role)) {
        $error = 'Please fill in all required fields.';
    } else if ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $validationResult = validatePassword($password);
        if (!$validationResult['valid']) {
            $error = $validationResult['message'];
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } else {
        $error = 'Invalid email format.';
    } else {
        try {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
            $stmt->execute(['username' => $username, 'email' => $email]);
            if ($stmt->fetch()) {
                $error = 'Username or email already exists.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $conn->beginTransaction();

                $sql = "INSERT INTO users (first_name, last_name, email, username, password, role, barangay) VALUES (:first_name, :last_name, :email, :username, :password, :role, :barangay)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'username' => $username,
                    'password' => $hashedPassword,
                    'role' => $role,
                    'barangay' => $barangay
                ]);

                $user_id = $conn->lastInsertId();

                // Create default settings for the new user
                $stmt = $conn->prepare("INSERT INTO settings (user_id) VALUES (:user_id)");
                $stmt->execute(['user_id' => $user_id]);

                $conn->commit();

                if ($role === 'barangay_staff') {
                    $login_page = 'Barangay_Staff_LogInPage.php';
                } else {
                    $login_page = 'Department_Admin_LogIn_Page.php';
                }
                $success = "User registered successfully! You can now <a href='$login_page'>login</a>.";
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            $error = 'Failed to register user: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - CARELINK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a4b8c 0%, #0d3a6e 100%);
            color: white;
            height: 100vh;
            overflow: auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .signup-container {
            padding: 20px;
        }

        .signup-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .signup-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .signup-header h1 {
            font-size: 2rem;
            color: #4CAF50;
            margin-bottom: 10px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1rem;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            background: #4CAF50;
            color: white;
        }

        .message {
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }

        .success {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid rgba(76, 175, 80, 0.5);
            color: #a5d6a7;
        }

        .error {
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid rgba(255, 77, 77, 0.5);
            color: #ff4d4d;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-header">
                <h1>Create Account</h1>
            </div>

            <?php if ($success): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" class="form-control" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" class="form-control" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" required>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control" onchange="toggleBarangayField()" required>
                        <option value="barangay_staff">Barangay Staff</option>
                        <option value="department_admin">Department Admin</option>
                    </select>
                </div>
                <div class="form-group" id="barangayField" style="margin-bottom: 20px;">
                    <label for="barangay">Barangay</label>
                    <select id="barangay" name="barangay" class="form-control">
                        <option value="">Select barangay</option>
                        <?php foreach ($barangays_list as $b): ?>
                            <option value="<?php echo htmlspecialchars($b); ?>"><?php echo htmlspecialchars($b); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Sign Up</button>
            </form>
        </div>
    </div>

    <script>
        function toggleBarangayField() {
            var role = document.getElementById('role').value;
            var barangayField = document.getElementById('barangayField');
            var barangaySelect = document.getElementById('barangay');
            if (role === 'barangay_staff') {
                barangayField.style.display = 'block';
                barangaySelect.setAttribute('required', 'required');
            } else {
                barangayField.style.display = 'none';
                barangaySelect.removeAttribute('required');
            }
        }
        // Initial check
        toggleBarangayField();
    </script>
</body>
</html>