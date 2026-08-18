<?php
require ('db.php');
include ('auth_session.php');
date_default_timezone_set('Asia/Manila');

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
    <title>Logs</title>
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

<sub>
    <div class="sub-recent anim t1">
            <thead>
                <h2 class="text-muted">Recent Tenant<span class="warning"> Log</span></h2>
                <table>
                    <tr>
                        <th>TENANT ID</th>
                        <th class="warning">ACTION</th>
                        <th>DATE ENTRY</th>
                    </tr>
                </thead>
            <tbody>
            
        <?php

        $query = "SELECT log_tbl.*, log.* , tenant_tbl.tenant_id
                    FROM `log_tbl`
                    JOIN log
                    ON log_tbl.log_type_id = log.log_type_id 
                    JOIN tenant_tbl
                    ON log_tbl.tenant_id = tenant_tbl.tenant_id
                    ORDER BY log_tbl.date_entry DESC
                    LIMIT $start_from, $results_view";

            $result   = mysqli_query($con, $query);


        if ($result->num_rows > 0) {
        foreach ($result as $row)
        { ?>
                <tr>
                    <td> <?php echo $row['tenant_id']  ?> </td>
                    <td> <?php echo $row['log_type']   ?> </td>
                    <td> <?php echo $row['date_entry'] ?> </td>
                </td>
            </tr>
        <?php  } echo "</tbody></table>";
        } else {
            echo "<tr><td colspan='9'>0 results</td></tr>";
        } ?>
    </div>
                   
    <div class="sub-recent anim t2">
        <thead>
            <h2 class="text-muted">Recent Pay <span class="warning">Log</span></h2>
            <table>
                <tr>
                    <th>PAYMENT ID</th>
                    <th class="warning">ACTION</th>
                    <th>DATE ENTRY</th>
                </tr>
            </thead>
        <tbody>
        
    <?php

    $query = "SELECT log_tbl_pay.*, log.* , payment_tbl.payment_id
                    FROM `log_tbl_pay`
                    JOIN log
                    ON log_tbl_pay.log_type_id = log.log_type_id 
                    JOIN payment_tbl
                    ON log_tbl_pay.payment_id = payment_tbl.payment_id
                    ORDER BY log_tbl_pay.date_entry DESC
                    LIMIT $start_from, $results_view";

        $result   = mysqli_query($con, $query);


    if ($result->num_rows > 0) {
    foreach ($result as $row)
    { ?>
            <tr>
                <td> <?php echo $row['payment_id']   ?> </td>
                <td> <?php echo $row['log_type']     ?> </td>
                <td> <?php echo $row['date_entry']   ?> </td>
            </td>
        </tr>
    <?php  } echo "</tbody></table>";
    } else {
        echo "<tr><td colspan='9'>0 results</td></tr>";
    } ?>
</div>

<div class="sub-recent anim t3">
        <thead>
            <h2 class="text-muted">Recent Reserve <span class="warning">Log</span></h2>
            <table>
                <tr>
                    <th>RESERVATION ID</th>
                    <th class="warning">ACTION</th>
                    <th>DATE ENTRY</th>
                </tr>
            </thead>
        <tbody>
        
    <?php

    $query = "SELECT log_tbl_rs.*, log.* , reserve_tbl.reserve_id
                    FROM `log_tbl_rs`
                    JOIN log
                    ON log_tbl_rs.log_type_id = log.log_type_id 
                    JOIN reserve_tbl
                    ON log_tbl_rs.reserve_id = reserve_tbl.reserve_id
                    ORDER BY log_tbl_rs.date_entry DESC
                    LIMIT $start_from, $results_view";

        $result   = mysqli_query($con, $query);


    if ($result->num_rows > 0) {
    foreach ($result as $row)
    { ?>
            <tr>
                <td> <?php echo $row['reserve_id']   ?> </td>
                <td> <?php echo $row['log_type']       ?> </td>
                <td> <?php echo $row['date_entry'] ?> </td>
            </td>
        </tr>
    <?php  } echo "</tbody></table>";
    } else {
        echo "<tr><td colspan='9'>0 results</td></tr>";
    } ?>
</div>
</sub>                

