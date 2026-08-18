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

$email = $_SESSION['email']; 
    $query = "SELECT first_name, last_name FROM tenant_tbl WHERE email = '$email'";
    $nresult = mysqli_query($con,$query);
    $nrow = mysqli_fetch_array($nresult);

$results_per_page = 5;
$results_view     = 4;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($current_page - 1) * $results_per_page;

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
                <h3>Manage Units</h3>                     
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
<div class="brief-info anim">
                    <div class="total"> 
                    <?php $query = "SELECT COUNT(*) AS tcount FROM tenant_tbl WHERE status_id = '11'";
                            $result = mysqli_query($con,$query);
                            $row = mysqli_fetch_assoc($result);
                            $tcount = $row['tcount'];?>
                        <div class="middle">
                            <div class="left">
                                <h2 class="text-muted">Active <span class="warning">Tenant</span></h2>
                                <h1><?php echo $tcount; ?></h1>
                            </div>
                        </div>
                    </div>

                    <div class="total">  
                    <?php $query = "SELECT COUNT(*) AS rcount FROM room_tbl WHERE status_id = '11'";
                            $result = mysqli_query($con,$query);
                            $row = mysqli_fetch_assoc($result);
                            $rcount = $row['rcount'];?>
                        <div class="middle">
                            <div class="left">
                                <h2 class="text-muted">Active <span class="warning">Units</span></h2>
                                <h1><?php echo $rcount; ?></h1>
                            </div>
                        </div>
                    </div>

                    <div class="status">
                        <div class="middle">
                            <h2 class="text-muted">Current <span class="warning">Time</span></h2>
                            <div id="time"></div>
                        </div>
                    </div>
                </div>

