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
    <title>Maintenance</title>
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=2.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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

<!------------------ START RESERVE TABLE  -------------------------------->
<main>
            <div class="multi-table">
                <h1 class="text-muted">Manage <span class="warning">Maintenance</span></h1>
                <form action="" method="GET">
                <div>
                    <input type="textsearch" autocomplete="off" id="search" name="search" value="<?php if(isset($_GET['search'])){echo $_GET['search']; } ?>" class="form-control" placeholder="Search data" onclick="toggleClearButton()">
                    <button type="search" class="searchbtn">Search</button>
                    <button type="clearsearch" id="clear" class="clearbtn" onclick="clearSearch()"><ion-icon name="close"></ion-icon></button>
                </div>
                </form>
                <button type="add" class="modal-maint" id="modal-maint" data-target="myModal">Add Maintenance Request</button>
            <table>
                <thead>
                <tr>
                    <th>REQUEST ID</th>
                    <th>REQUESTED BY</th>
                    <th>UNIT(FLOOR & NO)</th>
                    <th>ISSUE</th>
                    <th>DATE ADDED</th>
                    <th>DATE COMPLETED</th>
                    <th class="success">STATUS</th>
                </tr>
                </thead>
<tbody>
<?php  
    $results_per_page = 10; 
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start_from = max(0, ($current_page - 1) * $results_per_page);
    $filtervalues = isset($_GET['search']) ? $_GET['search'] : '';

    $query = "SELECT maintenance_tbl.request_id, maintenance_tbl.issue, maintenance_tbl.description, 
                 maintenance_tbl.date_added, maintenance_tbl.date_completed,
                 maintenance_tbl.status_id, status.status_type, 
                 tenant_tbl.tenant_id, tenant_tbl.first_name, tenant_tbl.last_name,
                 room_tbl.room_id, room_tbl.room_number, room_tbl.room_floor
          FROM maintenance_tbl
          JOIN tenant_tbl ON maintenance_tbl.tenant_id = tenant_tbl.tenant_id
          JOIN status ON maintenance_tbl.status_id = status.status_id
          LEFT JOIN room_tbl ON maintenance_tbl.room_id = room_tbl.room_id";

    // Search filter
    if (!empty($filtervalues)) {
        $query .= " WHERE CONCAT(maintenance_tbl.request_id, maintenance_tbl.issue, maintenance_tbl.description, 
                                maintenance_tbl.date_added, maintenance_tbl.date_completed,
                                maintenance_tbl.status_id, status.status_type, 
                                tenant_tbl.tenant_id, tenant_tbl.first_name, tenant_tbl.last_name,
                                room_tbl.room_id, room_tbl.room_number, room_tbl.room_floor) 
                    LIKE '%$filtervalues%' ";
    }

    $query .= " ORDER BY maintenance_tbl.date_added DESC LIMIT $start_from, $results_per_page";

    $result = mysqli_query($con, $query);
    ?>
        <?php 
        if ($result->num_rows > 0) {
            foreach ($result as $row) {
                echo "<tr>
                        <td>  {$row['request_id']} </td>
                        <td>  {$row['first_name']} {$row['last_name']}  </td>
                        <td>  {$row['room_floor']}, {$row['room_number']}  </td>
                        <td>  {$row['issue']}  </td>
                        <td>  {$row['date_added']}  </td>
                        <td>  {$row['date_completed']}  </td>
                        <td>  {$row['status_type']}  </td>
                        <td>
                            <button type='editMaint' title='Edit Maintenance' class='editMaint' id='{$row['request_id']}'><ion-icon name='create'></ion-icon></button>
                                <form action='proc.php' method='POST'>
                                    <input type='hidden' name='delete-maint' value='{$row['request_id']}'>
                                    <button type='delete' name='delete-request' onclick='return confirm(\"Are you sure you want to delete?\");'>
                                        <ion-icon name='trash'></ion-icon>
                                    </button>
                                </form>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No records found</td></tr>";
        }
        ?>
    </tbody>
    </table>

    <?php
    // Pagination logic
    $count_query = "SELECT COUNT(*) AS total FROM maintenance_tbl 
                    LEFT JOIN status ON maintenance_tbl.status_id = status.status_id";

    if (!empty($filtervalues)) {
        $query .= " AND CONCAT(maintenance_tbl.request_id, maintenance_tbl.issue, maintenance_tbl.description, 
                                maintenance_tbl.date_added, maintenance_tbl.date_completed,
                                tenant_tbl.tenant_id, tenant_tbl.first_name, tenant_tbl.last_name, 
                                room_tbl.room_id, room_tbl.room_number, room_tbl.room_floor, 
                                status.status_id, status.status_type) 
                    LIKE '%$filtervalues%' ";
    }

    $count_result = $con->query($count_query);
    $count_row = $count_result->fetch_assoc();
    $total_records = $count_row["total"];
    $total_pages = ceil($total_records / $results_per_page);

    if ($total_records > $results_per_page) { // Show pagination if more than 8 records
        $pagination_range = 3; // Number of pages to show

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

<div class="modal" id="myModal">
    <div class="modal-content">
        <span id="closeModal" class="close"><ion-icon name="close">&times;</ion-icon></span>
        <h2 class="text-muted">Enter New <span class="warning">Maintenance Request</span></h2>
        <form class="maintenance-modal" id="maintenance-modal" action="proc.php" method="POST">

                <h2 for="tenant" style="position: relative; top: 10px;">Tenant ID/Name</h2>
                <select type="option" name="tenant_id" required>
                    <option value="" disabled selected>Select ID/Name</option>
                    <?php
                    include 'db.php';
                    $tenant_query = "SELECT tenant_id, CONCAT(tenant_id, ' - ', first_name) AS tenant_info FROM tenant_tbl";
                    $result = mysqli_query($con, $tenant_query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['tenant_id']}'>{$row['tenant_info']}</option>";
                    }
                    ?>
                </select>

                <h2 for="con_sub" style="margin-top: 10px;height: 2dvh;">Unit ID </h2>
                <input type="text" name="room_id" id="room_id" placeholder="Enter Unit ID">

                <h2 for="con_sub" style="margin-top: 10px;height: 2dvh;">Concern: </h2>
                <input type="text" name="issue" id="issue" placeholder="Enter Concern">

                <h2 for="con_sub" style="margin-top: 10px;height: 2dvh;">Description: </h2>
                <textarea type="textbox" name="description" id="description" rows="4" cols="50" style="top: 15px;"></textarea>

                <input type="submit" name="maintenance-sub" class="maintenance-sub" value="send"> 
                </form> 
        </form>
    </div>
