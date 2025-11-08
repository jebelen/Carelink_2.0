<?php
session_start();
require_once '../includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Fetch user data and settings
try {
    $stmt = $conn->prepare("SELECT u.*, s.theme, s.language, s.notifications FROM users u LEFT JOIN settings s ON u.id = s.user_id WHERE u.id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch notifications
    $stmt = $conn->prepare("SELECT * FROM notifications ORDER BY created_at DESC");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
    $user = [];
    $notifications = [];
}

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateProfile'])) {
    $displayName = $_POST['displayName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    try {
        $stmt = $conn->prepare("UPDATE users SET display_name = :display_name, email = :email, phone = :phone WHERE id = :id");
        $stmt->execute([
            'display_name' => $displayName,
            'email' => $email,
            'phone' => $phone,
            'id' => $user_id
        ]);
        $message = "Profile updated successfully!";
        // Re-fetch user data
        $stmt = $conn->prepare("SELECT u.*, s.theme, s.language, s.notifications FROM users u LEFT JOIN settings s ON u.id = s.user_id WHERE u.id = :id");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error updating profile: " . $e->getMessage();
    }
}

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateSettings'])) {
    $theme = $_POST['theme'];
    $language = $_POST['language'];
    $notification_pref = $_POST['notifications'];

    try {
        $stmt = $conn->prepare("INSERT INTO settings (user_id, theme, language, notifications) VALUES (:user_id, :theme, :language, :notifications) ON DUPLICATE KEY UPDATE theme = :theme, language = :language, notifications = :notifications");
        $stmt->execute([
            'user_id' => $user_id,
            'theme' => $theme,
            'language' => $language,
            'notifications' => $notification_pref
        ]);
        $message = "Settings updated successfully!";
        // Re-fetch user data
        $stmt = $conn->prepare("SELECT u.*, s.theme, s.language, s.notifications FROM users u LEFT JOIN settings s ON u.id = s.user_id WHERE u.id = :id");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error updating settings: " . $e->getMessage();
    }
}


// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updatePassword'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword !== $confirmPassword) {
        $error = "New passwords do not match.";
    } else if (password_verify($currentPassword, $user['password'])) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute(['password' => $hashedPassword, 'id' => $user_id]);
            $message = "Password updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating password: " . $e->getMessage();
        }
    } else {
        $error = "Incorrect current password.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CARELINK — Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/barangay-sidebar.css">
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
            overflow: hidden;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar styles are handled by barangay-sidebar.css */

        /* Main Content */
        .main-content {
            flex: 1;
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

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background: white;
            border-radius: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
            font-size: 18px;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-details h2 {
            font-size: 14px;
            margin-bottom: 2px;
            color: var(--primary);
        }

        .user-details p {
            color: var(--gray);
            font-size: 12px;
        }

        /* Settings Cards */
        .settings-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .settings-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .settings-card h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--primary);
            display: flex;
            align-items: center;
        }

        .settings-card h3 i {
            margin-right: 10px;
            color: var(--secondary);
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
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--secondary);
        }

        .form-group input:disabled {
            background-color: #f5f5f5;
            color: #999;
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

        .btn:hover {
            background: #2980b9;
        }

        .btn-secondary {
            background: var(--gray);
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .btn-small {
            padding: 8px 16px;
            font-size: 12px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .profile-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .profile-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .profile-info h2 {
            color: var(--primary);
            margin-bottom: 5px;
        }

        .profile-info p {
            color: var(--gray);
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 10px;
                height: auto;
            }
            
            .nav-links {
                display: flex;
                overflow-x: auto;
            }
            
            .nav-links li {
                white-space: nowrap;
            }
            
            .main-content {
                height: auto;
                overflow-y: visible;
            }
            
            .settings-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .header-actions {
                align-self: flex-end;
            }
            
            .settings-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include '../partials/barangay_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-content">
                    <div class="welcome-message">Welcome back, <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>!</div>
                    <h1>User Settings</h1>
                </div>
                <div class="header-actions">
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
                                            </div>                        <div class="user-details">
                            <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                            <p><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $user['role']))); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="message" style="background-color: var(--success); color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px;"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="error" style="background-color: var(--accent); color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px;"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Profile Section -->
            <div class="profile-section">
                <div class="profile-header">
                    <div class="profile-avatar"><?php echo htmlspecialchars(strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1))); ?></div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                        <p><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $user['role']))); ?><?php if ($_SESSION['role'] !== 'department_admin'): ?> • <?php echo htmlspecialchars($user['barangay']); ?><?php endif; ?></p>
                    </div>
                </div>
                <p style="color: var(--gray); font-size: 14px;">Manage your account preferences and appearance.</p>
            </div>

            <!-- Settings Grid -->
            <div class="settings-grid">
                <!-- Profile Settings -->
                <div class="settings-card">
                    <h3><i class="fas fa-user"></i> Profile Settings</h3>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="displayName">Display Name</label>
                            <input type="text" id="displayName" name="displayName" value="<?php echo htmlspecialchars($user['display_name'] ?? ''); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone (optional)</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" disabled>
                        </div>
                        <div class="actions">
                            <button type="button" class="btn btn-small" id="editProfileBtn">Edit Profile</button>
                            <button type="submit" name="updateProfile" class="btn btn-success btn-small" id="saveBtn" style="display: none;">Save Changes</button>
                            <button type="button" class="btn btn-secondary btn-small" id="cancelBtn" style="display: none;">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- System Settings -->
                <div class="settings-card">
                    <h3><i class="fas fa-cog"></i> System Settings</h3>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="theme">Theme</label>
                            <select id="theme" name="theme" disabled>
                                <option value="light" <?php echo ($user['theme'] ?? 'light') === 'light' ? 'selected' : ''; ?>>Light</option>
                                <option value="dark" <?php echo ($user['theme'] ?? '') === 'dark' ? 'selected' : ''; ?>>Dark</option>
                                <option value="auto" <?php echo ($user['theme'] ?? '') === 'auto' ? 'selected' : ''; ?>>Auto</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="language">Language</label>
                            <select id="language" name="language" disabled>
                                <option value="en" <?php echo ($user['language'] ?? 'en') === 'en' ? 'selected' : ''; ?>>English</option>
                                <option value="fil" <?php echo ($user['language'] ?? '') === 'fil' ? 'selected' : ''; ?>>Filipino</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notifications">Notifications</label>
                            <select id="notifications" name="notifications" disabled>
                                <option value="all" <?php echo ($user['notifications'] ?? 'all') === 'all' ? 'selected' : ''; ?>>All Notifications</option>
                                <option value="important" <?php echo ($user['notifications'] ?? '') === 'important' ? 'selected' : ''; ?>>Important Only</option>
                                <option value="none" <?php echo ($user['notifications'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
                            </select>
                        </div>
                        <div class="actions">
                            <button type="button" class="btn btn-small" id="editSystemBtn">Edit Settings</button>
                            <button type="submit" name="updateSettings" class="btn btn-success btn-small" id="saveSystemBtn" style="display: none;">Save Changes</button>
                            <button type="button" class="btn btn-secondary btn-small" id="cancelSystemBtn" style="display: none;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="settings-card">
                <h3><i class="fas fa-shield-alt"></i> Security Settings</h3>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" name="currentPassword" placeholder="Enter current password" disabled>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" name="newPassword" placeholder="Enter new password" disabled>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password" disabled>
                    </div>
                    <div class="actions">
                        <button type="button" class="btn btn-small" id="editPasswordBtn">Change Password</button>
                        <button type="submit" name="updatePassword" class="btn btn-success btn-small" id="savePasswordBtn" style="display: none;">Update Password</button>
                        <button type="button" class="btn btn-secondary btn-small" id="cancelPasswordBtn" style="display: none;">Cancel</button>
                    </div>
                </form>
            </div>
    </div>

    <script src="../assets/js/sidebar-toggle.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const welcomeMessage = document.querySelector('.welcome-message');
            const hour = new Date().getHours();
            let greeting;
            
            if (hour < 12) {
                greeting = "Good morning";
            } else if (hour < 18) {
                greeting = "Good afternoon";
            } else {
                greeting = "Good evening";
            }
            
            welcomeMessage.innerHTML = `${greeting}, <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>!`;

            // Profile Settings
            const editProfileBtn = document.getElementById('editProfileBtn');
            const saveBtn = document.getElementById('saveBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const profileInputs = ['displayName', 'email', 'phone'];

            editProfileBtn.addEventListener('click', () => {
                profileInputs.forEach(id => document.getElementById(id).disabled = false);
                editProfileBtn.style.display = 'none';
                saveBtn.style.display = 'inline-block';
                cancelBtn.style.display = 'inline-block';
            });

            cancelBtn.addEventListener('click', () => {
                profileInputs.forEach(id => document.getElementById(id).disabled = true);
                editProfileBtn.style.display = 'inline-block';
                saveBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
                // Reset to original values if needed
            });

            // System Settings
            const editSystemBtn = document.getElementById('editSystemBtn');
            const saveSystemBtn = document.getElementById('saveSystemBtn');
            const cancelSystemBtn = document.getElementById('cancelSystemBtn');
            const systemInputs = ['theme', 'language', 'notifications'];

            editSystemBtn.addEventListener('click', () => {
                systemInputs.forEach(id => document.getElementById(id).disabled = false);
                editSystemBtn.style.display = 'none';
                saveSystemBtn.style.display = 'inline-block';
                cancelSystemBtn.style.display = 'inline-block';
            });

            cancelSystemBtn.addEventListener('click', () => {
                systemInputs.forEach(id => document.getElementById(id).disabled = true);
                editSystemBtn.style.display = 'inline-block';
                saveSystemBtn.style.display = 'none';
                cancelSystemBtn.style.display = 'none';
            });

            // Security Settings
            const editPasswordBtn = document.getElementById('editPasswordBtn');
            const savePasswordBtn = document.getElementById('savePasswordBtn');
            const cancelPasswordBtn = document.getElementById('cancelPasswordBtn');
            const passwordInputs = ['currentPassword', 'newPassword', 'confirmPassword'];

            editPasswordBtn.addEventListener('click', () => {
                passwordInputs.forEach(id => document.getElementById(id).disabled = false);
                editPasswordBtn.style.display = 'none';
                savePasswordBtn.style.display = 'inline-block';
                cancelPasswordBtn.style.display = 'inline-block';
            });

            cancelPasswordBtn.addEventListener('click', () => {
                passwordInputs.forEach(id => {
                    document.getElementById(id).disabled = true;
                    document.getElementById(id).value = '';
                });
                editPasswordBtn.style.display = 'inline-block';
                savePasswordBtn.style.display = 'none';
                cancelPasswordBtn.style.display = 'none';
            });
        });
    </script>
</body>
</html>
