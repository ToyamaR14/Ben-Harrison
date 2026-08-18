<?php
require ('db.php');
include ('auth_session.php');
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['tenant_id']) || !isset($_SESSION['user_type_id'])) {
    header("Location: login.php");
    exit();
}
// Check if user is an admin (assuming user_type_id 1 is admin)
if ($_SESSION['user_type_id'] != 1) {
    header("Location: index.php"); // Redirect to normal user page
    exit();
}

$query_total = "SELECT SUM(amount) AS total_revenue FROM payment_tbl WHERE status_id = 5";
$result_total = mysqli_query($con, $query_total);
$total_revenue = ($row = mysqli_fetch_assoc($result_total)) ? $row['total_revenue'] : 0;

// Fetch weekly earnings for the last 7 weeks
$query_weekly = "SELECT DATE_FORMAT(date_paid, '%Y-%u') AS week, SUM(amount) AS weekly_total
                FROM payment_tbl
                WHERE status_id = 5 
                AND YEARWEEK(date_paid, 1) >= YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 7 WEEK), 1)
                GROUP BY DATE_FORMAT(date_paid, '%Y-%u')
                ORDER BY date_paid DESC
                LIMIT 7";
$result_weekly = mysqli_query($con, $query_weekly);

$weeks = [];
$earnings = [];

while ($row = mysqli_fetch_assoc($result_weekly)) {
    $week = $row['week'];
    $week_parts = explode('-', $week); 
    $year = $week_parts[0];
    $week_number = $week_parts[1];

    $start_of_week = strtotime($year . "W" . $week_number . "1");
    $month_name = date('F', $start_of_week);
    $formatted_week = $month_name . " Week " . $week_number;

    $weeks[] = $formatted_week;
    $earnings[] = $row['weekly_total'];
}
$weeks_js = json_encode(array_reverse($weeks)); 
$earnings_js = json_encode(array_reverse($earnings));

$query_monthly = "SELECT DATE_FORMAT(date_paid, '%Y-%m') AS month, SUM(amount) AS monthly_total 
                  FROM payment_tbl 
                  WHERE status_id = 5 
                  AND amount IS NOT NULL
                  AND amount > 0
                  AND date_paid >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), '%Y-%m-01') 
                  GROUP BY DATE_FORMAT(date_paid, '%Y-%m') 
                  ORDER BY date_paid DESC";
$result_monthly = mysqli_query($con, $query_monthly);

$months = [];
$monthly_earnings = [];

while ($row = mysqli_fetch_assoc($result_monthly)) {
    // Convert 'YYYY-MM' to 'January 2025'
    $formatted_month = date('F Y', strtotime($row['month'] . '-01'));
    $months[] = $formatted_month;
    $monthly_earnings[] = $row['monthly_total'];
}

$months_js = json_encode(array_reverse($months)); 
$monthly_earnings_js = json_encode(array_reverse($monthly_earnings));

// Fetch recent payments
// Fetch recent payments for the current week
$query_recent = "SELECT t.first_name, p.amount, p.date_paid
                    FROM payment_tbl p 
                    JOIN tenant_tbl t ON p.tenant_id = t.tenant_id 
                    WHERE p.status_id = 5
                    AND YEARWEEK(p.date_paid, 1) = YEARWEEK(CURDATE(), 1)
                    ORDER BY p.date_paid DESC
                    LIMIT 6";
$result_recent = mysqli_query($con, $query_recent);

$email = $_SESSION['email']; 
    $query = "SELECT first_name, last_name FROM tenant_tbl WHERE email = '$email'";
    $nresult = mysqli_query($con,$query);
    $nrow = mysqli_fetch_array($nresult);

// Fetch today's earnings
$query_today = "SELECT SUM(amount) AS today_total 
                FROM payment_tbl 
                WHERE status_id = 5 AND DATE(date_paid) = CURDATE()";
$result_today = mysqli_query($con, $query_today);
$today_earnings = ($row = mysqli_fetch_assoc($result_today)) ? $row['today_total'] : 0;

