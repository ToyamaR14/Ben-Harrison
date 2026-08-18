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
    <title>Inventory</title>
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
                <h1 class="text-muted">Manage <span class="warning">Inventory</span></h1>
                <form action="" method="GET">
                <div>
                    <input type="textsearch" autocomplete="off" id="search" name="search" value="<?php if(isset($_GET['search'])){echo $_GET['search']; } ?>" class="form-control" placeholder="Search data" onclick="toggleClearButton()">
                    <button type="search" class="searchbtn">Search</button>
                </div>
                </form>
                <button type="add" class="modal-tenant" id="modal-tenant" data-target="myModal">Add Item</button>
                <button type="add" class="borrow-item" id="borrow-item">Add Borrower</button>
                
            <table>
                <thead>
                <tr>
                    <th>ITEM NAME</th>
                    <th>QUANTITY</th>
                    <th>ON HAND (TENANT)</th>
                    <th>OWNED BY</th>
                    <th>DATE ADDED</th>
                    <th class="warning">LAST UPDATED</th>
                    <th class="success">STATUS</th>
                    <th>EDIT/DELETE</th>
                </tr>
                </thead>
<tbody>
<?php  
    $results_per_page = 20; 
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start_from = ($current_page - 1) * $results_per_page;
    $filtervalues = isset($_GET['search']) ? $_GET['search'] : '';

    // Query to fetch inventory with status
    $query = "SELECT inventory_tbl.item_id, inventory_tbl.item_name, inventory_tbl.quantity, inventory_tbl.on_hand, 
                    inventory_tbl.date_added, inventory_tbl.last_updated, inventory_tbl.owner, status.status_type 
                FROM inventory_tbl
                LEFT JOIN status ON inventory_tbl.status_id = status.status_id";

    // Search filter
    if (!empty($filtervalues)) {
        $query .= " WHERE CONCAT(inventory_tbl.item_id, inventory_tbl.item_name, inventory_tbl.quantity, inventory_tbl.on_hand,
                                inventory_tbl.date_added, inventory_tbl.last_updated, inventory_tbl.owner, status.status_type) 
                    LIKE '%$filtervalues%' ";
    }

    $query .= " ORDER BY inventory_tbl.date_added DESC LIMIT $start_from, $results_per_page";

    $result = mysqli_query($con, $query);
    ?>
           <?php 
            if ($result->num_rows > 0) {
                foreach ($result as $row) {
                    echo "<tr>
                            <td>  {$row['item_name']}  </td>
                            <td>  {$row['quantity']}   </td>
                            <td>  {$row['on_hand']}    </td>
                            <td>  {$row['owner']}  </td>
                            <td>  {$row['date_added']}  </td>
                            <td>  {$row['last_updated']}  </td>
                            <td>  {$row['status_type']}  </td>
                            <td class='inventory-btns'>
                                <input type='hidden' name='item_id' value='{$row['item_id']}'>
                                <button type='edit' class='edit' id='{$row['item_id']}'><ion-icon name='create'></ion-icon></button>
                                <form action='proc.php' method='POST'>
                                    <input type='hidden' name='item-del' value='{$row['item_id']}'>
                                    <button type='delete' name='delete-item' onclick='return confirm(\"Are you sure you want to delete?\");'>
                                        <ion-icon name='trash'></ion-icon>
                                    </button>
                                </form>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php
    // Pagination logic
    $count_query = "SELECT COUNT(*) AS total FROM inventory_tbl 
                    LEFT JOIN status ON inventory_tbl.status_id = status.status_id";

    if (!empty($filtervalues)) {
        $count_query .= " WHERE CONCAT(inventory_tbl.item_id, inventory_tbl.item_name, inventory_tbl.quantity, inventory_tbl.on_hand,
                                        inventory_tbl.date_added, inventory_tbl.last_updated, inventory_tbl.owner, status.status_type) 
                            LIKE '%$filtervalues%'";
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
    ?>
    </div>


<section class="borrow">
    <div class="multi-table">
                <h1 class="text-muted">Manage <span class="warning">Borrower</span></h1>             
            <table>
                <thead>
                <tr>
                    <th>BORROWER</th>
                    <th>ITEM NAME</th>
                    <th>BORROW QUANTITY</th>
                    <th>ON HAND ITEMS(QTY)</TH>
                    <th>DATE BORROWED</th>
                    <th>DATE RETURNED</th>
                    <th class="success">STATUS</th>
                </tr>
                </thead>
<tbody>
<?php  
    $results_per_page = 20; 
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start_from = ($current_page - 1) * $results_per_page;

    // Query to fetch borrow with status
    $query = "SELECT borrow_tbl.borrow_id, borrow_tbl.item_id, inventory_tbl.item_id, inventory_tbl.item_name, inventory_tbl.quantity, inventory_tbl.on_hand,
                 borrow_tbl.date_borrow, borrow_tbl.date_return, borrow_tbl.borrow_qty, borrow_tbl.borrow_on_hand,
                 tenant_tbl.tenant_id, tenant_tbl.first_name, status.status_id, status.status_type 
          FROM borrow_tbl
          JOIN inventory_tbl ON borrow_tbl.item_id = inventory_tbl.item_id
          JOIN tenant_tbl ON borrow_tbl.tenant_id = tenant_tbl.tenant_id
          JOIN status ON borrow_tbl.status_id = status.status_id";

        $query .= " ORDER BY borrow_tbl.date_borrow DESC LIMIT $start_from, $results_per_page";

        $result = mysqli_query($con, $query);
        ?>

        <?php 
        if ($result->num_rows > 0) {
            foreach ($result as $row) {
                // Check if the status_id is 18 (Returned)
                $isDisabled = ($row['status_id'] == 18) ? 'disabled' : '';  // Disable button if status is 'Returned'

                echo "<tr>
                        <td>  {$row['first_name']}      </td>
                        <td>  {$row['item_name']}       </td>
                        <td>  {$row['borrow_qty']}      </td>
                        <td>  {$row['borrow_on_hand']}  </td>
                        <td>  {$row['date_borrow']}     </td>
                        <td>  {$row['date_return']}     </td>
                        <td>  {$row['status_type']}     </td>
                        <td class='borrow-btns'>
                            <input type='hidden' name='borrow_id' value='{$row['borrow_id']}'>
                            <button type='editbor' class='editbor' id='{$row['borrow_id']}' $isDisabled><ion-icon name='create'></ion-icon></button>
                            <form action='proc.php' method='POST'>
                                <input type='hidden' name='bor-del' value='{$row['borrow_id']}'>
                                <button type='delete' name='delete-borrow' onclick='return confirm(\"Are you sure you want to delete?\");'>
                                    <ion-icon name='trash'></ion-icon>
                                </button>
                            </form>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No records found</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <?php
    // Pagination logic
    $count_query = "SELECT COUNT(*) AS total FROM borrow_tbl 
                LEFT JOIN status ON borrow_tbl.status_id = status.status_id";

    if (!empty($filtervalues)) {
        $count_query .= " WHERE CONCAT(borrow_tbl.*, status.status_type) 
                            LIKE '%$filtervalues%'";
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
    ?>
        <div style="display: flex;position: relative;align-items: center;justify-content: center;top: 10rem;">
            <a href="borrow_history.php" class="buttonlog1" style="margin-bottom: 50px;">Go to Borrow History</a>
        </div>
    </div>
</section>
   
<div class="modal" id="myModal">
    <div class="modal-content">
        <span id="closeModal" class="close"><ion-icon name="close">&times;</ion-icon></span>
        <h2 class="text-muted">Enter New <span class="warning">Item</span></h2>
        <form class="manage-modal" id="manage_modal" action="proc.php" method="POST">
            <input type="text" name="item_name" autocomplete="off" placeholder="Item Name" required>
            <input type="text" name="quantity" autocomplete="off" placeholder="Quantity" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            <input type="text" name="on_hand" autocomplete="off" placeholder="On Hand" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            <input type="text" name="owner" autocomplete="off" placeholder="Ownership by">
            <input type="submit" name="add-item" value="Add Item">
            <button type="close" id="close" name="modal-close" value="close">Close</button>
        </form>
    </div>
</div>

<div class="modal" id="editModal">
    <div class="modal-dialog">
    <div class="modal-content" value="<?php echo isset($row['item_id']) ? $row['item_id'] : ''; ?>">

      <div class="modal-header">
        <h2 class="text-muted">Edit <span class="warning">Item</span></h2>
        <span type="button" class="close" data-dismiss="modal"><ion-icon name="close">&times;</ion-icon></span>
      </div>

      <div class="modal-body">
        No Data...
      </div>
      
    </div>
  </div>
</div>

<div class="modal" id="borModal">
    <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h2 class="text-muted">Add<span class="danger"> Borrower</span></h2>
        <span type="button" class="close" data-dismiss="modal"><ion-icon name="close">&times;</ion-icon></span>
      </div>

      <div class="modal-body">
        No Data...
      </div>
      
    </div>
  </div>
</div>

<div class="modal" id="editborModal">
    <div class="modal-dialog">
    <div class="modal-content" value="<?php echo isset($row['borrow_id']) ? $row['borrow_id'] : ''; ?>">

      <div class="modal-header">
        <h2 class="text-muted">Edit <span class="warning">Borrower</span></h2>
        <span type="button" class="close" data-dismiss="modal"><ion-icon name="close">&times;</ion-icon></span>
      </div>

      <div class="modal-body">
        No Data...
      </div>
      
    </div>
  </div>
</div>

<script>
        // Edit Item Modal
        $(document).ready(function(){
        $('.edit').click(function(){
        id_emp = $(this).attr('id')
        $.ajax({url: "select.php",
        method:'post',
        data:{inv_id:id_emp},
        success: function(result){
        $(".modal-body").html(result);}});
        $('#editModal').modal("show");})})

        // Edit Borrower Modal
        $(document).ready(function(){
        $('.editbor').click(function(){
        id_emp = $(this).attr('id')
        $.ajax({url: "select.php",
        method:'post',
        data:{bor_id:id_emp},
        success: function(result){
        $(".modal-body").html(result);}});
        $('#editborModal').modal("show");})})

        // Add Borrower
        $(document).ready(function() {
        $('.borrow-item').click(function() {
            var formHTML = `
                <form action="proc.php" method="POST">
                <label for="borrow_tenant" style="position: relative; top: 10px;">Select Borrower</label>
                <select  type="option" name="tenant_id" required>
                    <option value="" disabled selected>Select Borrower</option>
                    <?php
                    include 'db.php';
                    $tenant_query = "SELECT tenant_id, first_name FROM tenant_tbl";
                    $result = mysqli_query($con, $tenant_query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['tenant_id']}'>{$row['first_name']}</option>";
                    }
                    ?>
                </select>

                <label for="borrow_item" style="position: relative; top: 10px;">Select Item</label>
                <select  type="option" name="item_id" required>
                    <option value="" disabled selected>Select Item</option>
                    <?php
                    $item_query = "SELECT item_id, item_name, quantity, on_hand 
                                FROM inventory_tbl 
                                WHERE quantity > on_hand"; // Only show items with available stock
                    $result = mysqli_query($con, $item_query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['item_id']}'>{$row['item_name']} (Available: " . ($row['quantity'] - $row['on_hand']) . ")</option>";
                    }
                    ?>
                </select>

                <input type="number" name="borrow_quantity" min="1" placeholder="Item Quantity" required>

                <label for="date" style="position: relative; top: 10px;">Date Borrowed:</label>
                <input type="date" name="date_borrow" value="<?php echo date('Y-m-d'); ?>" style="position: relative;top: 10px;" readonly>

                <input type="submit" name="borrow-item" class="borrow-item" value="Borrow">
            </form>
            `;

            $(".modal-body").html(formHTML);
            $('#borModal').modal("show");
        });
        });

        // Add Item Modal
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
                </main>
             </div>
                <script src="notifications.js"></script>
                <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
                <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
        </body>
</html>