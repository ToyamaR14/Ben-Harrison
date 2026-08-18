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
    <title>Accounts</title>
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

<!------------------ START TABLE  -------------------------------->
    <main>
            <div class="multi-table">
                <h1 class="text-muted">Manage <span class="warning">Tenants</span></h1>
                <form action="" method="GET">
                <div>
                    <input type="textsearch" autocomplete="off" id="search" name="search" value="<?php if(isset($_GET['search'])){echo $_GET['search']; } ?>" class="form-control" placeholder="Search data" onclick="toggleClearButton()">
                    <button type="search" class="searchbtn">Search</button>
                </div>
                </form>
                <button type="add" class="modal-tenant" id="modal-tenant" data-target="myModal">Add Tenant</button>
                <button type="add" class="message-tenant" id="message-tenant">Message All Tenant</button>
                
            <table>
                <thead>
                <tr>
                    <th>TENANT ID</th>
                    <th>TYPE</th>
                    <th>FIRST NAME</th>
                    <th>LAST NAME</th>
                    <th>CONTACT NUMBER</th>
                    <th>EMAIL ADDRESS</th>
                    <th>DATE JOINED</th>
                    <th class="warning">STATUS</th>
                    <th class="success">MESSAGE/EDIT/RESET/DELETE</th>
                </tr>
                </thead>
