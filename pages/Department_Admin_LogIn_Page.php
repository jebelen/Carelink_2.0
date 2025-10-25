<?php
session_start();
require_once '../includes/db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['adminUsername'];
    $password = $_POST['adminPassword'];

    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND role = 'department_admin'");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $user['role'];
            header("Location: Department_Dashboard.php");
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Admin Login - CARELINK</title>
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
            overflow: hidden;
        }

        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: url('../images/LOGO_1.png');
            background-size: cover;
            background-position: center;
            opacity: 0.3;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.5s ease-out;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-icon {
            font-size: 4rem;
            color: #4CAF50;
            margin-bottom: 15px;
        }

        .login-header h1 {
            font-size: 2rem;
            color: #4CAF50;
            margin-bottom: 10px;
        }

        .login-header p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #ff4d4d;
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid rgba(255, 77, 77, 0.5);
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-card {
                padding: 30px 25px;
                margin: 20px;
            }
            
            .login-header h1 {
                font-size: 1.7rem;
            }
            
            .login-icon {
                font-size: 3.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Back Button -->
    <a href="../index.php" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        Back to Home
    </a>

    <!-- Background Image -->
    <div class="background-image"></div>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <h1>Department Admin Login</h1>
                <p>Access system administration and management tools</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form id="adminLoginForm" method="post" action="">
                <div class="form-group">
                    <label for="adminUsername">Username</label>
                    <input type="text" id="adminUsername" name="adminUsername" class="form-control" placeholder="Enter admin username" required>
                </div>
                
                <div class="form-group">
                    <label for="adminPassword">Password</label>
                    <input type="password" id="adminPassword" name="adminPassword" class="form-control" placeholder="Enter admin password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Login to Admin Panel</button>
                
                <div class="forgot-password">
                    <a href="#">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>
    
</body>
</html>