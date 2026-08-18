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
    <title>Reservations</title>
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=2.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <h1 class="text-muted">Manage<span class="warning"> Reservations</span></h1>
        <form action="" method="GET">
        <div>
            <input type="textsearch" id="search" autocomplete="off" name="search" value="<?php if(isset($_GET['search'])){echo $_GET['search']; } ?>" class="form-control" placeholder="Search data" onclick="toggleClearButton()">
            <button type="search" class="searchbtn">Search</button>
        </div>
             </form>
            <button type="reserve" class="modal-res" id="modal-res">Add Reservation</button>
            <table>
                <thead>
                <tr>
                    <th>RESERVE ID</th>
                    <th>FIRST NAME</th>
                    <th>LAST NAME</th>
                    <th>EMAIL ADDRESS</th>
                    <th>CONTACT NUMBER</th>
                    <th>DATE REQUESTED</th>
                    <th class="warning">STATUS</th>
                    <th class="success">ACCEPT/CANCEL</th>
                </tr>
                </thead>
<tbody>
<?php
  $results_per_page = 8; 
  $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
  $start_from = ($current_page - 1) * $results_per_page;
  $filtervalues = isset($_GET['search']) ? $_GET['search'] : '';
  
  $query = "SELECT reserve_tbl.reserve_id, reserve_tbl.res_fname, reserve_tbl.res_lname, 
      reserve_tbl.res_email, reserve_tbl.res_contact, reserve_tbl.date_requested,
      status.status_id, status.status_type 
      FROM `reserve_tbl`
      LEFT JOIN status
      ON reserve_tbl.status_id = status.status_id ";
  
  if (!empty($filtervalues)) {
      $query .= "WHERE CONCAT(res_fname, res_lname, res_email, date_requested, status_type) LIKE '%$filtervalues%' ";
  }
  
  $query .= "ORDER BY reserve_tbl.reserve_id DESC 
      LIMIT $start_from, $results_per_page";
  
  $result = mysqli_query($con, $query);
  
  if ($result->num_rows > 0) {
      foreach ($result as $row) {
          echo "<tr>
                  <td>  {$row['reserve_id']}    </td>
                  <td>  {$row['res_fname']}     </td>
                  <td>  {$row['res_lname']}     </td>
                  <td>  {$row['res_email']}     </td>
                  <td>  {$row['res_contact']}   </td>
                  <td>  {$row['date_requested']}</td>
                  <td>  {$row['status_type']}   </td>
                  <td>
                    <form action='proc.php' method='POST'>
                        <input type='hidden' name='reserve_id' value ='{$row['reserve_id']}'>
                
                        <button class='acceptbtn' type='submit' name='accept' value='Accept' 
                            onclick=\"return confirm('Are you sure you want to accept this reservation?');\">
                            <ion-icon name='checkmark-outline'></ion-icon>
                        </button>
                
                        <button class='cancelbtn' type='submit' name='cancel' value='Cancel' 
                            onclick=\"return confirm('Are you sure you want to cancel this reservation?');\">
                            <ion-icon name='close'></ion-icon>
                        </button>
                    </form>
                
                    <form action='proc.php' method='POST'>
                        <input type='hidden' name='delete_reserve' value ='{$row['reserve_id']}'>
                        <button type='submit' name='delete' onclick=\"return confirm('Are you sure you want to delete?');\">
                            <ion-icon name='trash'></ion-icon>
                        </button>
                    </form>
                </td>
               </tr>";
      }
      echo "</tbody></table></form>";
  } else {
      echo "<tr><td colspan='9'>0 results</td></tr>";
  }
  
  $count_query = "SELECT COUNT(*) AS total FROM `reserve_tbl` ";
  if (!empty($filtervalues)) {
      $count_query .= "LEFT JOIN status ON reserve_tbl.status_id = status.status_id 
                      WHERE CONCAT(res_fname, res_lname, res_email, date_requested, status_type) LIKE '%$filtervalues%'";
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
  
      // Next button
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
            <span id="closeModal" class="close"><ion-icon name="close"></ion-icon></span>
            <h2 class="text-muted">Enter New <span class="warning">Reservation</span></h2>
            <form class="reserve-modal" id="reserve_modal" action="proc.php" method="POST">
            <input type="text" name="resm_fname" autocomplete="off" placeholder="First Name" required>
            <input type="text" name="resm_lname" autocomplete="off" placeholder="Last Name" required>
			<input type="email" name="resm_email" autocomplete="off" placeholder="Email" required>
            <input type="tel" name="resm_contact" autocomplete="off" placeholder="Contact Number" pattern="[0]{1}[9]{1}[0-9]{9}" maxlength="11" required>
			<input type="submit" name="res-modal-submit" value="Submit">
            <button type="close" id="close" name="modal-close" value="close">Close</button>
            </form>
        </div>
    </div>
    
    <script>
        //Reserve Add Modal
        const modal = document.getElementById('myModal');
        const openBtn = document.getElementById('modal-res');
        const closeBtn = document.getElementById('closeModal');
        const closeBtn1 = document.getElementById('close');

        openBtn.addEventListener('click', () => {
        modal.style.display = 'block';
        });

        closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        });
        closeBtn1.addEventListener('click', () => {
        modal.style.display = 'none';
        });
        
    </script>
                </main>
             </div>
             <script src="notifications.js"></script>
             <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
             <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
        </body>
</html>