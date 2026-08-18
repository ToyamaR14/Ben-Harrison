<?php 
require ('db.php');
include ('auth_session.php');
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['tenant_id']) || $_SESSION['user_type_id'] !== '2') {
    header("Location: homepage.php"); // Redirect to login page if unauthorized
    exit();
}

$user_id = $_SESSION['email'];
$stmt = $con->prepare("SELECT tenant_tbl.*, status.*, payment_tbl.*, user_type.*, room_tbl.*
                       FROM payment_tbl
                       JOIN status ON payment_tbl.status_id = status.status_id
                       JOIN tenant_tbl ON payment_tbl.tenant_id = tenant_tbl.tenant_id
                       JOIN user_type ON tenant_tbl.user_type_id = user_type.user_type_id
                       JOIN room_tbl ON tenant_tbl.tenant_id = room_tbl.tenant_id
                       WHERE tenant_tbl.email = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array();

$query1 = "SELECT payment_tbl.amount, payment_tbl.date_paid, status.status_id, status.status_type, tenant_tbl.tenant_id, tenant_tbl.email
            FROM payment_tbl
            JOIN status ON payment_tbl.status_id = status.status_id
            JOIN tenant_tbl ON payment_tbl.tenant_id = tenant_tbl.tenant_id
            WHERE tenant_tbl.email = '$user_id'
            ORDER BY payment_tbl.date_paid DESC
            LIMIT 3";
$result1 = mysqli_query($con,$query1);


$query = "SELECT tenant_id FROM tenant_tbl WHERE email = '$user_id'";
$result = mysqli_query($con, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $tenant_id = $row['tenant_id'];

    $query = "SELECT COUNT(*) AS count FROM message_tbl WHERE status_id = '3' AND tenant_id = '$tenant_id'";
    $result = mysqli_query($con, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];
    } else {
        echo "Error: " . mysqli_error($con);
    }
} else {
    echo "Error: " . mysqli_error($con);
}

$email = $_SESSION['email']; 
    $query = "SELECT first_name FROM tenant_tbl WHERE email = '$email'";
    $nresult = mysqli_query($con,$query);
    $nrow = mysqli_fetch_array($nresult);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="utf-8"/>
    <title>Maintenance Request</title>
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="styler.css?v=2.0">
    <link rel="manifest" href="/manifest.json">
</head>
<body>
    <div class="container">
        <aside>
            <div class="top">
                <div class="logo">
                    <h2 class="text-muted">Ben <span class="danger">Harrison</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>
            <div class="user">
                    <h2 class="showemail text-muted">Hello! <span class="danger"><?php echo $nrow['first_name'];?></span></h2>
            </div>
            
                <div class="sidebar">
                    <a href="home.php">
                        <span class="material-icons-sharp">home</span>
                        <h3>Dashboard</h3>
                    </a>
                    <a href="profile.php">
                        <span class="material-icons-sharp">person_outline</span>
                        <h3>Profile</h3>
                    </a>
                    <a href="message.php">
                        <span class="material-icons-sharp">mail_outline</span>
                        <h3>Message</h3>
                    </a>
                    <a href="admin_message.php">
                        <span class="material-icons-sharp">mail_outline</span>
                        <h3>Inbox</h3>
                        <span class="message-count"><?php echo $count; ?></span>
                    </a>
                    <a href="request.php">
                            <span class="material-icons-sharp">engineering</span>
                            <h3>Request Maintenance</h3>
                    </a>
                    <a href="payments.php">
                        <span class="material-icons-sharp">receipt_long</span>
                        <h3>Payment</h3>
                    </a>
                    <a href="logout.php">
                        <span class="material-icons-sharp">logout</span>
                        <h3>Logout</h3>
                    </a>
                </div>
            </aside>
            <main>
    <h1 class="text-muted">Request<span class="warning"> Maintenance</span></h1>
    <div class="date">
                <h1>Date Today:</h1>
                <h2 id="currentDate"></h2>
    </div>
    <?php 
        $query = "SELECT tenant_tbl.tenant_id, 
            tenant_tbl.first_name, tenant_tbl.last_name, 
            room_tbl.room_id, 
            CONCAT(tenant_tbl.first_name, ' ', tenant_tbl.last_name) AS full_name, 
            CONCAT(room_tbl.room_floor, ' - ', room_tbl.room_number) AS room_info
            FROM tenant_tbl 
            LEFT JOIN room_tbl ON tenant_tbl.tenant_id = room_tbl.tenant_id
            WHERE tenant_tbl.email = ?"; 
        
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($row = $result->fetch_assoc()) {
    ?> 
<div class="panel">
    <form action="proc.php" method="POST">
        <label class="head">Tenant Name:</label>
        <input class="text" type="text" value="<?php echo htmlspecialchars($row['full_name']); ?>" disabled>
        
        <label class="head">Room Information:</label>
        <input class="text" type="text" value="<?php echo htmlspecialchars($row['room_info']); ?>" disabled>
        <input type="hidden" name="room_id" value="<?php echo $row['room_id']; ?>">

        <label class="head">Issue:</label>
        <input class="text" type="text" name="issue" autocomplete="off" placeholder="Enter issue title..." required>
        
        <label class="head">Description:</label>
        <textarea class="text1" type="text" name="description" placeholder="Describe the issue..." required rows="5" maxlength="40"></textarea>  

        <div class="submit">
            <input type="hidden" name="tenant_id" value="<?php echo $row['tenant_id']; ?>">
            <input type="submit" id="submit-maint" class="submit-maint" name="submit-maintenance" value="Submit Request">
        </div>
    </form>
</div>
    
    <?php 
        } else {
            echo "<p>No tenant found.</p>";
        } 
    ?>