// Fetch weekly earnings (last 7 days)
$query_weekly = "SELECT SUM(amount) AS weekly_total 
                 FROM payment_tbl 
                 WHERE status_id = 5 AND DATE(date_paid) >= CURDATE() - INTERVAL 7 DAY";
$result_weekly = mysqli_query($con, $query_weekly);
$weekly_earnings = ($row = mysqli_fetch_assoc($result_weekly)) ? $row['weekly_total'] : 0;

// Fetch monthly earnings (current month)
$query_monthly = "SELECT SUM(amount) AS monthly_total 
                  FROM payment_tbl 
                  WHERE status_id = 5 AND MONTH(date_paid) = MONTH(CURDATE()) 
                  AND YEAR(date_paid) = YEAR(CURDATE())";
$result_monthly = mysqli_query($con, $query_monthly);
$monthly_earnings = ($row = mysqli_fetch_assoc($result_monthly)) ? $row['monthly_total'] : 0;

// Fetch total for Miscellaneous payments
$query_total_miscellaneous = "SELECT SUM(amount) AS total_miscellaneous 
                              FROM payment_tbl 
                              WHERE purpose_id = 4 AND status_id = 5";
$result_miscellaneous = mysqli_query($con, $query_total_miscellaneous);

// Fetch total for Maintenance payments
$query_total_maintenance = "SELECT SUM(amount) AS total_maintenance 
                            FROM payment_tbl 
                            WHERE purpose_id = 5 AND status_id = 5";
$result_maintenance = mysqli_query($con, $query_total_maintenance);

// Fetch total rent earned
$query_total_rent = "SELECT SUM(amount) AS total_rent 
                     FROM payment_tbl 
                     WHERE purpose_id = 1 AND status_id = 5";
$result_rent = mysqli_query($con, $query_total_rent);

// Fetch results
$total_miscellaneous = ($row = mysqli_fetch_assoc($result_miscellaneous)) ? $row['total_miscellaneous'] : 0;
$total_maintenance = ($row = mysqli_fetch_assoc($result_maintenance)) ? $row['total_maintenance'] : 0;
$total_rent = ($row = mysqli_fetch_assoc($result_rent)) ? $row['total_rent'] : 0;

?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="utf-8"/>
    <title>Home</title>
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=1280, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="styles.css?v=2.0">
</head>
<body>
    <div class="container">
        <div class="user">
        <h2 class="showemail text-muted">Welcome, <span class="showemail success"><?php echo $nrow['first_name']; ?></span></h2>

        <div class="notification-container">
            <div class="notification-icon" id="notifBell">
                <span class="material-icons-sharp">notifications</span>
                <span class="notif-count" id="notifCount">0</span>
            </div>

            <div class="notification-dropdown" id="notifDropdown">
                <h3>Notifications</h3>
                <ul id="notifList"></ul>
            </div>
        </div>
    </div>

        <aside>
            <div class="top">
                <div class="logo">
                <h2 class="text-muted">Ben Harrison <span class="danger">Residence</span></h2>
            </div>
        </div>
        <div class="sidebar">
            <a href="dashboard.php">
                <span class="material-icons-sharp">home</span>
                <h3>Dashboard</h3>
            </a>

            <a href="reservation.php">
                <span class="material-icons-sharp">menu_book</span>
                <h3>Reservations</h3>
            </a>

            <a href="room.php">
                <span class="material-icons-sharp">room_preferences</span>
                <h3>Manage Rooms</h3>                     
            </a>

            <a href="manage.php">
                <span class="material-icons-sharp">manage_accounts</span>
                <h3>Manage Tenants</h3>
            </a>

            <a href="inventory.php">
                <span class="material-icons-sharp">warehouse</span>
                <h3>Manage Inventory</h3>
            </a>
            
            <a href="maintenance.php">
                <span class="material-icons-sharp">engineering</span>
                <h3>Maintenance</h3>
            </a>

            <a href="notification.php">
                <span class="material-icons-sharp">announcement</span>
                <h3>View Messages</h3>
            </a>

            <a href="inbox.php">
                <span class="material-icons-sharp">announcement</span>
                <h3>Tenant Messages</h3>
            </a>

            <a href="payment.php">
                <span class="material-icons-sharp">receipt_long</span>
                <h3>Payments</h3>
            </a>

            <a href="stats.php">
                <span class="material-icons-sharp">analytics</span>
                <h3>Financial Summary</h3>
            </a>

            <a href="logs.php">
                <span class="material-icons-sharp">history</span>
                <h3>Logs</h3>
            </a>
            
            <a href="logout.php">
                <span class="material-icons-sharp">logout</span>
                <h3>Logout</h3>
            </a>
                </div>
        </aside>
