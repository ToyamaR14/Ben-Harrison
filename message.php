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
    <title>Message</title>
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
                </a>>
            </div>
        </aside>
        <main>
            <h1 class="text-muted">Mess<span class="warning">age</span></h1>

            <div class="date">
                <h1>Date Today:</h1>
                <h2 id="currentDate"></h2>
            </div>

                <div class="qrrows">
                    <button type="button" class="modal-btn" id="modal-btn" style="display: none;"></button>
                    <div class="modal" id="myModal">
                        <div class="modal-content">
                            <span class="close" id="closeModal"><ion-icon name="close"></ion-icon></span>
                            
                            <button type="button" id="close" name="modal-close" value="close"></button>
                        </div>
                    </div>
                </div>

                <div class="modal-con" id="messageModal">
                    <div class="modal-dialog">
                    <div class="modal-content-con" value="<?php echo isset($row['t_message_id'])? $row['t_message_id']: ''; ?>">
                        <div class="modal-header">
                            <span type="button" class="close" data-dismiss="modal"><ion-icon name="close">&times;</ion-icon></span>
                        </div>

                        <div class="modal-body">
                        No Data...
                        </div>
            
                        </div>
                    </div>  
                </div>

                <div style="display: flex;justify-content: center;">
                    <h2>Message Admin</h2>
                </div>

                <div class="panel">
                    <form action="proc.php" method="POST"> 
                        <label class="head">Subject:</label>
                        <input class="text" type="text" name="subject" autocomplete="off" placeholder="Subject...">

                        <label class="head">Description:</label>
                        <textarea class="text1" name="tenant_message" placeholder="Body..." required rows="5"></textarea>

                        <div class="submit">
                            <input type="hidden" name="tenant_id" value="<?php echo $tenant_id; ?>">
                            <input type="submit" id="submit-mess" class="submit-mess" name="submit-message" value="Submit Message ">
                        </div>
                    </form>
                </div>

                <div class="log-table">
                    <h2>Sent Messages</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>To</th>
                                <th>Date</th>
                                <th>Actions</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $results_per_page = 6;
                            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $start_from = ($current_page - 1) * $results_per_page;

                            $user_email = $_SESSION['email'];

                            // Fetch tenant_id using email
                            $query_tenant_id = "SELECT tenant_id FROM tenant_tbl WHERE email = '$user_email'";
                            $result_tenant_id = mysqli_query($con, $query_tenant_id);

                            if ($result_tenant_id && mysqli_num_rows($result_tenant_id) > 0) {
                                $row_tenant_id = mysqli_fetch_assoc($result_tenant_id);
                                $user_id = $row_tenant_id['tenant_id'];

                                // Fetch messages
                                $query_messages = "SELECT tenant_mes_tbl.*, status.*
                                                    FROM tenant_mes_tbl
                                                    JOIN status ON tenant_mes_tbl.status_id = status.status_id
                                                    WHERE tenant_mes_tbl.tenant_id = '$user_id' 
                                                    ORDER BY tenant_mes_tbl.date_sent DESC
                                                    LIMIT $start_from, $results_per_page";

                                $result_messages = mysqli_query($con, $query_messages);

                                if ($result_messages && mysqli_num_rows($result_messages) > 0) {
                                    while ($row = mysqli_fetch_assoc($result_messages)) { ?>
                                        <tr>    
                                            <td> <?php echo $row['sent_to']; ?> </td>
                                            <td> <?php echo $row['date_sent']; ?> </td>
                                            <td class='message-btn'>
                                                <button type='button' class='read' id='<?php echo $row['t_message_id']; ?>' 
                                                    onclick="openMessageModal(<?php echo $row['t_message_id']; ?>)">
                                                    <span class="material-icons-sharp">visibility</span>
                                                </button>
                                            </td>
                                            <td> <?php echo $row['status_type']; ?> </td>
                                        </tr>
                                    <?php }
                                } else {
                                    // Keep table structure when there are no messages
                                    echo "<tr><td colspan='4' style='text-align:center; padding:20px;'>No messages found</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center; padding:20px;'>Error fetching tenant ID</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <?php
                    $count_query = "SELECT COUNT(*) AS total FROM tenant_mes_tbl WHERE tenant_mes_tbl.tenant_id = '$user_id'";
                    $count_result = $con->query($count_query);
                    $count_row = $count_result->fetch_assoc();
                    $total_records = $count_row["total"];
                    $total_pages = ceil($total_records / $results_per_page);

                    if ($total_records > $results_per_page) {
                        $pagination_range = 3;

                        echo "<div class='footer'><span style='position: relative; top: 5px;'>Page: </span>";

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
                                echo "<strong style='margin-right: 10px; margin-left: 10px; margin-top: 6px;'>$i</strong> ";
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
        //Scroll Modal
        function openMessageModal(messageId) {
            var modal = document.getElementById("messageModal");

            // Show the modal (if it's hidden)
            modal.style.display = "block";

            // Scroll smoothly to the modal
            modal.scrollIntoView({ behavior: "smooth", block: "center" });

            // Optional: If you want to focus on the modal for accessibility
            modal.focus();
        }
        
        // Modal
        document.addEventListener("DOMContentLoaded", function () {
    // Close button for sidebar
    const closeSidebarBtn = document.getElementById("close-btn");
    const aside = document.querySelector("aside");

    if (closeSidebarBtn && aside) {
        closeSidebarBtn.addEventListener("click", function () {
            aside.style.display = "none";
        });
    }

    // Modal handling
    const modal = document.getElementById("myModal");
    const openBtn = document.getElementById("modal-btn");
    const closeBtn = document.getElementById("closeModal");
    const messageModal = document.getElementById("messageModal");

    if (openBtn && modal) {
        openBtn.addEventListener("click", function () {
            modal.style.display = "block";
        });
    }

    if (closeBtn && modal) {
        closeBtn.addEventListener("click", function () {
            modal.style.display = "none";
        });
    }

    // Close button for message modal
    const messageCloseBtn = messageModal.querySelector(".close");
    if (messageCloseBtn && messageModal) {
        messageCloseBtn.addEventListener("click", function () {
            messageModal.style.display = "none";
        });
    }

    // Close modal when clicking outside
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
        if (event.target === messageModal) {
            messageModal.style.display = "none";
        }
    });
});

        // Read
        $(document).ready(function(){
        $('.read').click(function(){
        id_mes = $(this).attr('id')
        $.ajax({url: "tenant_sel.php",
        method:'post',
        data:{t_message_id:id_mes},
        success: function(result){
        $(".modal-body").html(result);}});
        $('#messageModal').modal("show");})})

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