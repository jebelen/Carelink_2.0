<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/barangays_list.php';

// Auto-login from remember me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    list($selector, $validator) = explode(':', $_COOKIE['remember_me']);

    $stmt = $conn->prepare("SELECT * FROM remember_tokens WHERE selector = :selector");
    $stmt->execute(['selector' => $selector]);
    $token = $stmt->fetch();

    if ($token) {
        if (hash_equals($token['validator_hash'], hash('sha256', $validator))) {
            // Token is valid, log in the user
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute(['id' => $token['user_id']]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['barangay'] = $user['barangay'];

                // Regenerate token to prevent theft
                $newValidator = bin2hex(random_bytes(32));
                $newValidatorHash = hash('sha256', $newValidator);
                $expiry = date('Y-m-d H:i:s', time() + (86400 * 30));

                $stmt = $conn->prepare("UPDATE remember_tokens SET validator_hash = :validator_hash, expires = :expires WHERE id = :id");
                $stmt->execute([
                    'validator_hash' => $newValidatorHash,
                    'expires' => $expiry,
                    'id' => $token['id']
                ]);

                setcookie('remember_me', $selector . ':' . $newValidator, time() + (86400 * 30), "/", "", false, true);

                header("Location: Barangay_Dash.php");
                exit;
            }
        }
    }
    // If token is invalid or expired, clear the cookie
    setcookie('remember_me', '', time() - 3600, "/");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staffId = trim($_POST['staffId']);
    $password = $_POST['password'];
    $barangay = $_POST['barangay'];
    $remember = isset($_POST['remember']);

    if (empty($staffId) || empty($password) || empty($barangay)) {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND barangay = :barangay AND role = 'barangay_staff'");
            $stmt->execute(['username' => $staffId, 'barangay' => $barangay]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['barangay'] = $user['barangay'];

                if ($remember) {
                    // Generate a secure "remember me" token
                    $selector = bin2hex(random_bytes(8)); // 16 character hex
                    $validator = bin2hex(random_bytes(32)); // 64 character hex
                    $validatorHash = hash('sha256', $validator);
                    $expiry = date('Y-m-d H:i:s', time() + (86400 * 30)); // 30 days

                    // Store the token in the database
                    $stmt = $conn->prepare("INSERT INTO remember_tokens (user_id, selector, validator_hash, expires) VALUES (:user_id, :selector, :validator_hash, :expires)");
                    $stmt->execute([
                        'user_id' => $user['id'],
                        'selector' => $selector,
                        'validator_hash' => $validatorHash,
                        'expires' => $expiry
                    ]);

                    // Set the cookie
                    setcookie('remember_me', $selector . ':' . $validator, time() + (86400 * 30), "/", "", false, true); // 30 days, HttpOnly
                }

                header("Location: Barangay_Dash.php");
                exit;
            } else {
                $error = 'Invalid Staff ID, password, or barangay.';
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Staff Login - CARELINK</title>
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

        /* Make select dropdowns clearly visible against the dark background */
        select.form-control {
            background: #ffffff; /* opaque white */
            color: #222; /* dark text for readability */
            -webkit-appearance: menulist;
            appearance: menulist;
            padding-right: 36px; /* room for dropdown arrow */
        }

        /* Better focus state for selects */
        select.form-control:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.08);
            background: #fff;
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
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1>Barangay Staff Login</h1>
                <p>Access your local records and beneficiary management system</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form id="staffLoginForm" method="post" action="">
                <div class="form-group">
                    <label for="staffId">Staff ID</label>
                    <input type="text" id="staffId" name="staffId" class="form-control" placeholder="Enter your staff ID" required>
                </div>
                
                <div class="form-group">
                    <label for="staffPassword">Password</label>
                    <input type="password" id="staffPassword" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                
                <div class="form-group">
                    <label for="barangaySelect">Barangay</label>
                    <select id="barangaySelect" name="barangay" class="form-control" required>
                        <option value="">Select your barangay</option>
                        <?php foreach ($barangays_list as $b): ?>
                            <option value="<?php echo htmlspecialchars($b); ?>"><?php echo htmlspecialchars($b); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="display: flex; align-items: center;">
                    <input type="checkbox" id="remember" name="remember" style="margin-right: 10px;">
                    <label for="remember" style="margin-bottom: 0;">Remember Me</label>
                </div>
                
                <button type="submit" class="btn btn-primary">Login to System</button>
                
                <div class="forgot-password">
                    <a href="#">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>
    
</body>
</html>