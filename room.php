<?php
require ('db.php');
include ('auth_session.php');

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
    <title>Units</title>
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

<main>
    <div class="multi-table">
        <h1 class="text-muted">Manage <span class="warning">Units</span></h1>
        <form action="" method="GET">
        <div>
            <input type="textsearch" autocomplete="off" id="search" name="search" value="<?php if(isset($_GET['search'])){echo $_GET['search']; } ?>" class="form-control" placeholder="Search data" onclick="toggleClearButton()">
            <button type="search" class="searchbtn">Search</button>
        </div>
            </form>
            <button type="add" class="modal-room" id="modal-room" data-target="myModal">Add Occupant</button>
        <table>
            <thead>
                <tr>
                    <th>UNIT ID</th>
                    <th>UNIT NUMBER</th>
                    <th>UNIT FLOOR</th>
                    <th>FIRST NAME</th>
                    <th>LAST NAME</th>
                    <th>EMAIL ADDRESS</th>
                    <th>DATE START</th>
                    <th>DATE END</th>
                    <th>REMAINING DAYS</th>
                    <th class="warning">UNIT STATUS</th>
                    <th class="success">EDIT / DELETE</th>
                </tr>
            </thead>
<tbody>
<?php  
   $results_per_page = 20;
   $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
   $start_from = ($current_page - 1) * $results_per_page;
   
   $filtervalues = isset($_GET['search']) ? $_GET['search'] : '';
   $query = "SELECT room_tbl.room_id, room_tbl.room_number, room_tbl.room_floor,
   room_tbl.date_in, room_tbl.date_out, tenant_tbl.tenant_id, 
   tenant_tbl.first_name, tenant_tbl.last_name, tenant_tbl.email,
   status.status_id, status.status_type
    FROM `room_tbl`
    LEFT JOIN tenant_tbl ON room_tbl.tenant_id = tenant_tbl.tenant_id
    LEFT JOIN status ON room_tbl.status_id = status.status_id"; // Changed INNER JOIN to LEFT JOIN

    if (!empty($filtervalues)) {
    $query .= " WHERE CONCAT(room_tbl.room_id, room_number, room_floor, status_type, 
                first_name, last_name, email, date_in, date_out) 
    LIKE '%$filtervalues%' "; // Fixed missing space before WHERE and added room_id
    }
    $query .= " ORDER BY room_tbl.room_id 
    LIMIT $start_from, $results_per_page"; // Fixed missing space before ORDER BY

    $result = mysqli_query($con, $query);
   
   if ($result->num_rows > 0) {
       foreach ($result as $row) {
           echo "<tr>
                   <td> {$row['room_id']}    </td>
                   <td> {$row['room_number']}</td>
                   <td> {$row['room_floor']} </td>
                   <td> {$row['first_name']} </td>
                   <td> {$row['last_name']}  </td>
                   <td> {$row['email']}      </td>
                   <td> {$row['date_in']}    </td>
                   <td> {$row['date_out']}    </td>
                   <td>";
           
                   $dateOut = $row['date_out'];
                   $dateOutObj = new DateTime($dateOut);
                   $currentDateObj = new DateTime();

                   if ($currentDateObj >= $dateOutObj) {
                       echo "EXPIRED";
                   } else {
                       $interval = $currentDateObj->diff($dateOutObj);
                       if ($interval->y > 0) {
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
                           echo "Just now";
                       }
                   }
           echo "</td>
                   <td>{$row['status_type']}</td>
                   <td class='tenant-btns'>
                           <input type='hidden' name='room_id' value='{$row['room_id']}'>
                        <button type='edit' class='edit' id='{$row['room_id']}'><ion-icon name='create'></ion-icon></button>
                        <form method='POST' action='proc.php'>
                        <input type='hidden' name='delete_room' value='{$row['room_id']}'>
                        <button type='submit' onclick=\"return confirm('Are you sure you want to delete?');\">
                            <ion-icon name='trash'></ion-icon>
                        </button>
                    </form>
                </td>
                </tr>";
       }
       echo "</tbody></table>";
   } else {
       echo "<tr><td colspan='9'>0 results</td></tr>";
   }
   
   $count_query = "SELECT COUNT(*) AS total FROM `room_tbl` ";
   if (!empty($filtervalues)) {
       $count_query .= "LEFT JOIN tenant_tbl ON room_tbl.tenant_id = tenant_tbl.tenant_id
                       INNER JOIN status ON room_tbl.status_id = status.status_id 
                       WHERE CONCAT(room_number, room_floor, status_type, first_name, last_name, email, date_in, date_out) LIKE '%$filtervalues%'";
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
   
       // Next button
       if ($current_page < $total_pages) {
           echo "<a href='?page=" . ($current_page + 1) . "&search=" . urlencode($filtervalues) . "'>Next >></a>";
       }
   
       echo "</footer>";
   } else {
       echo "<footer>Page: 1</footer>"; // Show only page 1 if there are 8 or fewer records
   }
   
   mysqli_close($con);
   ?>
   

