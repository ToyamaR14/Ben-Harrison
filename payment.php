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
    <title>Payments</title>
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
                <h1 class="text-muted">Manage <span class="warning">Payments</span></h1>
                <form action="" method="GET">
                <div>
                    <input type="textsearch" autocomplete="off" id="search" name="search" value="<?php if(isset($_GET['search'])){echo $_GET['search']; } ?>" class="form-control" placeholder="Search data" onclick="toggleClearButton()">
                    <button type="search" class="searchbtn">Search</button>
                </div>
                </form>
                <button type="add" class="modal-pay" id="modal-pay" data-target="myModal">Add Payment</button>
            <table>
                <thead>
                <tr>
                    <th>Tenant ID</th>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Purpose</th>
                    <th>Amount Paid</th>
                    <th>Payment Reference</th>
                    <th>Date Entry</th>
                    <th>Date Paid</th>
                    <th>Payment Type</th>
                    <th class="warning">Payment Status</th>
                    <th class="success">Actions</th>
                </tr>
                </thead>
<tbody>
<?php  
    $results_per_page = 10;
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1; 
    $start_from = ($current_page - 1) * $results_per_page; 
    $filtervalues = isset($_GET['search']) ? $_GET['search'] : '';
    
    $query = "SELECT payment_tbl.payment_id, payment_tbl.contact_number, payment_tbl.email_address, 
                payment_tbl.amount,payment_tbl.payment_intent_id, payment_tbl.date_entry, payment_tbl.date_paid,
                tenant_tbl.tenant_id, tenant_tbl.first_name, tenant_tbl.last_name, tenant_tbl.email, 
                tenant_tbl.contacts, status.status_id, status.status_type, payment.payment_type_id,
                payment.payment_type, purpose.purpose_id, purpose.purpose_type
                FROM `payment_tbl`
                LEFT JOIN tenant_tbl
                ON payment_tbl.tenant_id = tenant_tbl.tenant_id
                INNER JOIN status
                ON payment_tbl.status_id = status.status_id
                INNER JOIN payment
                ON payment_tbl.payment_type_id = payment.payment_type_id
                INNER JOIN purpose
                ON payment_tbl.purpose_id = purpose.purpose_id";
    
    if (!empty($filtervalues)) {
        $query .= " WHERE CONCAT(payment_id, first_name, last_name, contact_number, email_address, purpose_type, amount, payment_intent_id, date_entry, date_paid, payment_type, status_type) LIKE '%$filtervalues%' ";
    }
    
    $query .= " ORDER BY payment_tbl.payment_id DESC
                LIMIT $start_from, $results_per_page";
    
    $result = mysqli_query($con, $query);
    
    if ($result->num_rows > 0) {
        foreach ($result as $row) {
            echo "<tr>
                    <td>    {$row['tenant_id']}          </td>
                    <td>    {$row['first_name']} {$row['last_name']}  </td>
                    <td>    {$row['email_address']}              </td>
                    <td>    {$row['purpose_type']}       </td>
                    <td>    {$row['amount']}             </td>
                    <td>    {$row['payment_intent_id']}  </td>
                    <td>    {$row['date_entry']}         </td>
                    <td>    {$row['date_paid']}          </td>
                    <td>    {$row['payment_type']}       </td>
                    <td>    {$row['status_type']}        </td>
                    <td class='tenant-btns'>
                        <input type='hidden' name='payment_id' value='{$row['payment_id']}'>
                            <button type='view' class='view' id='{$row['payment_id']}'><ion-icon name='reader'></ion-icon></button>
                            <button type='edit' class='edit' id='{$row['payment_id']}'><ion-icon name='create'></ion-icon></button>
                            </button>
                    </td>
                 </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<tr><td colspan='9'>0 results</td></tr>";
    }
    
    $count_query = "SELECT COUNT(*) AS total FROM `payment_tbl` ";
    if (!empty($filtervalues)) {
        $count_query .= "LEFT JOIN tenant_tbl ON payment_tbl.tenant_id = tenant_tbl.tenant_id
                         LEFT JOIN status ON payment_tbl.status_id = status.status_id
                         LEFT JOIN payment ON payment_tbl.payment_type_id = payment.payment_type_id
                         WHERE CONCAT(payment_id, contact_number, email_address, amount, payment_intent_id, date_entry, date_paid, payment_type, status_type) LIKE '%$filtervalues%'";
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

<div class="modal" id="myModal">
    <div class="modal-content">
        <span id="closeModal" class="close"><ion-icon name="close">&times;</ion-icon></span>
        <h2 class="text-muted">Payment <span class="warning">Entry</span></h2>

        <form class="payment-modal" id="payment_modal" action="proc.php" method="POST">

        <label for="tenant" style="position: relative; top: 10px;">Tenant ID/Name</label>
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

            <label for="contact_number" style="top: 10px; position: relative;">Enter Contact Number</label>
            <input type="tel" autocomplete="off" id="contact_number" name="contact_number" pattern="[0]{1}[9]{1}[0-9]{9}" maxlength="11" placeholder="Enter Contact Number:">

            <label for="email_address" style="top: 10px; position: relative;">Enter Email Address:</label>
            <input type="email" autocomplete="off" name="email_address" id="email_address" placeholder="Enter Email Address" required>

            <label for="purpose" style="top: 10px; position: relative;">Purpose</label>
            <select type="option" name="purpose" id="purpose">
                <option value="1"<?php echo (isset($row['purpose_id']) && $row['purpose_id'] == 1) ? ' selected' : ''; ?>>Rent Bill</option>
                <option value="2"<?php echo (isset($row['purpose_id']) && $row['purpose_id'] == 2) ? ' selected' : ''; ?>>Electricity Bill</option>
                <option value="3"<?php echo (isset($row['purpose_id']) && $row['purpose_id'] == 3) ? ' selected' : ''; ?>>Water Bill</option>
                <option value="4"<?php echo (isset($row['purpose_id']) && $row['purpose_id'] == 4) ? ' selected' : ''; ?>>Miscellaneous</option>
                <option value="5"<?php echo (isset($row['purpose_id']) && $row['purpose_id'] == 5) ? ' selected' : ''; ?>>Maintenance</option>
            </select>

            <label for="amount" style="top: 10px; position: relative;">Enter Amount</label>
            <input type="tel" autocomplete="off" id="amount" name="amount" placeholder="Enter Amount" maxlength="10" required>

            <label for="payment" style="top: 10px; position: relative;">Enter Reference Number</label>
            <input type="tel" autocomplete="off" id="payment" name="payment_intent_id" placeholder="Enter Reference Number">

            <label for="payment_type" style="top: 10px; position: relative;">Payment Type</label>
            <select type="option" name="payment_type" id="payment_type">
                <option value="1"<?php echo (isset($row['payment_type_id']) && $row['payment_type_id'] == 1) ? ' selected' : ''; ?>>CASH IN</option>
                <option value="2"<?php echo (isset($row['payment_type_id']) && $row['payment_type_id'] == 2) ? ' selected' : ''; ?>>Gcash</option>
            </select>

            <label for="status_type" style="top: 10px; position: relative;">Status</label>
            <select type="option" name="status_type" id="status_type">
                <option value="0"<?php echo (isset($row['status_id']) && $row['status_id'] == 0) ? ' selected' : ''; ?>>Pending</option>.
                <option value="2"<?php echo (isset($row['status_id']) && $row['status_id'] == 2) ? ' selected' : ''; ?>>Cancel</option>
                <option value="5"<?php echo (isset($row['status_id']) && $row['status_id'] == 5) ? ' selected' : ''; ?>>Paid</option>
                <option value="7"<?php echo (isset($row['status_id']) && $row['status_id'] == 7) ? ' selected' : ''; ?>>Refund</option>
            </select>

            <button type="close" id="close" name="modal-close" value="close">Close</button>
            <input type="submit" name="payment-modal-submit" value="Submit Payment">
        </form>
    </div>
</div>


<div class="modal" id="viewModal">
    <div class="modal-dialog">
    <div class="modal-content" value="<?php echo isset($row['payment_id']) ? $row['payment_id']: ''; ?>">

      <div class="modal-header">
        <h2 class="text-muted">Payment <span class="danger">Details</span></h2>
        <span type="button" class="close" data-dismiss="modal"><ion-icon name="close">&times;</ion-icon></span>
      </div>

      <div class="modal-body">
        No Data...
      </div>
      
    </div>
  </div>
</div>

<div class="modal" id="editModal">
    <div class="modal-dialog">
    <div class="modal-content" value="<?php echo isset($row['payment_id']) ? $row['payment_id']: ''; ?>">

      <div class="modal-header">
        <h2 class="text-muted">Edit Payment<span class="danger"> Detail</span></h2>
        <span type="button" class="close" data-dismiss="modal"><ion-icon name="close">&times;</ion-icon></span>
      </div>

      <div class="modal-body">
        No Data...
      </div>
      
    </div>
  </div>
</div>

<script>
        // Edit Modal
    $(document).ready(function(){
    $('.edit').click(function(){
    id_emp = $(this).attr('id')
    $.ajax({url: "select.php",
    method:'post',
    data:{ed_id:id_emp},
    success: function(result){
    $(".modal-body").html(result);}});
    $('#editModal').modal("show");})})

     // View Modal
    $(document).ready(function(){
    $('.view').click(function(){
    id_emp = $(this).attr('id')
    $.ajax({url: "select.php",
    method:'post',
    data:{vi_id:id_emp},
    success: function(result){
    $(".modal-body").html(result);}});
    $('#viewModal').modal("show");})})

        // Add Modal
    const modal = document.getElementById('myModal');
    const openBtn = document.getElementById('modal-pay');
    const closeBtn = document.getElementById('closeModal');
    const closeBtne1 = document.getElementById('close');
        openBtn.addEventListener('click', () => {
    modal.style.display = 'block';});
        closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';});
        closeBtne1.addEventListener('click', () => {
    modal.style.display = 'none';});

    function confirmPayment() {
        return confirm("Are you sure you want to proceed with this payment?");
    }
</script>
            </main>
         </div>
            <script src="notifications.js"></script>
            <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    </body>
</html>