<main>     

<div class="container1">
    <h1 class="text-muted">Payment <span class="warning">Statistics</span></h1>
    <button id="exportExcel" class="export-btn">Export All Payment Data to Excel</button>
    <button id="exportExcel7Days" class="export-btn">Export Last 7 Days to Excel</button>

    <div class="stats-box">
        <div class="card">
            <h3>Total Revenue</h3>
            <p style="font-size: 1.5em; font-weight: bold;">₱<?php echo number_format($total_revenue, 2); ?></p>
        </div>
    </div>

    <div class="chart-container-wrapper">
        <div class="chart-container">
            <h3>Weekly Revenue (Last 7 Weeks)</h3>
            <canvas id="earningsChart"></canvas>
        </div>

        <div class="chart-container">
            <h3>Monthly Revenue (Last 6 Months)</h3>
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <div class="chart-card-wrapper">
        <div class="card">
            <h2>Today's Earnings</h2>
                <p style="font-size: 1.5em; font-weight: bold;">₱<?php echo number_format($today_earnings, 2); ?></p>
            <hr>
            <h2>Weekly Earnings</h2>
                <p style="font-size: 1.5em; font-weight: bold;">₱<?php echo number_format($weekly_earnings, 2); ?></p>
            <hr>
            <h2>Monthly Earnings</h2>
                <p style="font-size: 1.5em; font-weight: bold;">₱<?php echo number_format($monthly_earnings, 2); ?></p>
        </div>

        <div class="card">
            <h2><strong>Recent Payments (Last 7 Days)</strong></h2>
            <ul>
                <?php
                if (mysqli_num_rows($result_recent) > 0) {
                    while ($row = mysqli_fetch_assoc($result_recent)) { ?>
                        <li>
                            <strong><?php echo $row['first_name']; ?></strong> paid 
                            <strong>₱<?php echo number_format($row['amount'], 2); ?></strong> 
                            on <strong><?php echo date("F j, Y", strtotime($row['date_paid'])); ?></strong>
                        </li>
                    <?php }
                } else {
                    echo '<li>No recent payments</li>';
                }
                ?>
            </ul>
        </div>

        <div class="card">
            <h2>Total Rent Earned</h2>
                <p style="font-size: 1.5em; font-weight: bold;">₱<?php echo number_format($total_rent, 2); ?></p>
            <hr>
            <h2>Total Miscellaneous Costs</h2>
                <p style="font-size: 1.5em; font-weight: bold;">₱<?php echo number_format($total_miscellaneous, 2); ?></p>
            <hr>
            <h2>Total Maintenance Costs</h2>
                <p style="font-size: 1.5em; font-weight: bold;">₱<?php echo number_format($total_maintenance, 2); ?></p>
        </div>

    </div>
</div>

</main>            
        </div>
    <script src="notifications.js"></script>
    <script>
        const weeklyCtx = document.getElementById('earningsChart').getContext('2d');
        const earningsChart = new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: <?php echo $weeks_js; ?>,
                datasets: [{
                    label: 'Weekly Revenue (₱)',
                    data: <?php echo $earnings_js; ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Monthly Revenue Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $months_js; ?>, // Unique months
                datasets: [{
                    label: 'Monthly Revenue (₱)',
                    data: <?php echo $monthly_earnings_js; ?>, // Earnings data
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

    document.getElementById("exportExcel").addEventListener("click", function () {
    window.location.href = "export_excel.php";
        });
    document.getElementById("exportExcel7Days").addEventListener("click", function () {
    window.location.href = "export_excel_seven.php?days=7";
        });
    </script>
    </body>
</html>