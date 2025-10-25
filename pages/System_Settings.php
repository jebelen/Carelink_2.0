<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CARELINK — System Settings</title>
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

        /* Main Content */
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

        /* Cards */
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

        /* Settings Grid */
        .settings-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        /* Form */
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--secondary);
        }

        .form-group input:disabled {
            background-color: #f5f5f5;
            color: #999;
        }

        /* Buttons */
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

        /* Actions */
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        /* Switch */
        .switch {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .switch input[type="checkbox"] {
            width: 50px;
            height: 25px;
            appearance: none;
            background: #ccc;
            border-radius: 25px;
            position: relative;
            cursor: pointer;
            transition: background 0.3s;
        }

        .switch input[type="checkbox"]:checked {
            background: var(--secondary);
        }

        .switch input[type="checkbox"]::before {
            content: '';
            position: absolute;
            width: 21px;
            height: 21px;
            border-radius: 50%;
            background: white;
            top: 2px;
            left: 2px;
            transition: transform 0.3s;
        }

        .switch input[type="checkbox"]:checked::before {
            transform: translateX(25px);
        }

        /* Responsive */
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
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <!-- Logo image with fallback -->
                    <img src="../images/LOGO.jpg" alt="Barangay Pinagbuhatan Logo" class="logo-image" onerror="this.style.display='none'; document.getElementById('fallback-logo').style.display='flex';">
                    <div id="fallback-logo" class="logo-image" style="display: none; background: var(--secondary); width: 40px; height: 40px; border-radius: 8px; align-items: center; justify-content: center; font-weight: bold; color: white; font-size: 0.9rem;">BP</div>
                    <h1 class="logo-text">CARELINK</h1>
                </div>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="Department_Dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="User_Management.php"><i class="fas fa-user-cog"></i> User Management</a></li>
                    <li><a href="Department_Records.php"><i class="fas fa-database"></i> Records</a></li>
                    <li><a href="Verify_Document.php"><i class="fas fa-check-circle"></i> Verify Documents</a></li>
                    <li class="active"><a href="System_Settings.php"><i class="fas fa-cog"></i> System Settings</a></li>
                    <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-content">
                    <div class="welcome-message" data-first-name="<?php echo htmlspecialchars($_SESSION['first_name']); ?>" data-last-name="<?php echo htmlspecialchars($_SESSION['last_name']); ?>" data-role="<?php echo htmlspecialchars($_SESSION['role']); ?>"></div>
            <h1>System Settings</h1>
                </div>
                <div class="header-actions">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-details">
                            <h2><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h2>
                            <p><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $_SESSION['role']))); ?></p>
          </div>
          </div>
        </div>
      </div>

            <!-- Settings Grid -->
            <div class="settings-grid">
                <!-- General Settings -->
      <div class="card">
                    <h3><i class="fas fa-cog"></i> General Settings</h3>
                    <div class="form-group">
                        <label for="systemName">System Name</label>
                        <input type="text" id="systemName" value="CARELINK" disabled>
                    </div>
                    <div class="form-group">
                        <label for="version">Version</label>
                        <input type="text" id="version" value="2.0.0" disabled>
                    </div>
                    <div class="form-group">
                        <label for="language">Default Language</label>
                        <select id="language">
                            <option value="en">English</option>
                            <option value="fil">Filipino</option>
                        </select>
        </div>
                    <div class="form-group">
                        <label for="timezone">Timezone</label>
                        <select id="timezone">
                            <option value="UTC+8">UTC+8 (Philippines)</option>
                            <option value="UTC+0">UTC+0 (GMT)</option>
                        </select>
                </div>
                    <div class="actions">
                        <button class="btn btn-small" onclick="editSettings('general')">Edit Settings</button>
                        <button class="btn btn-secondary btn-small" onclick="saveSettings('general')" style="display: none;">Save Changes</button>
                </div>
              </div>

                <!-- Security Settings -->
                <div class="card">
                    <h3><i class="fas fa-shield-alt"></i> Security Settings</h3>
                    <div class="form-group">
                        <label for="sessionTimeout">Session Timeout (minutes)</label>
                        <input type="number" id="sessionTimeout" value="30" min="5" max="120">
                    </div>
                    <div class="form-group">
                        <label for="maxLoginAttempts">Max Login Attempts</label>
                        <input type="number" id="maxLoginAttempts" value="5" min="3" max="10">
                </div>
                    <div class="form-group">
                        <div class="switch">
                            <input type="checkbox" id="twoFactorAuth" checked>
                            <label for="twoFactorAuth">Enable Two-Factor Authentication</label>
                </div>
              </div>
                    <div class="form-group">
                        <div class="switch">
                            <input type="checkbox" id="passwordPolicy" checked>
                            <label for="passwordPolicy">Enforce Password Policy</label>
                </div>
              </div>
              <div class="actions">
                        <button class="btn btn-small" onclick="editSettings('security')">Edit Settings</button>
                        <button class="btn btn-secondary btn-small" onclick="saveSettings('security')" style="display: none;">Save Changes</button>
              </div>
                </div>
              </div>

            <!-- Notification Settings -->
            <div class="card">
                <h3><i class="fas fa-bell"></i> Notification Settings</h3>
                <div class="form-group">
                    <div class="switch">
                        <input type="checkbox" id="emailNotifications" checked>
                        <label for="emailNotifications">Email Notifications</label>
                </div>
                </div>
                <div class="form-group">
                    <div class="switch">
                        <input type="checkbox" id="smsNotifications">
                        <label for="smsNotifications">SMS Notifications</label>
              </div>
            </div>
                <div class="form-group">
                    <div class="switch">
                        <input type="checkbox" id="pushNotifications" checked>
                        <label for="pushNotifications">Push Notifications</label>
            </div>
            </div>
                <div class="form-group">
                    <label for="notificationEmail">Notification Email</label>
                    <input type="email" id="notificationEmail" value="admin@carelink.com">
            </div>
                <div class="actions">
                    <button class="btn btn-small" onclick="editSettings('notifications')">Edit Settings</button>
                    <button class="btn btn-secondary btn-small" onclick="saveSettings('notifications')" style="display: none;">Save Changes</button>
              </div>
            </div>

            <!-- System Maintenance -->
            <div class="card">
                <h3><i class="fas fa-tools"></i> System Maintenance</h3>
                <div class="form-group">
                    <label for="backupFrequency">Backup Frequency</label>
                    <select id="backupFrequency">
                        <option value="daily">Daily</option>
                        <option value="weekly" selected>Weekly</option>
                        <option value="monthly">Monthly</option>
                </select>
              </div>
                <div class="form-group">
                    <div class="switch">
                        <input type="checkbox" id="autoBackup" checked>
                        <label for="autoBackup">Automatic Backup</label>
        </div>
      </div>
                <div class="form-group">
                    <label for="logRetention">Log Retention (days)</label>
                    <input type="number" id="logRetention" value="30" min="7" max="365">
  </div>
                <div class="actions">
                    <button class="btn btn-small" onclick="editSettings('maintenance')">Edit Settings</button>
                    <button class="btn btn-secondary btn-small" onclick="saveSettings('maintenance')" style="display: none;">Save Changes</button>
                    <button class="btn btn-warning btn-small" onclick="runBackup()">Run Backup Now</button>
      </div>
      </div>
    </div>
  </div>

  <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event to navigation items
            const navItems = document.querySelectorAll('.sidebar-menu li');
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    navItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Update welcome message based on time of day
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
        });

        function editSettings(section) {
            const card = event.target.closest('.card');
            const inputs = card.querySelectorAll('input, select, textarea');
            const editBtn = card.querySelector('button[onclick*="editSettings"]');
            const saveBtn = card.querySelector('button[onclick*="saveSettings"]');
            
            inputs.forEach(input => {
                input.disabled = false;
            });
            
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-block';
        }

        function saveSettings(section) {
            alert(`${section.charAt(0).toUpperCase() + section.slice(1)} settings saved successfully!`);
            
            const card = event.target.closest('.card');
            const inputs = card.querySelectorAll('input, select, textarea');
            const editBtn = card.querySelector('button[onclick*="editSettings"]');
            const saveBtn = card.querySelector('button[onclick*="saveSettings"]');
            
            inputs.forEach(input => {
                input.disabled = true;
            });
            
            editBtn.style.display = 'inline-block';
            saveBtn.style.display = 'none';
        }

        function runBackup() {
            alert('Backup process started! You will be notified when it completes.');
        }
  </script>
</body>
</html>