</div>

<div class="modal" id="editModal">
    <div class="modal-dialog">
    <div class="modal-content" value="<?php echo isset($row['request_id']) ? $row['request_id'] : ''; ?>">

      <div class="modal-header">
        <h2 class="text-muted">Edit <span class="warning">Request</span></h2>
        <span type="button" class="close" data-dismiss="modal"><ion-icon name="close">&times;</ion-icon></span>
      </div>

      <div class="modal-body">
        No Data...
      </div>
      
    </div>
  </div>
</div>

<script>
        // Edit Maintance Modal
        $(document).ready(function(){
        $('.editMaint').click(function(){
        id_emp = $(this).attr('id')
        $.ajax({url: "select.php",
        method:'post',
        data:{maintedit_id:id_emp},
        success: function(result){
        $(".modal-body").html(result);}});
        $('#editModal').modal("show");})})

        // Add Item Modal
        const modal = document.getElementById('myModal');
        const openBtn = document.getElementById('modal-maint');
        const closeBtn = document.getElementById('closeModal');
        const closeBtne1 = document.getElementById('close');
        openBtn.addEventListener('click', () => {
        modal.style.display = 'block';});
        closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';});
        closeBtne1.addEventListener('click', () => {
        modal.style.display = 'none';});

          //Clear Search 
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
                </main>
             </div>
             <script src="notifications.js"></script>
             <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
             <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
        </body>
</html>