<?php
require ('db.php');
include ('auth_session.php');

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
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="utf-8"/>
    <title>Messages</title>
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
    <div class="multi-table">
        <h1 class="text-muted">Manage Contact<span class="warning"> Messages</span></h1>
            <form action="" method="GET">
                <div>
                    <input type="textsearch" autocomplete="off" id="search" name="search" value="<?php if(isset($_GET['search'])){echo $_GET['search']; } ?>" class="form-control" placeholder="Search data" onclick="toggleClearButton()">
                    <button type="search" class="searchbtn">Search</button>
                </div>
            </form>

            <table>
                <thead>
                <tr>
                    <th>FULL NAME</th>
                    <th>SUBJECT</th>
                    <th>CONTACT NUMBER</th>
                    <th>EMAIL ADDRESS</th>
                    <th>DATE INQUIRED</th>
                    <th class="warning">STATUS</th>
                    <th class="success">VIEW/DELETE   </th>
                </tr>
                </thead>
<tbody>
<?php  

    $results_per_page = 10; 
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start_from = ($current_page - 1) * $results_per_page;
    $filtervalues = isset($_GET['search']) ? $_GET['search'] : '';
    
    $query = "SELECT contact_tbl.con_id, contact_tbl.full_name, contact_tbl.con_email,
            contact_tbl.con_number, contact_tbl.con_sub, contact_tbl.con_mes,
            contact_tbl.con_datetime, status.status_id, status.status_type
            FROM `contact_tbl`
            LEFT JOIN status
            ON contact_tbl.status_id = status.status_id ";
    
    if (!empty($filtervalues)) {
        $query .= "WHERE CONCAT(full_name, con_sub, con_email, con_number, con_datetime, status_type) LIKE '%$filtervalues%' ";
    }
    
    $query .= "ORDER BY contact_tbl.con_id DESC
               LIMIT $start_from, $results_per_page";
    
    $result = mysqli_query($con, $query);
    
    if ($result->num_rows > 0) {
        foreach ($result as $row) {
            echo "<tr>
                    <td>    {$row['full_name']}   </td>
                    <td>    {$row['con_sub']}     </td>
                    <td>    {$row['con_number']}  </td>
                    <td>    {$row['con_email']}   </td>
                    <td>    {$row['con_datetime']}</td>
                    <td>    {$row['status_type']} </td>
                    <td class='contact-btns'>
                        <input type='hidden' name='con_id' value='{$row['con_id']}'>
                            <button type='read' name='read' class='read' id='{$row['con_id']}'><ion-icon name='reader'></ion-icon></button>
                            <form action='proc.php' method='POST'>
                                <input type='hidden' name='con_id' value='{$row['con_id']}'>
                                <button type='delete' name='delete-con' onclick='return confirm(\"Are you sure you want to delete?\");'><ion-icon name='trash'></ion-icon></button>
                        </form>
                    </td>
                 </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<tr><td colspan='9'>0 results</td></tr>";
    }
    
    $count_query = "SELECT COUNT(*) AS total FROM `contact_tbl` ";
    if (!empty($filtervalues)) {
        $count_query .= "LEFT JOIN status ON contact_tbl.status_id = status.status_id 
                         WHERE CONCAT(full_name, con_sub, con_email, con_number, con_datetime, status_type) LIKE '%$filtervalues%'";
    }
    $count_result = $con->query($count_query);
    $count_row = $count_result->fetch_assoc();
    $total_records = $count_row["total"];
    
    $total_pages = ceil($total_records / $results_per_page);
    
    if ($total_records > $results_per_page) {
        $pagination_range = 3;
    
        echo "<footer>Page: ";
 
        if ($current_page > 1) {
            echo "<a href='?page=" . ($current_page - 1) . "&search=" . urlencode($filtervalues) . "'><< Back</a> ";
        }
    
        $start_page = max(1, $current_page - floor($pagination_range / 2));
        $end_page = min($total_pages, $start_page + $pagination_range - 1);
    
        if ($start_page > 1) {
            echo "<a href='?page=1&search=" . urlencode($filtervalues) . "'>1</a> ";
            if ($start_page > 2) {
                echo "... ";
            }
        }
    
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $current_page) {
                echo "<strong>$i</strong> ";
            } else {
                echo "<a href='?page=$i&search=" . urlencode($filtervalues) . "'>$i</a> ";
            }
        }
    
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                echo "... ";
            }
            echo "<a href='?page=$total_pages&search=" . urlencode($filtervalues) . "'>$total_pages</a> ";
        }

        if ($current_page < $total_pages) {
            echo "<a href='?page=" . ($current_page + 1) . "&search=" . urlencode($filtervalues) . "'>Next >></a>";
        }
    
        echo "</footer>";
    } else {
        echo "<footer>Page: 1</footer>";
    }
    
    mysqli_close($con);
        ?>
</div>

<div class="modal-con" id="contactModal">
    <div class="modal-dialog">
    <div class="modal-content-con" value="<?php echo isset($row['con_id'])? $row['con_id']: ''; ?>">

      <div class="modal-header">
        <span type="button" class="close" data-dismiss="modal" onclick="refreshPage()"><ion-icon name="close">&times;</ion-icon></span>
      </div>

      <div class="modal-body">
        No Data...
      </div>
      
    </div>
  </div>
</div>

<script>
    // Read Contact Modal
    $(document).ready(function(){
    $('.read').click(function(){
    id_rec = $(this).attr('id')
    $.ajax({url: "select.php",
    method:'post',
    data:{contact_id:id_rec},
    success: function(result){
    $(".modal-body").html(result);}});
    $('#contactModal').modal("show");})})

    //Refresh Page
    function refreshPage() {
    location.reload();}
    window.onclick = function(event) {
    var modal = document.getElementById("contactModal");
    if (event.target == modal) {
        modal.style.display = "none"; // Close modal
        refreshPage(); // Refresh page when clicked outside the modal
    }
    }
   
    </script>
                </main>
             </div>
             <script src="notifications.js"></script>
             <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
             <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
        </body>
</html>