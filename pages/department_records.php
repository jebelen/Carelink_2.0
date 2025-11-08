<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/barangays_list.php'; // Ensure barangays_list.php is included

// --- BACKEND LOGIC ---
// Authenticate and authorize
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'department_admin') {
    header('Location: ../index.php');
    exit;
}

// 1. Get filter, search, and pagination parameters from URL
$search = $_GET['search'] ?? '';
$barangayFilter = $_GET['barangay'] ?? 'all';
$typeFilter = $_GET['type'] ?? 'all';
$statusFilter = $_GET['status'] ?? 'all';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$recordsPerPage = 15; // Number of records to display per page
$offset = ($page - 1) * $recordsPerPage;

// 2. Build the database query dynamically
$baseQuery = "FROM applications";
$whereClauses = [];
$params = [];

if (!empty($search)) {
    $whereClauses[] = "(full_name LIKE :search OR id LIKE :search)";
    $params[':search'] = "%$search%";
}
if ($barangayFilter !== 'all') {
    $whereClauses[] = "barangay = :barangay";
    $params[':barangay'] = $barangayFilter;
}
if ($typeFilter !== 'all') {
    $whereClauses[] = "application_type = :type"; // Match the column name
    $params[':type'] = $typeFilter;
}
if ($statusFilter !== 'all') {
    $whereClauses[] = "status = :status";
    $params[':status'] = $statusFilter;
}

$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = " WHERE " . implode(' AND ', $whereClauses);
}

// 3. Get total number of records for pagination
$totalQuery = "SELECT COUNT(*) " . $baseQuery . $whereSql;
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->execute($params);
$totalRecords = $totalStmt->fetchColumn();
$totalPages = ceil($totalRecords / $recordsPerPage);

// 4. Get the records for the current page
$recordsQuery = "SELECT * " . $baseQuery . $whereSql . " ORDER BY date_submitted DESC LIMIT :limit OFFSET :offset";
$recordsStmt = $conn->prepare($recordsQuery);

// Bind all parameters including limit and offset
foreach ($params as $key => &$val) {
    if (strpos($key, ':limit') === false && strpos($key, ':offset') === false) { // Don't bind limit/offset with foreach
        $recordsStmt->bindParam($key, $val);
    }
}
$recordsStmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
$recordsStmt->bindParam(':offset', $offset, PDO::PARAM_INT);