<tbody>
<?php  

  $results_per_page = 8; 
  $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
  $start_from = ($current_page - 1) * $results_per_page;
  $filtervalues = isset($_GET['search']) ? $_GET['search'] : '';
  
  $query = "SELECT tenant_tbl.tenant_id , tenant_tbl.first_name , tenant_tbl.last_name ,
                  tenant_tbl.contacts , tenant_tbl.email , tenant_tbl.password ,
                  status.status_id , status.status_type , user_type.user_type ,
                  user_type.user_type_id , tenant_tbl.joined_date
                  FROM `tenant_tbl`
                  LEFT JOIN status
                  ON tenant_tbl.status_id = status.status_id
                  INNER JOIN user_type
                  ON tenant_tbl.user_type_id = user_type.user_type_id ";
  
  if (!empty($filtervalues)) {
      $query .= "WHERE CONCAT(first_name, last_name, email, contacts, joined_date, tenant_id, user_type, status_type) LIKE '%$filtervalues%' ";
  }
  
  $query .= "ORDER BY tenant_tbl.tenant_id DESC
                  LIMIT $start_from, $results_per_page";
  
  $result = mysqli_query($con, $query);
  
  if ($result->num_rows > 0) {
      foreach ($result as $row) {
          echo "<tr>
                  <td>  {$row['tenant_id']}  </td>
                  <td>  {$row['user_type']}  </td>
                  <td>  {$row['first_name']} </td>
                  <td>  {$row['last_name']}  </td>
                  <td>  {$row['contacts']}   </td>
                  <td>  {$row['email']}      </td>
                  <td>  {$row['joined_date']}</td>
                  <td>  {$row['status_type']}</td>
                  <td class='tenant-btns'>
                      <input type='hidden' name='tenant_id' value='{$row['tenant_id']}'>
                        <button type='button' class='send' 
                            data-tenant-id='" . $row['tenant_id'] . "' 
                            data-tenant-name='" . $row['first_name'] . " " . $row['last_name'] . "'>
                            <ion-icon name='send-outline'></ion-icon>
                        </button>
                        <button type='edit' class='edit' id='{$row['tenant_id']}'><ion-icon name='create'></ion-icon></button>
                        <button type='reset' class='reset' id='{$row['tenant_id']}'><ion-icon name='refresh-outline'></ion-icon></button>
                      <form action='proc.php' method='POST'>
                          <input type='hidden' name='tenant-del' value='{$row['tenant_id']}'>
                          <button type='delete' name='delete-tenant' onclick='return confirm(\"Are you sure you want to delete?\");'><ion-icon name='trash'></ion-icon></button>
                      </form>
                  </td>
               </tr>";
      }
      echo "</tbody></table>";
  } else {
      echo "<tr><td colspan='9'>0 results</td></tr>";
  }
  
  $count_query = "SELECT COUNT(*) AS total FROM `tenant_tbl` ";
  if (!empty($filtervalues)) {
      $count_query .= "LEFT JOIN status ON tenant_tbl.status_id = status.status_id
                      INNER JOIN user_type ON tenant_tbl.user_type_id = user_type.user_type_id 
                      WHERE CONCAT(first_name, last_name, email, contacts, joined_date, tenant_id, user_type, status_type) LIKE '%$filtervalues%'";
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
            <h2 class="text-muted">Enter New <span class="warning">Tenant</span></h2>
            <form class="manage-modal" id="manage_modal" action="proc.php" method="POST">
            <input type="text" name="tenant_type" placeholder="Tenant" disabled>
            <input type="text" name="first_name" autocomplete="off" placeholder="First Name" required>
            <input type="text" name="last_name" autocomplete="off" placeholder="Last Name" required>
			<input type="email" name="email" autocomplete="off" placeholder="Email" required>
            <input type="tel" id="phone" name="contacts"autocomplete="off" placeholder="Contact Number" pattern="[0]{1}[9]{1}[0-9]{9}" maxlength="11" required>
            <input type="password" name="password" id="pass" placeholder="Password" required>
            <div class="checkb">
                <label>Show Password</label>
                <input type="checkbox" name="show" onclick="showhide()">
            </div>
            <input type="password" name="cpassword" id="cpass" placeholder="Confirm Password" >
            <div class="checkb">
                <label>Show Password</label>
                <input type="checkbox" name="show" onclick="showhideC()">
            </div>
			<input type="submit" name="tenant-modal-submit" value="Add">
            <button type="close" id="close" name="modal-close" value="close">Close</button>
            </form>
        </div>
    </div>

<div class="modal" id="editModal">
    <div class="modal-dialog">
    <div class="modal-content" value="<?php echo isset($row['tenant_id']) ? $row['tenant_id'] : ''; ?>">

      <div class="modal-header">
        <h2 class="text-muted">Edit <span class="warning">Tenant</span></h2>
        <span type="button" class="close" data-dismiss="modal"><ion-icon name="close">&times;</ion-icon></span>
      </div>

      <div class="modal-body">
        No Data...
      </div>
      
    </div>
  </div>
</div>

<div class="modal" id="editPModal">
    <div class="modal-dialog">
    <div class="modal-content" value="<?php echo isset($row['tenant_id']) ? $row['tenant_id'] : ''; ?>">

      <div class="modal-header">
        <h2 class="text-muted">Reset <span class="warning">Password</span></h2>
        <span type="button" class="close" data-dismiss="modal"><ion-icon name="close">&times;</ion-icon></span>
      </div>

      <div class="modal-body">
        No Data...
      </div>
      
    </div>
  </div>
</div>

<div class="modal" id="MessageModal">
    <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h2 class="text-muted">Message <span class="warning">Tenants</span></h2>
        <span type="button" class="close" data-dismiss="modal"><ion-icon name="close">&times;</ion-icon></span>
      </div>

      <div class="modal-body">
        No Data...
      </div>
      
    </div>
  </div>
</div>

<div class="modal" id="SendModal">
    <div class="modal-dialog">
    <div class="modal-content" value="<?php echo isset($row['tenant_id']) ? $row['tenant_id'] : ''; ?>">

      <div class="modal-header">
        <h2 class="text-muted">Message <span class="warning">Tenant</span></h2>
        <span type="button" class="close" data-dismiss="modal"><ion-icon name="close">&times;</ion-icon></span>
      </div>

      <div class="modal-body">
        No Data...
      </div>
      
    </div>
  </div>
</div>
    </main>
</div>
<script>
        //Show Password
        function showhide() {
        var x = document.getElementById("pass");
        if (x.type === "password") {
            x.type = "show";
        } else {
            x.type = "password";}}
        function showhideC() {
        var x = document.getElementById("cpass");
        if (x.type === "password") {
            x.type = "show";
        } else {
            x.type = "password";}}

        // Edit Tenant Modal
        $(document).ready(function(){
        $('.edit').click(function(){
        id_emp = $(this).attr('id')
        $.ajax({url: "select.php",
        method:'post',
        data:{cus_id:id_emp},
        success: function(result){
        $(".modal-body").html(result);}});
        $('#editModal').modal("show");})})

        // Reset Password Modal
        $(document).ready(function(){
        $('.reset').click(function(){
        id_emp = $(this).attr('id')
        $.ajax({url: "select.php",
        method:'post',
        data:{resp_id:id_emp},
        success: function(result){
        $(".modal-body").html(result);}});
        $('#editPModal').modal("show");})})

        // Message All Tenant Modal
        $(document).ready(function() {
        $('.message-tenant').click(function() {
            var formHTML = `
                <form action="proc.php" method="POST">
                    <h2 for="con_sub" style="top: 10px; position: relative; height: 5dvh;">From: </h2>
                    <input type="see" name="message_all" id="message_all" placeholder="Admin" disabled>

                    <h2 for="con_sub" style="top: 10px; position: relative; height: 5dvh;">To: </h2>
                    <input type="see" name="message_all" id="message_all" placeholder="All Tenants" disabled>

                    <h2 for="con_sub" style="top: 10px; position: relative; height: 5dvh;">Subject: </h2>
                    <input type="see" name="subject" autocomplete="off" id="subject">

                    <textarea type="textbox" name="message" id="message" rows="4" cols="50"></textarea>

                    <input type="submit" name="message-tenant-admin" class="message-tenant-admin" value="send"> 
                </form>
            `;

            $(".modal-body").html(formHTML);
            $('#MessageModal').modal("show");
        });
    });

    //Message One Tenant
        $(document).ready(function() {
        $('.send').click(function() {
            var tenantId = $(this).data('tenant-id'); 
            var tenantName = $(this).data('tenant-name'); // Get full name from button
    
            // Instead of appending new form, replace the content
            var formHTML = `
                <form action="proc.php" method="POST">
                    <h2 style="top: 20px;position: relative;height: 5dvh;">From:</h2>
                    <input type="text" name="message_all" id="message_all" value="Admin" disabled>
    
                    <h2 style="top: 20px;position: relative;height: 5dvh;">To:</h2>
                    <input type="text" name="message_to" id="message_to" value="${tenantName}" readonly>
    
                    <h2 style="top: 20px;position: relative;height: 5dvh;">Subject:</h2>
                    <input type="text" name="subject" autocomplete="off" id="subject">
                    
                    <h2 style="top: 10px;position: relative;height: 2dvh;">Message:</h2>
                    <textarea type="textbox" name="message" id="message" rows="4" cols="50"></textarea>
    
                    <input type="hidden" name="tenant_id" value="${tenantId}">
                    <input type="submit" name="message-tenant" class="message-tenant" value="Send"> 
                </form>
            `;
    
            // Instead of appending, replace existing content
            $(".modal-body").html(formHTML);
            $('#SendModal').modal("show");
        });
    });

        // Add Tenant Modal
        const modal = document.getElementById('myModal');
        const openBtn = document.getElementById('modal-tenant');
        const closeBtn = document.getElementById('closeModal');
        const closeBtne1 = document.getElementById('close');
        openBtn.addEventListener('click', () => {
        modal.style.display = 'block';});
        closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';});
        closeBtne1.addEventListener('click', () => {
        modal.style.display = 'none';});
        </script>
        <script src="notifications.js"></script>
        <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
        </body>
</html>