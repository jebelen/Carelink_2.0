<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Records - CARELINK</title>
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
            flex: 1;
            padding: 20px;
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
        }

        .records-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .records-actions {
            display: flex;
            gap: 10px;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 5px;
            padding: 8px 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .search-box input {
            border: none;
            outline: none;
            padding: 5px;
            width: 200px;
        }

        .records-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }

        .table-header {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
            padding: 15px 20px;
            background: var(--primary);
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            transition: background 0.3s;
        }

        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-size: 14px;
            color: var(--dark);
            font-weight: 500;
        }

        .filter-group select, .filter-group input {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            min-width: 150px;
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
                    <div class="welcome-message" data-first-name="<?php echo htmlspecialchars($_SESSION['first_name']); ?>" data-last-name="<?php echo htmlspecialchars($_SESSION['last_name']); ?>" data-role="<?php echo htmlspecialchars($_SESSION['role']); ?>"></div>
                    <h1>Department Records</h1>
                </div>
                <div class="user-info">
                    <div class="user-avatar">AD</div>
                    <span><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
                    <span><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $_SESSION['role']))); ?></span>
                </div>
            </div>

            <!-- Records Section -->
            <div class="records-section">
                <div class="records-header">
                    <h2>All Records for Pasig City</h2>
                    <div class="records-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search records...">
                        </div>
                        <button class="btn"><i class="fas fa-download"></i> Export</button>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="filter-group">
                        <label for="barangay-filter">Barangay</label>
                        <select id="barangay-filter">
                            <option value="all">All Barangays</option>
                            <option value="Maybunga">Maybunga</option>
                            <option value="Malinao">Malinao</option>
                            <option value="Sta. Lucia">Sta. Lucia</option>
                            <option value="Manggahan">Manggahan</option>
                            <option value="Rosario">Rosario</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="type-filter">Application Type</label>
                        <select id="type-filter">
                            <option value="all">All Types</option>
                            <option value="PWD">PWD</option>
                            <option value="Senior Citizen">Senior Citizen</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="status-filter">Status</label>
                        <select id="status-filter">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="verified">Verified</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                
                <div class="records-table">
                    <div class="table-container">
                        <div class="table-header">
                            <div>Applicant Name</div>
                            <div>Application Type</div>
                            <div>Barangay</div>
                            <div>Date Submitted</div>
                            <div>Status</div>
                            <div>ID Number</div>
                            <div>Actions</div>
                        </div>
                        
                        <div class="table-data">
                            <div class="table-row">
                                <div>Carlos Mendoza</div>
                                <div>PWD</div>
                                <div>Maybunga</div>
                                <div>01/05/2024</div>
                                <div><span class="application-status status-rejected">Rejected</span></div>
                                <div>PWD-004</div>
                                <div><button class="btn btn-small"><i class="fas fa-eye"></i> View</button></div>
                            </div>
                            <div class="table-row">
                                <div>Maria Santos</div>
                                <div>Senior Citizen</div>
                                <div>Malinao</div>
                                <div>01/08/2024</div>
                                <div><span class="application-status status-pending">Pending</span></div>
                                <div>SC-012</div>
                                <div><button class="btn btn-small"><i class="fas fa-eye"></i> View</button></div>
                            </div>
                             <div class="table-row">
                                <div>Juan Dela Cruz</div>
                                <div>PWD</div>
                                <div>Sta. Lucia</div>
                                <div>01/10/2024</div>
                                <div><span class="application-status status-sent">Sent to City Hall</span></div>
                                <div>PWD-007</div>
                                <div><button class="btn btn-small"><i class="fas fa-eye"></i> View</button></div>
                            </div>
                        </div>
                    </div>
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
        });
    </script>
</body>
</html>