<div class="modal" id="myModal">
        <div class="modal-content">
            <span id="closeModal" class="close"><ion-icon name="close">&times;</ion-icon></span>
            <h2 class="text-muted">Enter New<span class="warning"> Occupant</span></h2>
            <form class="room-modal" id="room_modal" action="proc.php" method="POST">

                <label for="room_number" style="top: 10px; position: relative;">Unit Number:</label>
                <input type="text" autocomplete="off" name="room_number" id="room_number" placeholder="Room Number.." required>

                <label for="room_floor" style="top: 10px; position: relative;">Unit Floor:</label>
                <input type="text" autocomplete="off" name="room_floor" id="room_floor" placeholder="Room Floor.."required>

                <label for="tenant" style="position: relative; top: 10px;">Tenant ID/Name</label>
                <select type="option" name="tenant_id" required>
                <option value="" disabled selected>Select ID/Name</option>
                <?php
                include 'db.php';
                $tenant_query = "SELECT tenant_id, CONCAT(tenant_id, ' - ', first_name) AS tenant_info FROM tenant_tbl";
                $result = mysqli_query($con, $tenant_query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='{$row['tenant_id']}'>{$row['tenant_info']}</option>";
                }?>
                </select>

                <label for="datePickerIn" style="top: 25px; position: relative;">DATE IN:</label>
                <input type="datetime-local" class="date" id="datePickerIn" name="date_in">

                <label for="datePickerOut" style="top: 25px; position: relative;">DATE OUT:</label>
                <input type="datetime-local" class="date" id="datePickerOut" name="date_out">

                <label style="top: 1px; position: relative; font-size: medium; font-style: italic;" id="dateCounter"></label>

                <input type="submit" name="room-modal-submit">
            </form>
        </div>
    </div>

<div class="modal" id="roomModal">
    <div class="modal-dialog">
    <div class="modal-content" value="<?php echo isset($row['room_id']) ? $row['room_id']:'';?>">

      <div class="modal-header">
        <h2 class="text-muted">Edit Unit<span class="warning"> Occupancy</span></h2>
        <span type="button" class="close" data-dismiss="modal"><ion-icon name="close">&times;</ion-icon></span>
      </div>

      <div class="modal-body">
        No Data...
      </div>
      
    </div>
  </div>
</div>

<script>
    //Date Settings
    const datePickerIn = document.getElementById('datePickerIn');
    const datePickerOut = document.getElementById('datePickerOut');
    const dateCounter = document.getElementById('dateCounter');
    const today = new Date();
    const todayISO = today.toISOString().split("T")[0] + "T00:00";
    datePickerIn.min = todayISO;
    datePickerOut.min = todayISO;
    function dateDiffInDays(a, b) {
        return Math.floor((b - a) / (1000 * 60 * 60 * 24));
    }
    function updateCounter() {
        const dateIn = new Date(datePickerIn.value);
        const dateOut = new Date(datePickerOut.value);
        if (dateIn <= dateOut) {
            const daysDiff = dateDiffInDays(dateIn, dateOut);
            dateCounter.textContent = `Duration: ${daysDiff} days`;
        } else {
            dateCounter.textContent = '';
        }
    }
        datePickerIn.addEventListener('input', updateCounter);
        datePickerOut.addEventListener('input', updateCounter);

        //Show Password
function showhide() {
    var x = document.getElementById("pass");
    if (x.type === "password") {
        x.type = "show";
    } else {
        x.type = "password";
    }
}

        // Edit  Modal
    $(document).ready(function(){
    $('.edit').click(function(){
    id_emp = $(this).attr('id')
    $.ajax({url: "select.php",
    method:'post',
    data:{ro_id:id_emp},
    success: function(result){
    $(".modal-body").html(result);}});
    $('#roomModal').modal("show");})})

        // Add Modal
    const modal = document.getElementById('myModal');
    const openBtn = document.getElementById('modal-room');
    const closeBtn = document.getElementById('closeModal');
    const closeBtne1 = document.getElementById('close');
            openBtn.addEventListener('click', () => {
    modal.style.display = 'block';});
            closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';});
            closeBtne1.addEventListener('click', () => {
    modal.style.display = 'none';});
    </script>
                </main>
             </div>
                <script src="notifications.js"></script>
                <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
                <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
        </body>
</html>