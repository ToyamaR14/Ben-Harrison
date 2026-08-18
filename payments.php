<?php
require('db.php');
include('auth_session.php');
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['tenant_id']) || $_SESSION['user_type_id'] !== '2') {
    header("Location: homepage.php"); // Redirect to login page if unauthorized
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
$result1 = mysqli_query($con, $query1);

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
    <meta charset="utf-8" />
    <title>Payments</title>
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
            <h1 class="text-muted">Pay<span class="warning">ment</span></h1>

            <div class="date">
                <h1>Date Today:</h1>
                <h2 id="currentDate"></h2>
            </div>

            <div class="qrrows">
                <button type="button" class="modal-btn" id="modal-btn" style="cursor: pointer;">Open Payment Options</button>
                    <div class="modal" id="myModal">
                    <div class="modal-content">
                    <span id="closeModal" class="close"><ion-icon name="close"></ion-icon></span>

                <div class="payment-options">
                    <button class="options" id="onlineOption">Online Payment</button>
                    <button class="options" id="qrOption">QR Code</button>
                </div>

                <div id="qrSection" style="display: none;">
                    <img class="qrcode" src="imeg/Gcash.png">
                    <div class="guide">
                        <h1><p>How to Scan Gcash QR Code</p></h1>
                        <h2><p><strong>A.</strong> Use Gcash app and at homescreen Press QR icon.</p></h2>
                        <h2><p><strong>A.1.</strong> If you are using Desktop/Laptop, scan the QR code thru the Gcash App on your smartphone.</p></h2>
                        <h2><p><strong>A.2.</strong> If you are using Smartphone, screenshot the QR code and upload QR on the Gcash app.</p></h2>
                        <h2><p><strong>B.</strong> Once you scanned the QR, Please submit request payment GCASH (QR) to notify the landlord.</p></h2>
                    </div>
                </div>

            <?php 
                $query = "SELECT first_name, last_name, contacts, email, tenant_id
                FROM tenant_tbl
                WHERE email = '$user_id'";
            
                $result = mysqli_query($con, $query);  
                while($row = mysqli_fetch_array($result))  
                {  
            ?> 
                <div id="onlineSection" style="display: none;">
                <form method="POST" id="paymentForm" action="process_payment.php" style="all: unset; display: block; width: 100%;">
                    <label class="head">Name</label>
                    <input type="text" id="name" value="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>" readonly>
                    <input type="hidden" name="tenant_id" value="<?php echo htmlspecialchars($row['tenant_id']); ?>">
                    <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($row['first_name']); ?>">
                    <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($row['last_name']); ?>">

                    <label class="head">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" value="<?php echo htmlspecialchars($row['contacts']); ?>" readonly>

                    <label class="head">Email Address</label>
                    <input type="text" name="email_address" id="email" value="<?php echo htmlspecialchars($row['email']); ?>" readonly> 

                    <label class="head">Enter Amount</label>
                    <input type="text" id="amount" name="amount" placeholder="Enter Amount" min="20" step="1" maxlength="6" autocomplete="off" required onkeypress="return isNumber(event)" oninput="validateAmount(this)">
                    <small id="amountWarning" style="position: relative;color: red;display: inline;bottom: 20px;left: 6px;">! Minimum amount is ₱ 200.00 !</small>

                    <div class="form-group">
                        <label class="head">Purpose</label>
                        <select name="purpose" id="purpose_type">
                            <option value="1"<?php echo (isset($row['purpose_id']) && $row['purpose_id'] == 1) ? ' selected' : ''; ?>>Rent Bill</option>
                            <option value="2"<?php echo (isset($row['purpose_id']) && $row['purpose_id'] == 2) ? ' selected' : ''; ?>>Electricity Bill</option>
                            <option value="3"<?php echo (isset($row['purpose_id']) && $row['purpose_id'] == 3) ? ' selected' : ''; ?>>Water Bill</option>
                            <option value="5"<?php echo (isset($row['purpose_id']) && $row['purpose_id'] == 5) ? ' selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="head">Payment Type</label>
                        <select name="payment_type" id="payment_type" onchange="updateFormAction()">
                            <option value="1"<?php echo (isset($row['payment_type_id']) && $row['payment_type_id'] == 1) ? ' selected' : ''; ?>>CASH IN</option>
                            <option value="2"<?php echo (isset($row['payment_type_id']) && $row['payment_type_id'] == 2) ? ' selected' : ''; ?>>PAYMONGO (GCASH)</option>
                            <option value="3"<?php echo (isset($row['payment_type_id']) && $row['payment_type_id'] == 3) ? ' selected' : ''; ?>>GCASH (QR)</option>

                        </select>
                    </div>

                    <!-- Hidden fields for payment_intent_id and status_type -->
                    <input type="hidden" name="payment_intent_id" id="payment_intent_id">
                    <input type="hidden" name="status_type" value="0"> <!-- Assuming 0 is for "PENDING" -->

                     <button type="submit" id="submitBtn" name="pay-submit" class="button-submit" style="cursor: pointer;" disabled>Submit</button>
                </form>
            </div>
                <?php } ?>
            </div>
        </div>
    </div>

        <div class="log-table">
        <h2>Payment History</h2>
        <table>
            <thead>
                <tr>
                    <th class="amount">Amount</th>
                    <th class="entry">Date Entry</th>
                    <th class="paid">Date Paid</th>
                    <th class="ref">Payment Reference</th>
                    <th class="type">Payment Type</th>
                    <th class="purp">Purpose</th>
                    <th class="status">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $results_per_page = 6;
                $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $start_from = ($current_page - 1) * $results_per_page;

                $query = "SELECT 
                            payment_tbl.amount, 
                            payment_tbl.date_entry, 
                            payment_tbl.date_paid, 
                            payment_tbl.payment_intent_id, 
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
                        LIMIT ?, ?";

                $stmt = $con->prepare($query);
                $stmt->bind_param("sii", $user_id, $start_from, $results_per_page);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($paylog_row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td class="amount"><?php echo $paylog_row['amount']; ?></td>
                            <td class="entry"><?php echo $paylog_row['date_entry']; ?></td>
                            <td class="paid">
                                <?php echo ($paylog_row['date_paid'] == NULL) ? 'Pending' : $paylog_row['date_paid']; ?>
                            </td>
                            <td class="ref" style="cursor: pointer; color: cornflowerblue;">
                                <span class="short-id underlined" onclick="toggleFullId(this)" 
                                    data-full-id="<?php echo htmlspecialchars($paylog_row['payment_intent_id']); ?>">
                                    <?php echo !empty($paylog_row['payment_intent_id']) ? substr($paylog_row['payment_intent_id'], 0, 8) . '..' : ''; ?>
                                </span>
                            </td>
                            <td class="type"><?php echo $paylog_row['payment_type']; ?></td>
                            <td class="purp"><?php echo $paylog_row['purpose_type']; ?></td>
                            <td class="status"><?php echo $paylog_row['status_type']; ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="7" class="no-data">No payment history found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php
        // Pagination logic
        $count_query = "SELECT COUNT(*) AS total FROM payment_tbl
                        JOIN status ON payment_tbl.status_id = status.status_id
                        JOIN tenant_tbl ON payment_tbl.tenant_id = tenant_tbl.tenant_id
                        JOIN payment ON payment_tbl.payment_type_id = payment.payment_type_id
                        JOIN purpose ON payment_tbl.purpose_id = purpose.purpose_id
                        WHERE tenant_tbl.email = ?";
        
        $stmt = $con->prepare($count_query);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $count_result = $stmt->get_result();
        $count_row = $count_result->fetch_assoc();
        $total_records = $count_row["total"];

        $total_pages = ceil($total_records / $results_per_page);

        if ($total_records > $results_per_page) {
            $pagination_range = 3;

            echo "<div class='footer'><span>Page: </span>";

            if ($current_page > 1) {
                echo "<a href='?page=" . ($current_page - 1) . "'><< Back</a> ";
            }

            $start_page = max(1, $current_page - floor($pagination_range / 2));
            $end_page = min($total_pages, $start_page + $pagination_range - 1);

            if ($start_page > 1) {
                echo "<a href='?page=1'>1</a> ";
                if ($start_page > 2) {
                    echo "... ";
                }
            }

            for ($i = $start_page; $i <= $end_page; $i++) {
                if ($i == $current_page) {
                    echo "<strong class='current-page'>$i</strong> ";
                } else {
                    echo "<a href='?page=$i'>$i</a> ";
                }
            }

            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo "... ";
                }
                echo "<a href='?page=$total_pages'>$total_pages</a> ";
            }

            if ($current_page < $total_pages) {
                echo "<a href='?page=" . ($current_page + 1) . "'>Next >></a>";
            }

            echo "</div>";
        } else {
            echo "<div class='footer'>Page: 1</div>";
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
                                            $rows[] = $paylog_row;
                                        }
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
                                                echo "Just now";
                                            } ?>
                                </small>
                            </div>
                        </div>
                    <?php }
                                        if (empty($paylog_row)) {
                                            echo "<p>No entries found.</p>";
                                        } ?>
                </div>
            </div>
        </div>
    </div>
    <script src="page.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        // Modal
        const modal = document.getElementById('myModal');
        const openBtn = document.getElementById('modal-btn');
        const closeBtn = document.getElementById('closeModal');

        openBtn.addEventListener('click', () => {
            modal.style.display = 'block';
            modal.classList.add('popout');

            // Always reset to Online Payment first
            document.getElementById("onlineSection").style.display = "block";
            document.getElementById("qrSection").style.display = "none";
        });

        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            modal.classList.remove('popout');
        });

        document.getElementById("qrOption").addEventListener("click", function () {
            document.getElementById("qrSection").style.display = "block";
            document.getElementById("onlineSection").style.display = "none";
        });

        document.getElementById("onlineOption").addEventListener("click", function () {
            document.getElementById("onlineSection").style.display = "block";
            document.getElementById("qrSection").style.display = "none";
        });

        // Current Date
        var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var currentDate = new Date();
        var day = currentDate.getDate();
        var monthIndex = currentDate.getMonth();
        var year = currentDate.getFullYear();
        var monthName = months[monthIndex];
        var formattedDate = monthName + " " + day + ", " + year;
        document.getElementById("currentDate").innerHTML = formattedDate;

        //Payment Return
        window.addEventListener("beforeunload", function (event) {
        navigator.sendBeacon("expire_session.php"); // Optional: Notify the server
        window.location.href = "payments.php"; // Redirect to payments.php
        });

        //Payment Redirection
        function updateFormAction() {
        let paymentType = document.getElementById("payment_type").value;
        let form = document.getElementById("paymentForm");
        let submitBtn = document.getElementById("submitBtn");

        if (paymentType === "2") { // PayMongo (GCash) selected
            form.action = "process_payment.php";
            submitBtn.disabled = false;
        } else if (paymentType === "1") { // Cash In selected
            form.action = "proc.php"; // No action for now
            submitBtn.disabled = true; // Disable submit button
        }
        }
        document.addEventListener("DOMContentLoaded", updateFormAction);

        function isNumber(evt) {
        var charCode = evt.which ? evt.which : evt.keyCode;
        // Allow only numbers (0-9)
        if (charCode < 48 || charCode > 57) {
            return false;
        }
        return true;
        }

        function validateAmount(input) {
        let amount = parseFloat(input.value);
        let warning = document.getElementById("amountWarning");
        let submitBtn = document.getElementById("submitBtn");

        if (isNaN(amount) || amount < 200) {
            warning.style.display = "inline";  // Show warning
            submitBtn.disabled = true;         // Disable submit button
        } else {
            warning.style.display = "none";    // Hide warning
            submitBtn.disabled = false;        // Enable submit button
        }
    }
    
    function toggleFullId(element) {
    let fullId = element.getAttribute("data-full-id");

    if (!fullId || fullId.trim() === "") {
        element.textContent = ""; // Leave it empty if no data
        return;
    }

    let shortId = fullId.substring(0, 8);

    // Toggle between short and full ID
    element.textContent = (element.textContent === shortId) ? fullId : shortId;
    }


    </script>
</body>

</html>