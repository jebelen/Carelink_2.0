<?php
error_log("DEBUG: edit_user.php: Script started.");
session_start();
require_once '../includes/db_connect.php';

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'department_admin') {
    // For AJAX requests, return a JSON error. For direct access, redirect.
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Authentication failed.']);
        exit;
    } else {
        header('Location: ../index.php');
        exit;
    }
}
error_log("DEBUG: edit_user.php: User logged in as department_admin. User ID: " . $_SESSION['user_id']);

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../includes/barangays_list.php';

$user = null;
$currentProfilePicPath = '../images/profile_pictures/default.jpg';

// Handle POST request for updating user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateUser'])) {
    error_log("DEBUG: edit_user.php: POST request processing started.");
    $response = ['success' => false, 'message' => '', 'error' => ''];

    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $response['error'] = 'CSRF token validation failed.';
        error_log("ERROR: edit_user.php: CSRF token validation failed.");
    } else {
        $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        
        // Validation logic...
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $role = $_POST['role'];
        $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : null;
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($role)) {
            $response['error'] = 'Please fill in all required fields.';
        } elseif ($role === 'barangay_staff' && empty($barangay)) {
            $response['error'] = 'Barangay is required for Barangay Staff.';
        } elseif ($role === 'barangay_staff' && !in_array($barangay, $barangays_list)) {
            $response['error'] = 'Invalid barangay selected.';
        } elseif (!empty($newPassword) && $newPassword !== $confirmPassword) {
            $response['error'] = 'New password and confirm password do not match.';
        } else {
            // Check for duplicates if no other errors
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username AND id != :id");
                $stmt->execute(['username' => $username, 'id' => $id]);
                if ($stmt->fetchColumn() > 0) {
                    $response['error'] = 'Username already exists.';
                }

                if (empty($response['error'])) {
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :id");
                    $stmt->execute(['email' => $email, 'id' => $id]);
                    if ($stmt->fetchColumn() > 0) {
                        $response['error'] = 'Email already exists.';
                    }
                }
            } catch (PDOException $e) {
                $response['error'] = 'Database error during validation: ' . $e->getMessage();
                error_log("ERROR: edit_user.php: " . $e->getMessage());
            }
        }

        // Profile picture handling
        $profilePicture = $_POST['existing_profile_picture'] ?? 'default.jpg';
        if (empty($response['error']) && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
            $uploadFileDir = '../images/profile_pictures/';
            $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
            $fileName = $_FILES['profile_picture']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg'];

            if (in_array($fileExtension, $allowedfileExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = $uploadFileDir . $newFileName;
                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    $profilePicture = $newFileName;
                } else {
                    $response['error'] = 'Error moving uploaded file.';
                }
            } else {
                $response['error'] = 'Invalid file type for profile picture.';
            }
        } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
            $response['error'] = 'File upload error: ' . $_FILES['profile_picture']['error'];
        }

        // Database update
        if (empty($response['error'])) {
            try {
                $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, username = :username, role = :role, barangay = :barangay, profile_picture = :profile_picture";
                $params = [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'username' => $username,
                    'role' => $role,
                    'barangay' => ($role === 'barangay_staff') ? $barangay : null,
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

                $response['success'] = true;
                $response['message'] = 'User updated successfully!';
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Regenerate token

            } catch (PDOException $e) {
                $response['error'] = "Database update failed: " . $e->getMessage();
                error_log("ERROR: edit_user.php: " . $e->getMessage());
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Handle GET request to fetch user data for the modal form
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $currentProfilePic = $user['profile_picture'] ?? 'default.jpg';
            $currentProfilePicPath = '../images/profile_pictures/' . $currentProfilePic;
            if (!file_exists($currentProfilePicPath) || is_dir($currentProfilePicPath)) {
                $currentProfilePicPath = '../images/profile_pictures/default.jpg';
            }
        } else {
            // Handle user not found for GET request
            // Output an error message that can be displayed in the modal
            echo "<div class='error'>User not found.</div>";
            exit;
        }
    } catch (PDOException $e) {
        error_log("ERROR: edit_user.php: " . $e->getMessage());
        echo "<div class='error'>Database error while fetching user data.</div>";
        exit;
    }
} else {
    // Handle invalid GET request
    echo "<div class='error'>Invalid request. User ID is missing.</div>";
    exit;
}

// If we get here, it's a valid GET request and we have a user.
// Output the form HTML for the modal.
?>
<?php if ($user): ?>
<form id="editUserForm" method="post" action="pages/edit_user.php" enctype="multipart/form-data">
    <h3><i class="fas fa-user-edit"></i> Edit User: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
    <input type="hidden" name="existing_profile_picture" value="<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.jpg'); ?>">
    
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
        <div class="form-group" id="barangayFormGroup" style="<?php echo ($user['role'] !== 'barangay_staff') ? 'display: none;' : ''; ?>">
            <label for="barangay">Barangay</label>
            <select id="barangay" name="barangay" <?php echo ($user['role'] === 'barangay_staff') ? 'required' : ''; ?>>
                <option value="">Select barangay</option>
                <?php foreach ($barangays_list as $b): ?>
                    <option value="<?php echo htmlspecialchars($b); ?>" <?php echo ($user['barangay'] == $b) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="profile_picture">Profile Picture (optional)</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
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
        <button type="submit" name="updateUser" class="btn btn-success">Update</button>
        <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
    </div>
</form>

<script>
// This script is now loaded into the modal along with the form.
// It sets up the dynamic behavior for the aform elements.

// Ensure functions are globally available or scoped to the modal
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

(function() {
    // Profile picture preview
    const profilePictureInput = document.getElementById('profile_picture');
    const profilePicturePreview = document.getElementById('profile_picture_preview');
    const originalSrc = profilePicturePreview.src;

    profilePictureInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePicturePreview.src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            profilePicturePreview.src = originalSrc;
        }
    });

    // Dynamic display of barangay field based on role
    const roleSelect = document.getElementById('role');
    const barangayFormGroup = document.getElementById('barangayFormGroup');
    const barangaySelect = document.getElementById('barangay');

    function toggleBarangayField() {
        if (roleSelect.value === 'barangay_staff') {
            barangayFormGroup.style.display = 'block';
            barangaySelect.setAttribute('required', 'required');
        } else {
            barangayFormGroup.style.display = 'none';
            barangaySelect.removeAttribute('required');
            barangaySelect.value = '';
        }
    }

    roleSelect.addEventListener('change', toggleBarangayField);
})();
</script>
<?php else: ?>
<div class="error">Could not load user data.</div>
<?php endif; ?>