<div class="mid">
<section id="Tenant">
        <div class="log-table anim t1">
                <h1 class="text-muted">Tenant<span class="warning"> Logs</span></h1>
                    <table>
                        <thead>
                            <th>TENANT LOG ID</th>
                            <th>TENANT ID</th>
                            <th class="warning">ACTIONS</th>
                            <th>DATE ENTRY</th>
                        </tr>
                        </thead>
        <tbody>
    <?php   
    $query = "SELECT log_tbl.*, log.*
                FROM `log_tbl`
                JOIN `log`
                ON log_tbl.log_type_id = log.log_type_id
                ORDER BY log_tbl.tenant_log_id DESC
                LIMIT $start_from, $results_per_page";

    $result = mysqli_query($con, $query);
    
    if ($result->num_rows > 0) {
        foreach ($result as $row) {
            echo "<tr>
                    <td> {$row['tenant_log_id']}</td>
                    <td> {$row['tenant_id']}    </td>
                    <td> {$row['log_type']}     </td>
                    <td> {$row['date_entry']}   </td>
                    </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<tr><td colspan='9'>0 results</td></tr>";
            }
                ?>
        <div class="logbtn">
            <a href="log_tenant.php" class="buttonlog">Go to Full Tenant Log</a>
        </div>
    </div>
</section>

<section id="Payment">
        <div class="log-table anim t2">
                <h1 class="text-muted">Payment<span class="warning"> Logs</span></h1>
                    <table>
                        <thead>
                            <th>PAYMENT LOG ID</th>
                            <th>PAYMENT ID</th>
                            <th class="warning">ACTIONS</th>
                            <th>DATE ENTRY</th>
                        </tr>
                        </thead>
        <tbody>
    <?php   
    $query = "SELECT log_tbl_pay.*, log.*
                FROM `log_tbl_pay`
                JOIN `log`
                ON log_tbl_pay.log_type_id = log.log_type_id
                ORDER BY log_tbl_pay.payment_log_id DESC
                LIMIT $start_from, $results_per_page";

    $result = mysqli_query($con, $query);
    
    if ($result->num_rows > 0) {
        foreach ($result as $row) {
            echo "<tr>
                    <td> {$row['payment_log_id']}</td>
                    <td> {$row['payment_id']}    </td>
                    <td> {$row['log_type']}     </td>
                    <td> {$row['date_entry']}   </td>
                    </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<tr><td colspan='9'>0 results</td></tr>";
            }
                ?>
        <div class="logbtn">
            <a href="log_payment.php" class="buttonlog">Go to Full Payment Log</a>
        </div>
    </div>
</section>

<section id="Reserve">
        <div class="log-table anim t3">
                <h1 class="text-muted">Reserve<span class="warning"> Logs</span></h1>
                    <table>
                        <thead>
                            <th>RESERVE LOG ID</th>
                            <th>RESERVE ID</th>
                            <th class="warning">ACTIONS</th>
                            <th>DATE ENTRY</th>
                        </tr>
                        </thead>
        <tbody>
    <?php   
    $query = "SELECT log_tbl_rs.*, log.*
                FROM `log_tbl_rs`
                JOIN `log`
                ON log_tbl_rs.log_type_id = log.log_type_id
                ORDER BY log_tbl_rs.reserve_log_id DESC
                LIMIT $start_from, $results_per_page";

    $result = mysqli_query($con, $query);
    
    if ($result->num_rows > 0) {
        foreach ($result as $row) {
            echo "<tr>
                    <td> {$row['reserve_log_id']}</td>
                    <td> {$row['reserve_id']}    </td>
                    <td> {$row['log_type']}     </td>
                    <td> {$row['date_entry']}   </td>
                    </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<tr><td colspan='9'>0 results</td></tr>";
            }
                ?>
        <div class="logbtn">
            <a href="log_reserve.php" class="buttonlog">Go to Full Reserve Log</a>
        </div>
    </div>
</section>

<section id="Room">
        <div class="log-table anim t4">
                <h1 class="text-muted">Room<span class="warning"> Logs</span></h1>
                    <table>
                        <thead>
                            <th>ROOM LOG ID</th>
                            <th>ROOM ID</th>
                            <th class="warning">ACTIONS</th>
                            <th>DATE ENTRY</th>
                        </tr>
                        </thead>
        <tbody>
    <?php   
    $query = "SELECT log_tbl_r.*, log.*
                FROM `log_tbl_r`
                JOIN `log`
                ON log_tbl_r.log_type_id = log.log_type_id
                ORDER BY log_tbl_r.room_log_id DESC
                LIMIT $start_from, $results_per_page";

    $result = mysqli_query($con, $query);
    
    if ($result->num_rows > 0) {
        foreach ($result as $row) {
            echo "<tr>
                    <td> {$row['room_log_id']}</td>
                    <td> {$row['room_id']}    </td>
                    <td> {$row['log_type']}     </td>
                    <td> {$row['date_entry']}   </td>
                    </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<tr><td colspan='9'>0 results</td></tr>";
            }
                ?>
        <div class="logbtn">
            <a href="log_room.php" class="buttonlog">Go to Full Room Log</a>
        </div>
    </div>
</section>

<section id="Contact">
        <div class="log-table anim t5">
                <h1 class="text-muted">Contact<span class="warning"> Logs</span></h1>
                    <table>
                        <thead>
                            <th>CONTACT LOG ID</th>
                            <th>CONTACT ID</th>
                            <th class="warning">ACTIONS</th>
                            <th>DATE ENTRY</th>
                        </tr>
                        </thead>
        <tbody>
    <?php   
    $query = "SELECT log_tbl_c.*, log.*
                FROM `log_tbl_c`
                JOIN `log`
                ON log_tbl_c.log_type_id = log.log_type_id
                ORDER BY log_tbl_c.contact_log_id DESC
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
                ?>
        <div class="logbtn">
            <a href="log_contacts.php" class="buttonlog">Go to Full Contact Log</a>
        </div>
    </div>
</section>

<section id="Inventory">
    <div class="log-table anim t6">
        <h1 class="text-muted">Inventory<span class="warning"> Logs</span></h1>
            <table>
                <thead>
                    <tr>
                        <th>INVENTORY LOG ID</th>
                        <th>ITEM ID</th>
                        <th class="warning">ACTIONS</th>
                        <th>DATE ENTRY</th>
                    </tr>
                </thead>
                <tbody>
        <?php   
        $query = "SELECT log_tbl_i.*, log.*
                  FROM `log_tbl_i`
                  JOIN `log`
                  ON log_tbl_i.log_type_id = log.log_type_id
                  ORDER BY log_tbl_i.inventory_log_id DESC
                  LIMIT $start_from, $results_per_page";

        $result = mysqli_query($con, $query);
        
        if ($result->num_rows > 0) {
            foreach ($result as $row) {
                echo "<tr>
                        <td>{$row['inventory_log_id']}</td>
                        <td>{$row['item_id']}</td>
                        <td>{$row['log_type']}</td>
                        <td>{$row['date_entry']}</td>
                      </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<tr><td colspan='4'>0 results</td></tr>";
        }
        ?>
        <div class="logbtn">
            <a href="log_inventory.php" class="buttonlog">Go to Full Inventory Log</a>
        </div>
    </div>
</section>

<section id="Borrow">
    <div class="log-table anim t7">
        <h1 class="text-muted">Borrow<span class="warning"> Logs</span></h1>
        <table>
            <thead>
                <tr>
                    <th>BORROW LOG ID</th>
                    <th>BORROW ID</th>
                    <th class="warning">ACTIONS</th>
                    <th>DATE ENTRY</th>
                </tr>
            </thead>
            <tbody>
                <?php   
                $query = "SELECT log_tbl_b.*, log.*
                          FROM `log_tbl_b`
                          JOIN `log`
                          ON log_tbl_b.log_type_id = log.log_type_id
                          ORDER BY log_tbl_b.borrow_log_id DESC
                          LIMIT $start_from, $results_per_page";

                $result = mysqli_query($con, $query);
                
                if ($result->num_rows > 0) {
                    foreach ($result as $row) {
                        echo "<tr>
                                <td>{$row['borrow_log_id']}</td>
                                <td>{$row['borrow_id']}</td>
                                <td>{$row['log_type']}</td>
                                <td>{$row['date_entry']}</td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<tr><td colspan='4'>0 results</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="logbtn">
            <a href="log_borrow.php" class="buttonlog">Go to Full Borrow Log</a>
        </div>
    </div>
</section>

<section id="Maintenance">
    <div class="log-table anim t8">
        <h1 class="text-muted">Maintenance<span class="warning"> Logs</span></h1>
        <table>
            <thead>
                <tr>
                    <th>MAINTENANCE LOG ID</th>
                    <th>MAINTENANCE ID</th>
                    <th class="warning">ACTIONS</th>
                    <th>DATE ENTRY</th>
                </tr>
            </thead>
            <tbody>
                <?php   
                $query = "SELECT log_tbl_m.*, log.*
                          FROM `log_tbl_m`
                          JOIN `log`
                          ON log_tbl_m.log_type_id = log.log_type_id
                          ORDER BY log_tbl_m.log_maint_id DESC
                          LIMIT $start_from, $results_per_page";

                $result = mysqli_query($con, $query);
                
                if ($result->num_rows > 0) {
                    foreach ($result as $row) {
                        echo "<tr>
                                <td>{$row['log_maint_id']}</td>
                                <td>{$row['request_id']}</td>
                                <td>{$row['log_type']}</td>
                                <td>{$row['date_entry']}</td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<tr><td colspan='4'>0 results</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="logbtn">
            <a href="log_maintenance.php" class="buttonlog">Go to Full Maintenance Log</a>
        </div>
    </div>
</section>


        </div>
    </main>            
</div>

<script>
    
    function toggleClearButton() {
    var searchInput = document.getElementById('search');
    var clearButton = document.getElementById('clear');    
        if (searchInput.value.trim() === '') {
            clearButton.style.display = 'none';
        } else {
            clearButton.style.display = 'inline-block';}}

    function clearSearch() {
        var searchInput = document.getElementById('search');
        searchInput.value = '';
        toggleClearButton();
        }

</script>
<script src="notifications.js"></script>
    </body>
</html>