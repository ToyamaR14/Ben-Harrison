<?php
require ('db.php');
include ('auth_session.php');
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['tenant_id']) || $_SESSION['user_type_id'] !== '1') {
    header("Location: homepage.php"); // Redirect to login page if unauthorized
    exit();
}

$query = "SELECT COUNT(*) AS count FROM contact_tbl WHERE status_id = '3'";
    $result = mysqli_query($con,$query);
    $row = mysqli_fetch_assoc($result);
    $count = $row['count'];

$query = "SELECT COUNT(*) AS pending FROM reserve_tbl WHERE status_id = '0'";
    $result = mysqli_query($con,$query);
    $row = mysqli_fetch_assoc($result);
    $pending = $row['pending'];

$query = "SELECT COUNT(*) AS pending_maint FROM maintenance_tbl WHERE status_id = '0'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $pending_maint = $row['pending_maint']; 

$query = "SELECT COUNT(*) AS pending_ten FROM tenant_mes_tbl WHERE status_id = '0'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $pending_ten = $row['pending_ten']; 

$email = $_SESSION['email']; 
    $query = "SELECT first_name, last_name FROM tenant_tbl WHERE email = '$email'";
    $nresult = mysqli_query($con,$query);
    $nrow = mysqli_fetch_array($nresult);

$results_per_page = 15;
$results_view     = 3;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($current_page - 1) * $results_per_page;

?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="utf-8"/>
    <title>Contact Logs</title>
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=2.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
                    <span class="count"><?php echo $pending; ?></span>
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
                    <span class="count"><?php echo $pending_maint; ?></span>
                </a>

                <a href="notification.php">
                    <span class="material-icons-sharp">announcement</span>
                    <h3>View Messages</h3>
                    <span class="count"><?php echo $count; ?></span>
                </a>

                <a href="inbox.php">
                    <span class="material-icons-sharp">announcement</span>
                    <h3>Tenant Messages</h3>
                    <span class="count"><?php echo $pending_ten; ?></span>
                </a>

                <a href="payment.php">
                    <span class="material-icons-sharp">receipt_long</span>
                    <h3>Payments</h3>
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
    <div class="log-table anim t1">
        <div class="logbtn">
            <a href="logs.php#Contact" class="buttonlogr">Return to Brief Contact Log</a>
        </div>
                <h1 class="text-muted">Contact<span class="warning"> Logs</span></h1>
                <form action="" method="GET">
                    <div class="searchbar">
                        <input type="textsearch" autocomplete="off" id="search" name="search" value="<?php if(isset($_GET['search'])){echo $_GET['search']; } ?>" class="form-control" placeholder="Search data" onclick="toggleClearButton()">
                        <button type="search-log" class="searchbtn">Search</button>
                        <button type="button" id="clear" class="clearbtn" onclick="clearSearch()"><ion-icon name="close"></ion-icon></button>
                    </div>
                </form>
                    <table id="table">
                        <thead>
                            <th>CONTACT LOG ID</th>
                            <th>CONTACT ID</th>
                            <th class="warning">ACTIONS</th>
                            <th>DATE ENTRY</th>
                        </tr>
                        </thead>
        <tbody>
    <?php   
    $filtervalues = isset($_GET['search']) ? $_GET['search'] : '';

    $query = "SELECT log_tbl_c.*, log.*
                FROM `log_tbl_c`
                JOIN `log`
                ON log_tbl_c.log_type_id = log.log_type_id";
    
    if (!empty($filtervalues)) {
        $query .= " WHERE CONCAT(log_tbl_c.con_id, log_tbl_c.contact_log_id, log_tbl_c.date_entry, log.log_type) LIKE '%$filtervalues%' ";
    }
    
    $query .= " ORDER BY log_tbl_c.contact_log_id DESC
                LIMIT $start_from, $results_per_page";
    
    $result = mysqli_query($con, $query);
    
    if ($result->num_rows > 0) {
        foreach ($result as $row) {
            echo "<tr>
                    <td> {$row['contact_log_id']}</td>
                    <td> {$row['con_id']}    </td>
                    <td> {$row['log_type']}     </td>
                    <td> {$row['date_entry']}   </td>
                    </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<tr><td colspan='9'>0 results</td></tr>";
    }

    $count_query = "SELECT COUNT(*) AS total 
                    FROM `log_tbl_c`
                    JOIN `log` ON log_tbl_c.log_type_id = log.log_type_id";
    if (!empty($filtervalues)) {
        $count_query .= " WHERE CONCAT(log_tbl_c.con_id, log_tbl_c.contact_log_id, log_tbl_c.date_entry, log.log_type) LIKE '%$filtervalues%'";
    }
    
    $count_result = $con->query($count_query);
    $count_row = $count_result->fetch_assoc();
    $total_records = $count_row["total"];
    $total_pages = ceil($total_records / $results_per_page);

    if ($total_records > $results_per_page) {
        $pagination_range = 3;

        echo "<footer>Page: ";

        if ($current_page > 1) {
            echo "<a href='?page=" . ($current_page - 1) . "&search=" . urlencode($filtervalues) . "#table'><< Back</a> ";
        }

        $start_page = max(1, $current_page - floor($pagination_range / 2));
        $end_page = min($total_pages, $start_page + $pagination_range - 1);

        if ($start_page > 1) {
            echo "<a href='?page=1&search=" . urlencode($filtervalues) . "#table'>1</a> ";
            if ($start_page > 2) {
                echo "... ";
            }
        }

        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $current_page) {
                echo "<strong>$i</strong> ";
            } else {
                echo "<a href='?page=$i&search=" . urlencode($filtervalues) . "#table'>$i</a> ";
            }
        }

        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                echo "... ";
            }
            echo "<a href='?page=$total_pages&search=" . urlencode($filtervalues) . "#table'>$total_pages</a> ";
        }

        if ($current_page < $total_pages) {
            echo "<a href='?page=" . ($current_page + 1) . "&search=" . urlencode($filtervalues) . "#table'>Next >></a>";
        }

        echo "</footer>";
    } else {
        echo "<footer>Page: 1</footer>";
    }

    mysqli_close($con);

        ?>
        </div>
    </main>            
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function toggleClearButton() {
            var searchInput = document.getElementById('search');
            var clearButton = document.getElementById('clear');    
            if (searchInput.value.trim() === '') {
                clearButton.style.display = 'none';
            } else {
                clearButton.style.display = 'inline-block';
            }
        }
        function clearSearch() {
            var searchInput = document.getElementById('search');
            searchInput.value = '';
            toggleClearButton();
        }
        var searchInput = document.getElementById('search');
        searchInput.addEventListener('input', function() {
            toggleClearButton();
        });
        var clearButton = document.getElementById('clear');
        clearButton.addEventListener('click', function(event) {
            event.preventDefault();
            clearSearch();
        });
        toggleClearButton();
    });
</script>
    </body>
    <script src="notifications.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</html>