<sub>
<div class="sub-recent anim t1">
        <h2 class="text-muted">Recent <span class="warning">Tenants</span></h2>
        <table>
            <thead>
                <tr>
                    <th>LAST NAME</th>
                    <th>EMAIL ADDRESS</th>
                    <th class="warning">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT tenant_tbl.tenant_id, tenant_tbl.last_name, 
                            tenant_tbl.email, status.status_id, status.status_type
                            FROM `tenant_tbl`
                            LEFT JOIN status
                            ON tenant_tbl.status_id = status.status_id 
                            WHERE status.status_id = 11 AND tenant_tbl.user_type_id = 2
                            ORDER BY tenant_tbl.tenant_id DESC
                            LIMIT $start_from, $results_view";
                $result = mysqli_query($con, $query);

                if ($result->num_rows > 0) {
                    foreach ($result as $row) { ?>
                        <tr>
                            <td><?= $row['last_name'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['status_type'] ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="3" class="text-center">No Recent Tenants Available</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
                   
    <div class="sub-recent anim t2">
        <h2 class="text-muted">Recent Paid <span class="warning">Tenants</span></h2>
        <table>
            <thead>
                <tr>
                    <th>LAST NAME</th>
                    <th>DATE PAID</th>
                    <th>PURPOSE</th>
                    <th class="warning">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM payment_tbl
                            INNER JOIN status 
                            ON payment_tbl.status_id = status.status_id
                            INNER JOIN tenant_tbl 
                            ON payment_tbl.tenant_id = tenant_tbl.tenant_id
                            INNER JOIN payment 
                            ON payment_tbl.payment_type_id = payment.payment_type_id
                            INNER JOIN purpose 
                            ON payment_tbl.purpose_id = purpose.purpose_id
                            WHERE payment_tbl.status_id = 5 AND payment_tbl.date_paid IS NOT NULL
                            ORDER BY payment_tbl.date_paid DESC
                            LIMIT $start_from, $results_view";
                $result = mysqli_query($con, $query);

                if ($result->num_rows > 0) {
                    foreach ($result as $row) { ?>
                        <tr>
                            <td><?= $row['last_name'] ?></td>
                            <td><?= $row['date_paid'] ?></td>
                            <td><?= $row['purpose_type'] ?></td>
                            <td><?= $row['status_type'] ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="4" class="text-center">No Recent Paid Tenants Available</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="sub-recent anim t3">
        <h2 class="text-muted">Pending Pay <span class="warning">Tenants</span></h2>
        <table>
            <thead>
                <tr>
                    <th>LAST NAME</th>
                    <th>DATE ENTRY</th>
                    <th>PURPOSE</th>
                    <th class="warning">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM payment_tbl
                            INNER JOIN status 
                            ON payment_tbl.status_id = status.status_id
                            INNER JOIN tenant_tbl 
                            ON payment_tbl.tenant_id = tenant_tbl.tenant_id
                            INNER JOIN payment 
                            ON payment_tbl.payment_type_id = payment.payment_type_id
                            INNER JOIN purpose 
                            ON payment_tbl.purpose_id = purpose.purpose_id
                            WHERE payment_tbl.status_id = 0
                            ORDER BY payment_tbl.date_entry DESC
                            LIMIT $start_from, $results_view";
                $result = mysqli_query($con, $query);

                if ($result->num_rows > 0) {
                    foreach ($result as $row) { ?>
                        <tr>
                            <td><?= $row['last_name'] ?></td>
                            <td><?= $row['date_entry'] ?></td>
                            <td><?= $row['purpose_type'] ?></td>
                            <td><?= $row['status_type'] ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="4" class="text-center">No Pending Payments Available</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</sub>                

<div class="mid">
    <!-- Recent Occupied Rooms -->
    <div class="recent-table anim t1">
        <h1 class="text-muted">Recent Occu<span class="warning">pied Units</span></h1>
        <table>
            <thead>
                <tr>
                    <th>UNIT NUMBER</th>
                    <th>EMAIL ADDRESS</th>
                    <th>REMAINING DAY(S)</th>
                    <th class="warning">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT room_tbl.*, tenant_tbl.tenant_id, tenant_tbl.email,
                        status.status_id, status.status_type
                        FROM `room_tbl`
                        INNER JOIN `tenant_tbl`
                        ON room_tbl.tenant_id = tenant_tbl.tenant_id
                        INNER JOIN `status`
                        ON room_tbl.status_id = status.status_id 
                        WHERE room_tbl.status_id = 11
                        ORDER BY room_tbl.room_id DESC
                        LIMIT $start_from, $results_per_page";
                $result = mysqli_query($con, $query);

                if ($result->num_rows > 0) {
                    foreach ($result as $row) { ?>
                        <tr>
                            <td><?= $row['room_number'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td>
                                <?php 
                                $dateOut = new DateTime($row['date_out']);
                                $currentDate = new DateTime();
                                $interval = $currentDate->diff($dateOut);

                                if ($interval->y > 0) {
                                    echo $interval->y . " year" . ($interval->y > 1 ? "s" : "") . " left";
                                } elseif ($interval->m > 0) {
                                    echo $interval->m . " month" . ($interval->m > 1 ? "s" : "") . " left";
                                } elseif ($interval->d > 0) {
                                    echo $interval->d . " day" . ($interval->d > 1 ? "s" : "") . " left";
                                } else {
                                    echo "Expiring soon";
                                }
                                ?>
                            </td>
                            <td><?= $row['status_type'] ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="4" class="text-center">No Recent Occupied Units Available</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

 <!-- Recent Tenants -->
 <div class="recent-table anim t2">
        <h1 class="text-muted">Recent <span class="warning">Tenants</span></h1>
        <table>
            <thead>
                <tr>
                    <th>FIRST NAME</th>
                    <th>LAST NAME</th>
                    <th>CONTACT NUMBER</th>
                    <th>EMAIL ADDRESS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT tenant_tbl.tenant_id , tenant_tbl.first_name , tenant_tbl.last_name , 
                            tenant_tbl.contacts , tenant_tbl.email , 
                            status.status_id , status.status_type
                            FROM `tenant_tbl`
                            LEFT JOIN status
                            ON tenant_tbl.status_id = status.status_id 
                            WHERE tenant_tbl.user_type_id = 2
                            ORDER BY tenant_tbl.tenant_id DESC
                            LIMIT $start_from, $results_per_page";

                $result = mysqli_query($con, $query);

                if ($result->num_rows > 0) {
                    foreach ($result as $row) { ?>
                        <tr>
                            <td><?= $row['first_name'] ?></td>
                            <td><?= $row['last_name'] ?></td>
                            <td><?= $row['contacts'] ?></td>
                            <td><?= $row['email'] ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="4" class="text-center">No Recent Tenants Available</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Recent Reservations -->
    <div class="recent-table anim t3" style="margin-bottom: 40px;">
        <h1 class="text-muted">Recent <span class="warning">Reservations</span></h1>
        <table>
            <thead>
                <tr>
                    <th>FIRST NAME</th>
                    <th>LAST NAME</th>
                    <th>CONTACT NUMBER</th>
                    <th>EMAIL ADDRESS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT reserve_tbl.reserve_id , reserve_tbl.res_fname , reserve_tbl.res_lname , 
                            reserve_tbl.res_email , reserve_tbl.res_contact
                            FROM `reserve_tbl`
                            ORDER BY reserve_tbl.reserve_id DESC
                            LIMIT $start_from, $results_per_page";
                $result = mysqli_query($con, $query);

                if ($result->num_rows > 0) {
                    foreach ($result as $row) { ?>
                        <tr>
                            <td><?= $row['res_fname'] ?></td>
                            <td><?= $row['res_lname'] ?></td>
                            <td><?= $row['res_contact'] ?></td>
                            <td><?= $row['res_email'] ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="4" class="text-center">No Recent Reservations Available</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
            </main>            
        </div>
    <script src="notifications.js"></script>
    <script>
        function updateTime() {
            const now = new Date();
            const options = { timeZone: 'Asia/Manila', hour12: true, hour: 'numeric', minute: 'numeric', second: 'numeric' };
            const currentTime = now.toLocaleTimeString('en-US', options);
            document.getElementById('time').textContent = currentTime;
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>
    </body>
</html>