<div class="log-table">
    <h2>Request History</h2>
    <table>
        <thead>
            <tr>
                <th>Issue</th>
                <th>Date Submitted</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $results_per_page = 6;
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $start_from = ($current_page - 1) * $results_per_page;

            $user_email = $_SESSION['email'];

            // Get tenant_id from session email
            $query_tenant_id = "SELECT tenant_id FROM tenant_tbl WHERE email = ?";
            $stmt = $con->prepare($query_tenant_id);
            $stmt->bind_param("s", $user_email);
            $stmt->execute();
            $result_tenant_id = $stmt->get_result();

            if ($result_tenant_id->num_rows > 0) {
                $row_tenant_id = $result_tenant_id->fetch_assoc();
                $user_id = $row_tenant_id['tenant_id'];

                // Fetch maintenance requests
                $query_maintenance = "SELECT maintenance_tbl.issue, maintenance_tbl.date_added, maintenance_tbl.description, status.status_type
                                      FROM maintenance_tbl
                                      JOIN status ON maintenance_tbl.status_id = status.status_id
                                      WHERE maintenance_tbl.tenant_id = ?
                                      ORDER BY maintenance_tbl.date_added DESC
                                      LIMIT ?, ?";
                $stmt = $con->prepare($query_maintenance);
                $stmt->bind_param("iii", $user_id, $start_from, $results_per_page);
                $stmt->execute();
                $result_maintenance = $stmt->get_result();

                if ($result_maintenance->num_rows > 0) {
                    while ($row = $result_maintenance->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['issue']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_added']); ?></td>
                            <td><?php echo nl2br(wordwrap(htmlspecialchars($row['description']), 15, "\n", true)); ?></td>
                            <td><?php echo htmlspecialchars($row['status_type']); ?></td>
                        </tr>
                    <?php }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center; padding:10px;'>No maintenance requests found</td></tr>";
                }
            } else {
                echo "<tr><td colspan='3' style='text-align:center; padding:10px;'>Error fetching tenant ID</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php
    // Pagination logic
    if (isset($user_id)) {
        $count_query = "SELECT COUNT(*) AS total FROM maintenance_tbl WHERE tenant_id = ?";
        $stmt = $con->prepare($count_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $count_result = $stmt->get_result();
        $count_row = $count_result->fetch_assoc();
        $total_records = $count_row["total"];
        $total_pages = ceil($total_records / $results_per_page);

        if ($total_records > $results_per_page) {
            echo "<div class='footer'><span>Page: </span>";
            if ($current_page > 1) {
                echo "<a href='?page=" . ($current_page - 1) . "'><< Back</a> ";
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $current_page) {
                    echo "<strong>$i</strong> ";
                } else {
                    echo "<a href='?page=$i'>$i</a> ";
                }
            }

            if ($current_page < $total_pages) {
                echo "<a href='?page=" . ($current_page + 1) . "'>Next >></a>";
            }

            echo "</div>";
        } else {
            echo "<div class='footer'>Page: 1</div>";
        }
    }
    ?>
</div>

</main>
        <div class="right">
            <div class="top">
                <button id="menu-btn">
                    <span class="material-icons-sharp">menu</span>
                </button>
                    <div class="theme-toggle">
                        <span class="material-icons-sharp active">light_mode</span>
                        <span class="material-icons-sharp">dark_mode</span>
                    </div>
                    <div class="user">
                    <?php
                    if (isset($_SESSION['email'])) {
                        $email = $_SESSION['email'];

                        $sql = "SELECT tenant_tbl.first_name, tenant_tbl.last_name, tenant_tbl.email, user_type.user_type_id, user_type.user_type
                                    FROM tenant_tbl
                                    JOIN user_type
                                    ON tenant_tbl.user_type_id = user_type.user_type_id
                                    WHERE tenant_tbl.email = ?";

                        if ($stmt = $con->prepare($sql)) {
                            $stmt->bind_param('s', $email);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $dis = $result->fetch_assoc();
                            ?>
                                <p>Hello, <b><?php echo htmlspecialchars($dis['first_name']); ?></b></p>
                                <small class="text-muted"><?php echo htmlspecialchars($dis['user_type']); ?></small>
                            <?php
                            } else {
                                echo "<p>User not found.</p>";
                            }
                            $stmt->close();
                        } else {
                            echo "<p>Error preparing the query.</p>";
                        }
                    } else {
                        echo "<p>User not logged in.</p>";
                    }
                    ?>
                </div>
            </div>
                <div class="payment-log">
                    <h2>Payment Log</h2>
                    <div class="updates"><?php
                    $rows = [];
                    while ($paylog_row = mysqli_fetch_assoc($result1)) {
                    $rows[] = $paylog_row; }
                    foreach ($rows as $paylog_row) { ?>
                        <div class="update">
                            <div class="icon">
                                <span class="material-icons-sharp">priority_high</span>
                            </div>
                            <div class="message">
                                <p><b><?php echo $paylog_row['status_type'] ?></b> amount of <b> <?php echo $paylog_row['amount'] ?></b> Pesos</p>
                                <small class="text-muted">
                                <?php 
                                $datePaid = $paylog_row['date_paid'];
                                $datePaidObj = new DateTime($datePaid);
                                $currentDateObj = new DateTime();
                                $interval = $datePaidObj->diff($currentDateObj);
                                if ($interval->y > 0) {
                                    echo $interval->y . " year" . ($interval->y > 1 ? "s" : "") . " ago";
                                } elseif ($interval->m > 0) {
                                    echo $interval->m . " month" . ($interval->m > 1 ? "s" : "") . " ago";
                                } elseif ($interval->d > 0) {
                                    echo $interval->d . " day" . ($interval->d > 1 ? "s" : "") . " ago";
                                } elseif ($interval->h > 0) {
                                    echo $interval->h . " hour" . ($interval->h > 1 ? "s" : "") . " ago";
                                } elseif ($interval->i > 0) {
                                    echo $interval->i . " minute" . ($interval->i > 1 ? "s" : "") . " ago";
                                } else {
                                    echo "Just now";}?>
                                </small>
                            </div>
                        </div>
                    <?php }           
                    if (empty($paylog_row)) {
                        echo "<p>No entries found.</p>"; } ?>    
                    </div>
                </div>      
            </div>

    <script src="page.js"></script>
    <script>
            // Current Date
        var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var currentDate = new Date();
        var day = currentDate.getDate();
        var monthIndex = currentDate.getMonth();
        var year = currentDate.getFullYear();
        var monthName = months[monthIndex];
        var formattedDate = monthName + " " + day + ", " + year;
        document.getElementById("currentDate").innerHTML = formattedDate;

    </script>
    </body>
</html>