<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect if not logged in or not barangay staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'barangay_staff') {
    header('Location: ../index.php');
    exit;
}

$barangayName = htmlspecialchars($_SESSION['barangay'] ?? 'Unknown Barangay');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPRAS Dashboard - Barangay <?php echo $barangayName; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/barangay-sidebar.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Page-specific styles for dashboard */
        .dashboard-panels { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px; }
        .left-panel, .right-panel { display: flex; flex-direction: column; gap: 20px; }
        .chart-card, .calendar-card, .notifications-card { background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 20px; }
        .chart-card h3, .calendar-card h3, .notifications-card h3 { font-size: 18px; margin-bottom: 15px; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        
        #current-time { font-size: 1.5rem; font-weight: 600; color: var(--primary); text-align: center; margin-bottom: 10px; }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .calendar-header button { background: none; border: none; font-size: 1.2rem; color: var(--primary); cursor: pointer; padding: 5px; transition: color 0.3s; }
        .calendar-header button:hover { color: var(--secondary); }
        .calendar-header .month-year { font-size: 1.1rem; font-weight: 600; color: var(--dark); }
        .calendar-table { width: 100%; border-collapse: collapse; text-align: center; }
        .calendar-table th, .calendar-table td { padding: 8px; border: 1px solid #eee; }
        .calendar-table th { background: var(--light); color: var(--dark); font-weight: 500; }
        .calendar-table td { color: var(--primary); }
        .calendar-table td.inactive { color: var(--gray); background-color: #f9f9f9; }
        .calendar-table td.today { background: var(--secondary); color: white; border-radius: 50%; font-weight: bold; }
        
        .charts-container { display: flex; flex-direction: column; gap: 20px; }
        .chart-wrapper { position: relative; height: 250px; width: 100%; }
        .notifications-list { max-height: 450px; overflow-y: auto; }
        .notification-item { display: flex; align-items: center; padding: 15px 5px; border-bottom: 1px solid #eee; }
        .notification-item:last-child { border-bottom: none; }
        .notification-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0; }
        .notification-info { flex-grow: 1; }
        .notification-title { font-weight: 600; margin-bottom: 2px; color: var(--primary); }
        .notification-message { font-size: 15px; color: var(--dark); font-weight: bold; margin-bottom: 5px; }
        .notification-time { font-size: 13px; color: var(--dark); font-weight: bold; }
        .status-pending .notification-icon { background: rgba(243, 156, 18, 0.2); color: var(--warning); }
        .status-verified .notification-icon { background: rgba(52, 152, 219, 0.2); color: var(--secondary); }
        .status-rejected .notification-icon { background: rgba(231, 76, 60, 0.2); color: var(--accent); }
        .status-approved .notification-icon { background: rgba(46, 204, 113, 0.2); color: var(--success); }

        @media (max-width: 992px) { .dashboard-panels { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <?php include '../partials/barangay_sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <div class="header-content">
                    <div class="welcome-message" data-first-name="<?php echo htmlspecialchars($_SESSION['first_name']); ?>" data-last-name="<?php echo htmlspecialchars($_SESSION['last_name']); ?>"></div>
                    <h1>Barangay <?php echo $barangayName; ?> Dashboard</h1>
                </div>
                <div class="header-actions">
                    <button class="btn"><i class="fas fa-bell"></i> Notifications</button>
                    <div class="user-info">
                        <div class="user-avatar">
                            <img src="../images/profile_pictures/<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? 'default.jpg'); ?>" alt="Profile Picture">
                        </div>
                        <div class="user-details">
                            <h2><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h2>
                            <p><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $_SESSION['role']))) . ' • ' . htmlspecialchars($_SESSION['barangay']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-panels">
                <div class="left-panel">
                    <h2 style="color: var(--primary);">Application Statistics</h2>
                    <div class="charts-container">
                        <div class="chart-card">
                            <h3><i class="fas fa-chart-pie"></i> Application Status Distribution</h3>
                            <div class="chart-wrapper"><canvas id="statusChart"></canvas></div>
                        </div>
                        <div class="chart-card">
                            <h3><i class="fas fa-chart-bar"></i> Monthly Applications</h3>
                            <div class="chart-wrapper"><canvas id="monthlyChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <div class="right-panel">
                    <div class="calendar-card">
                        <h2 id="current-time"></h2>
                        <h3><i class="fas fa-calendar-alt"></i> Calendar</h3>
                        <div class="calendar-body">
                            <div class="calendar-header">
                                <button id="prev-month"><i class="fas fa-chevron-left"></i></button>
                                <span id="month-year"></span>
                                <button id="next-month"><i class="fas fa-chevron-right"></i></button>
                            </div>
                            <table class="calendar-table">
                                <thead><tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr></thead>
                                <tbody id="calendar-days"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="notifications-card">
                        <h3><i class="fas fa-bell"></i> Recent Applications</h3>
                        <div class="notifications-list" id="realtime-notifications-list">
                            <p>Loading notifications...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer">
                <p>Centralized Profiling and Record Authentication System | Barangay <?php echo $barangayName; ?> &copy; 2024</p>
            </div>
        </div>
    </div>

    <script src="../assets/js/sidebar-toggle.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeWelcomeMessage();
        initializeCalendar();
        updateTime();
        setInterval(updateTime, 1000);

        // Fetch dynamic data for charts and notifications
        fetch('../api/barangay_dashboard_data.php')
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    initializeCharts(result.data);
                    renderNotifications(result.data.notifications);
                } else {
                    console.error('Failed to load dashboard data:', result.message);
                    document.getElementById('realtime-notifications-list').innerHTML = '<p>Could not load notifications.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching dashboard data:', error);
                document.getElementById('realtime-notifications-list').innerHTML = '<p>Error loading notifications.</p>';
            });
    });

    function updateTime() {
        const timeEl = document.getElementById('current-time');
        if (timeEl) {
            timeEl.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
    }

    function initializeWelcomeMessage() {
        const welcomeMessage = document.querySelector('.welcome-message');
        const firstName = welcomeMessage.dataset.firstName;
        const lastName = welcomeMessage.dataset.lastName;
        const hour = new Date().getHours();
        let greeting = (hour < 12) ? "Good morning" : (hour < 18) ? "Good afternoon" : "Good evening";
        welcomeMessage.innerHTML = `${greeting}, <strong>${firstName} ${lastName}</strong>!`;
    }

    function initializeCalendar() {
        const monthYearEl = document.getElementById('month-year');
        const calendarDaysEl = document.getElementById('calendar-days');
        const prevMonthBtn = document.getElementById('prev-month');
        const nextMonthBtn = document.getElementById('next-month');
        if (!monthYearEl || !calendarDaysEl || !prevMonthBtn || !nextMonthBtn) return;
        let currentDate = new Date();
        function renderCalendar() {
            const year = currentDate.getFullYear(), month = currentDate.getMonth();
            const today = new Date();
            const firstDayOfMonth = new Date(year, month, 1), lastDayOfMonth = new Date(year, month + 1, 0);
            const firstDayOfWeek = firstDayOfMonth.getDay(), totalDays = lastDayOfMonth.getDate();
            const prevMonthDays = new Date(year, month, 0).getDate();
            monthYearEl.textContent = `${firstDayOfMonth.toLocaleString('default', { month: 'long' })} ${year}`;
            calendarDaysEl.innerHTML = '';
            let date = 1, nextMonthDate = 1;
            for (let i = 0; i < 6; i++) {
                const row = document.createElement('tr');
                let weekHasDays = false;
                for (let j = 0; j < 7; j++) {
                    const cell = document.createElement('td');
                    if (i === 0 && j < firstDayOfWeek) {
                        cell.textContent = prevMonthDays - firstDayOfWeek + j + 1;
                        cell.classList.add('inactive');
                    } else if (date > totalDays) {
                        cell.textContent = nextMonthDate++;
                        cell.classList.add('inactive');
                    } else {
                        cell.textContent = date;
                        if (date === today.getDate() && month === today.getMonth() && year === today.getFullYear()) cell.classList.add('today');
                        date++;
                        weekHasDays = true;
                    }
                    row.appendChild(cell);
                }
                if (weekHasDays || i === 0) calendarDaysEl.appendChild(row);
            }
        }
        prevMonthBtn.addEventListener('click', () => { currentDate.setMonth(currentDate.getMonth() - 1); renderCalendar(); });
        nextMonthBtn.addEventListener('click', () => { currentDate.setMonth(currentDate.getMonth() + 1); renderCalendar(); });
        renderCalendar();
    }

    function renderNotifications(notifications) {
        const listEl = document.getElementById('realtime-notifications-list');
        if (!listEl) return;
        if (notifications.length === 0) {
            listEl.innerHTML = '<p style="text-align: center; padding: 20px; color: var(--gray);">No recent applications.</p>';
            return;
        }
        listEl.innerHTML = '';
        notifications.forEach(notif => {
            const item = document.createElement('div');
            const statusClass = notif.status.toLowerCase().replace(' ', '-');
            const iconClass = { pending: 'fa-clock', verified: 'fa-check', rejected: 'fa-times', approved: 'fa-check-double' }[statusClass] || 'fa-info-circle';
            
            const dateObj = new Date(notif.date_submitted.replace(' ', 'T')); // Parse with 'T' for reliability
            const year = dateObj.getFullYear();
            const month = (dateObj.getMonth() + 1).toString().padStart(2, '0'); // Months are 0-indexed
            const day = dateObj.getDate().toString().padStart(2, '0');
            const hours = dateObj.getHours().toString().padStart(2, '0');
            const minutes = dateObj.getMinutes().toString().padStart(2, '0');
            const seconds = dateObj.getSeconds().toString().padStart(2, '0');

            const formattedDateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

            item.className = `notification-item status-${statusClass}`;
            item.innerHTML = `
                <div class="notification-icon"><i class="fas ${iconClass}"></i></div>
                <div class="notification-info">
                    <div class="notification-title">${notif.full_name}</div>
                    <div class="notification-message">${notif.application_type} - Status: ${notif.status}</div>
                    <div class="notification-time">${formattedDateTime}</div>
                </div>
            `;
            listEl.appendChild(item);
        });
    }

    function getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " years ago";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " months ago";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " days ago";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " hours ago";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " minutes ago";
        return Math.floor(seconds) + " seconds ago";
    }

    function initializeCharts(data) {
        // --- Status Chart ---
        const statusCtx = document.getElementById('statusChart')?.getContext('2d');
        if (statusCtx && data.stats) {
            const labels = data.stats.map(s => s.status);
            const counts = data.stats.map(s => s.count);
            const backgroundColors = labels.map(label => {
                switch(label.toLowerCase()) {
                    case 'pending': return '#f39c12';
                    case 'verified': return '#3498db';
                    case 'rejected': return '#e74c3c';
                    case 'approved': return '#2ecc71';
                    default: return '#95a5a6';
                }
            });
            new Chart(statusCtx, {
                type: 'doughnut',
                data: { labels, datasets: [{ data: counts, backgroundColor: backgroundColors, borderWidth: 2, borderColor: '#fff' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });
        }

        // --- Monthly Chart ---
        const monthlyCtx = document.getElementById('monthlyChart')?.getContext('2d');
        if (monthlyCtx && data.monthly) {
            const twelveMonths = [...Array(12)].map((_, i) => {
                const d = new Date();
                d.setMonth(d.getMonth() - i);
                return d.toLocaleString('default', { month: 'short' });
            }).reverse();

            const pwdData = Array(12).fill(0);
            const seniorData = Array(12).fill(0);

            data.monthly.forEach(item => {
                const monthIndex = twelveMonths.indexOf(item.month.substring(0, 3));
                if (monthIndex > -1) {
                    if (item.application_type === 'PWD') {
                        pwdData[monthIndex] = item.count;
                    } else if (item.application_type === 'Senior Citizen') {
                        seniorData[monthIndex] = item.count;
                    }
                }
            });

            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: twelveMonths,
                    datasets: [
                        { label: 'PWD Applications', data: pwdData, backgroundColor: '#3498db' },
                        { label: 'Senior Citizen Applications', data: seniorData, backgroundColor: '#2ecc71' }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        }
    }
    </script>
</body>
</html>