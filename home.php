<?php 
require ('db.php');
include ('auth_session.php');
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['tenant_id']) || !isset($_SESSION['user_type_id'])) {
    header("Location: login.php");
    exit();
}
// Check if user is an tenant
if ($_SESSION['user_type_id'] != 2) {
    header("Location: index.php"); // Redirect to normal user page
    exit();
}

$user_id = $_SESSION['email'];
$stmt = $con->prepare("SELECT tenant_tbl.*, status.*, payment_tbl.*, room_tbl.*
                       FROM payment_tbl
                       JOIN status ON payment_tbl.status_id = status.status_id
                       JOIN tenant_tbl ON payment_tbl.tenant_id = tenant_tbl.tenant_id
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

$user_query = "SELECT tenant_id FROM tenant_tbl WHERE email = '$user_id'";
$user_result = mysqli_query($con, $user_query);

if ($user_result) {
    $user_row = mysqli_fetch_assoc($user_result);
    $tenant_id = $user_row['tenant_id'];

    $message_query = "SELECT COUNT(*) AS message_count FROM message_tbl WHERE status_id = '3' AND tenant_id = '$tenant_id'";
    $message_result = mysqli_query($con, $message_query);

    if ($message_result) {
        $message_row = mysqli_fetch_assoc($message_result);
        $message_count = $message_row['message_count'];
    } else {
        echo "Error fetching message count: " . mysqli_error($con);
    }
} else {
    echo "Error fetching tenant ID: " . mysqli_error($con);
}
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
    <title>Home</title>
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
                <h1 class="text-muted">Dash<span class="warning">board</span></h1>

                <div class="date">
                    <h1>Date Today:</h1>
                    <h2 id="currentDate"></h2>
                 </div>

                 <div class="insights">
                    <div class="timer">
                        <span class="material-icons-sharp">alarm</span>
                        <div class="middle">
                            <div class="left">
                                <h3>Due Date:</h3>
                            </div>
                            <div class="progress">
                                <h1><?php 
                                   if (!isset($_SESSION['tenant_id'])) {
                                    echo "User not logged in.";
                                    exit;
                                }
                                
                                $userId = $_SESSION['tenant_id'];
                                
                                $query = "SELECT * FROM room_tbl WHERE tenant_id = ? AND status_id = 11 ORDER BY room_id ASC";
                                $stmt = $con->prepare($query);
                                $stmt->bind_param("i", $userId);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        if (isset($row['date_out'])) {
                                            $dateOut = $row['date_out'];
                                            $dateOutObj = new DateTime($dateOut);
                                            $currentDateObj = new DateTime();
                                            $interval = $currentDateObj->diff($dateOutObj);
                                
                                            if ($interval->invert) {
                                                echo "Expired";
                                                echo "<script type='text/javascript'>alert('Your rent is past the due date. Please make the payment within the next two weeks.');</script>";
                                            } elseif ($interval->y > 0) {
                                                echo $interval->y . " year" . ($interval->y > 1 ? "s" : "") . " left";
                                            } elseif ($interval->m > 0) {
                                                echo $interval->m . " month" . ($interval->m > 1 ? "s" : "") . " left";
                                            } elseif ($interval->d > 0) {
                                                echo $interval->d . " day" . ($interval->d > 1 ? "s" : "") . " left";
                                            } elseif ($interval->h > 0) {
                                                echo $interval->h . " hour" . ($interval->h > 1 ? "s" : "") . " left";
                                            } elseif ($interval->i > 0) {
                                                echo $interval->i . " minute" . ($interval->i > 1 ? "s" : "") . " left";
                                            } else {
                                                echo "Just Now";
                                            }
                                
                                            $formattedDateOut = $dateOutObj->format('Y-m-d');
                                            echo '<div class="smol">';
                                            echo '<p class="text-muted">' . htmlspecialchars($formattedDateOut) . '</p>';
                                            echo '</div>';
                                        } else {
                                            echo "Pending..";
                                        }
                                    }
                                } else {
                                    echo "Inactive.";
                                }
                                   ?>
                            </h1>
                            </div>
                        </div>
                    </div>

                    <div class="unread">
                        <span class="material-icons-sharp">mail_outline</span>
                        <div class="middle">
                            <div class="left">
                                <h3>Unread Messages:</h3>
                            </div>
                            <div class="progress">
                                <h1><?php echo $message_count; ?></h1>
                            </div>
                        </div>
                    </div>

                    <div class="time">
                        <span class="material-icons-sharp">schedule</span>
                        <div class="middle">
                            <div class="left">
                                <h3>Current Time:</h3>
                            </div>
                            <div class="progress">
                                <h1 id="time"></h1>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="log-table">
                <h2>Recent Payments</h2>
                <table>
                    <thead>
                        <tr>
                            <th class="pid">Payment ID</th>
                            <th>Amount</th>
                            <th>Payment Type</th>
                            <th>Purpose</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $query = "SELECT 
                                    payment_tbl.payment_id, 
                                    payment_tbl.amount, 
                                    payment.payment_type, 
                                    purpose.purpose_type, 
                                    status.status_type
                                FROM payment_tbl
                                JOIN status ON payment_tbl.status_id = status.status_id
                                JOIN tenant_tbl ON payment_tbl.tenant_id = tenant_tbl.tenant_id
                                JOIN payment ON payment_tbl.payment_type_id = payment.payment_type_id
                                JOIN purpose ON payment_tbl.purpose_id = purpose.purpose_id
                                WHERE tenant_tbl.email = ?
                                ORDER BY payment_tbl.payment_id DESC
                                LIMIT 6";

                        $stmt = $con->prepare($query);
                        $stmt->bind_param("s", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($paylog_row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td class="pid"><?php echo $paylog_row['payment_id']; ?></td>
                                    <td><?php echo $paylog_row['amount']; ?></td>
                                    <td><?php echo $paylog_row['payment_type']; ?></td>
                                    <td><?php echo $paylog_row['purpose_type']; ?></td>
                                    <td><?php echo $paylog_row['status_type']; ?></td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="5" class="no-data">No recent payments</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <div class="log-table">
                <h2>Recent Messages</h2>
                <table>
                    <thead>
                        <tr>
                            <th class="sender">From</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $query = "SELECT 
                                    message_tbl.subject,
                                    message_tbl.send_by, 
                                    message_tbl.date_sent, 
                                    status.status_type
                                FROM message_tbl
                                JOIN status ON message_tbl.status_id = status.status_id
                                JOIN tenant_tbl ON message_tbl.tenant_id = tenant_tbl.tenant_id
                                WHERE tenant_tbl.email = ?
                                ORDER BY message_tbl.date_sent DESC
                                LIMIT 6";

                        $stmt = $con->prepare($query);
                        $stmt->bind_param("s", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($msg_row = $result->fetch_assoc()) { 
                                // Format date to YYYY/MM/DD
                                $formatted_date = date("Y/m/d", strtotime($msg_row['date_sent']));
                                ?>
                                <tr>
                                    <td class="sender"><?php echo $msg_row['send_by']; ?></td>
                                    <td><?php echo $msg_row['subject']; ?></td>
                                    <td><?php echo $formatted_date; ?></td>
                                    <td><?php echo $msg_row['status_type']; ?></td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="3" class="no-data">No recent messages</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
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
                            echo "<p>User not logged in.</p>"; } 
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
        </div>
        <script src="page.js"></script>
    </body>
</html>