$recordsStmt->execute();
$applications = $recordsStmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to get status class for styling
function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'pending': return 'status-pending';
        case 'verified': return 'status-verified';
        case 'rejected': return 'status-rejected';
        case 'approved': return 'status-approved';
        case 'sent to city hall': return 'status-sent'; // Add this if 'sent' is a status
        default: return 'status-default';
    }
}
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
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr; /* Adjusted for ID Number */
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
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr; /* Adjusted for ID Number */
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

        /* Pagination styles */
        .pagination {
            display: flex;
            justify-content: center; /* Center pagination */
            padding: 20px;
            gap: 10px;
            align-items: center;
        }

        .pagination-links a,
        .pagination-links span {
            padding: 8px 12px;
            border: 1px solid var(--gray);
            border-radius: 5px;
            text-decoration: none;
            color: var(--primary);
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination-links a:hover {
            background-color: var(--secondary);
            color: white;
            border-color: var(--secondary);
        }

        .pagination-links .current-page {
            background-color: var(--secondary);
            color: white;
            border-color: var(--secondary);
        }

        .pagination-links .disabled {
            color: var(--gray);
            cursor: not-allowed;
        }
        .no-records { padding: 20px; text-align: center; color: var(--gray); }
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
                            <h2><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h2>
                            <p><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $_SESSION['role']))); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Records Section -->
            <div class="records-section">
                <form method="GET" action="department_records.php">
                    <div class="records-header">
                        <h2>All Records for Pasig City</h2>
                        <div class="records-actions">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" placeholder="Search records..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <button type="submit" class="btn"><i class="fas fa-filter"></i> Filter</button>
                            <button type="button" class="btn" onclick="window.location.href='department_records.php'"><i class="fas fa-redo"></i> Reset</button>
                            <button type="button" class="btn"><i class="fas fa-download"></i> Export</button>
                        </div>
                    </div>
                    
                    <!-- Filter Section -->
                    <div class="filter-section">
                        <div class="filter-group">
                            <label for="barangay-filter">Barangay</label>
                            <select id="barangay-filter" name="barangay">
                                <option value="all">All Barangays</option>
                                <?php foreach ($barangays_list as $b): ?>
                                    <option value="<?php echo htmlspecialchars($b); ?>" <?php echo ($barangayFilter === $b) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="type-filter">Application Type</label>
                            <select id="type-filter" name="type">
                                <option value="all">All Types</option>
                                <option value="PWD" <?php echo ($typeFilter === 'PWD') ? 'selected' : ''; ?>>PWD</option>
                                <option value="Senior Citizen" <?php echo ($typeFilter === 'Senior Citizen') ? 'selected' : ''; ?>>Senior Citizen</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="status-filter">Status</label>
                            <select id="status-filter" name="status">
                                <option value="all">All Status</option>
                                <option value="Pending" <?php echo ($statusFilter === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Verified" <?php echo ($statusFilter === 'Verified') ? 'selected' : ''; ?>>Verified</option>
                                <option value="Rejected" <?php echo ($statusFilter === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                <option value="Approved" <?php echo ($statusFilter === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                            </select>
                        </div>
                    </div>
                </form>
                
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
                            <?php if (empty($applications)): ?>
                                <div class="no-records">No records found matching your criteria.</div>
                            <?php else: ?>
                                <?php foreach ($applications as $app): ?>
                                    <div class="table-row">
                                        <div><?php echo htmlspecialchars($app['full_name']); ?></div>
                                        <div><?php echo htmlspecialchars($app['application_type']); ?></div>
                                        <div><?php echo htmlspecialchars($app['barangay']); ?></div>
                                        <div><?php echo date("M d, Y", strtotime($app['date_submitted'])); ?></div>
                                        <div><span class="application-status <?php echo getStatusClass($app['status']); ?>"><?php echo htmlspecialchars($app['status']); ?></span></div>
                                        <div><?php echo htmlspecialchars($app['id']); ?></div> <?php // Assuming ID Number refers to application ID ?>
                                        <div><a href="view_application.php?id=<?php echo $app['id']; ?>" class="btn btn-small"><i class="fas fa-eye"></i> View</a></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <div class="pagination-info">
                            Page <?php echo $page; ?> of <?php echo $totalPages; ?> &bull; Total: <?php echo $totalRecords; ?> records
                        </div>
                        <div class="pagination-links">
                            <?php
                            $queryString = http_build_query(array_filter(['search' => $search, 'barangay' => $barangayFilter, 'type' => $typeFilter, 'status' => $statusFilter]));
                            
                            if ($page > 1) { echo '<a href="?page=' . ($page - 1) . '&' . $queryString . '">Previous</a>'; } else { echo '<span class="disabled">Previous</span>'; }

                            for ($i = 1; $i <= $totalPages; $i++) {
                                if ($i == $page) { echo '<span class="current-page">' . $i . '</span>'; }
                                else { echo '<a href="?page=' . $i . '&' . $queryString . '">' . $i . '</a>'; }
                            }

                            if ($page < $totalPages) { echo '<a href="?page=' . ($page + 1) . '&' . $queryString . '">Next</a>'; } else { echo '<span class="disabled">Next</span>'; }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
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
            } else {
                greeting = "Good evening";
            }
            
            welcomeMessage.innerHTML = `${greeting}, <strong>${firstName} ${lastName}</strong>!`;
        });
    </script>
</body>
</html>