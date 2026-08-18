<link rel="stylesheet" href="styles.css"/>
<body>

<?php
require ('db.php');
include ('auth_session.php');

//Tenant Edit
    if(isset($_POST["cus_id"]))  
    {
    $output = '';
    $query = "SELECT tenant_tbl.*, user_type.user_type_id, user_type.user_type, status.status_id
            FROM tenant_tbl
            INNER JOIN status 
            ON tenant_tbl.status_id = status.status_id
            INNER JOIN user_type 
            ON tenant_tbl.user_type_id = user_type.user_type_id
            WHERE tenant_id = '" . $_POST["cus_id"] . "'";

    $result = mysqli_query($con, $query);  
    $output .= '  
    <div class="edit-tenant" id="editModal">
                    <div class="modal-edit-content">';
    while($row = mysqli_fetch_array($result))  
    {  
         $output .= '  
         <form action="proc.php" method="POST">
         <input type="hidden" name="edittenant_id" value ="'.$row['tenant_id'].'">

         <label for="tenant_type" style="top: 10px; position: relative;">Tenant Type</label>
         <input type="text" name="tenant_type" id="tenant_type" value="'.$row['user_type'].'" disabled>

         <label for="tenant_id" style="top: 10px; position: relative;">Tenant ID</label>
         <input type="text" name="tenant_id" id="tenant_id" value="'.$row['tenant_id'].'" disabled>

         <label for="first_name" style="top: 10px; position: relative;">First Name</label>
         <input type="text" autocomplete="off" id="first_name" name="first_name" value="'.$row['first_name'].'">

         <label for="last_name" style="top: 10px; position: relative;">Last Name</label>
         <input type="text" autocomplete="off" id="last_name" name="last_name"  value="'.$row['last_name'].'">

         <label for="email" style="top: 10px; position: relative;">Email</label>
         <input type="email" autocomplete="off" id="email" name="email" value="'.$row['email'].'" disabled>

         <label for="contacts" style="top: 10px; position: relative;">Contact Number</label>
         <input type="tel" autocomplete="off" id="contacts" name="contacts" value="'.$row['contacts'].'" pattern="[0]{1}[9]{1}[0-9]{9}" maxlength="11">

         <label for="status_type" style="top: 10px; position: relative;">Status</label>
            <select type="option" name="status_type" id="status_type">
                <option value="11"'. ($row['status_id'] == 11 ? ' selected' : '').'>ACTIVE</option>
                <option value="10"'. ($row['status_id'] == 10 ? ' selected' : '').'>INACTIVE</option>
                <option value="12"'. ($row['status_id'] == 12 ? ' selected' : '').'>DISABLE</option>
         </select>

         <input type="submit" name="tenant-modal-edit-submit" class="editTenant" value="Edit"> 
         <button type="close" class="close" data-dismiss="modal">Close</button>
         </form>
              ';  }  
    $output .= "</div></div>";  
    echo $output;  }

//Tenant Reset Password
    if(isset($_POST["resp_id"]))  
    {
    $output = '';
    $query = "  SELECT * FROM tenant_tbl 
                WHERE tenant_id = '".$_POST["resp_id"]."'";  
    $result = mysqli_query($con, $query);  
    $output .= '  
        <div class="edit-tenant" id="editModal">
        <div class="modal-edit-content">';
    while($row = mysqli_fetch_array($result))  
    {  
    $output .= '  
         <form action="proc.php" method="POST">
         <input type="hidden" name="reset_pass" value ="'.$row['tenant_id'].'">
    
         <label for="password" style="top: 10px; position: relative;">Password</label>
         <input type="password" name="password" id="pass" placeholder="Password">
    
         <label for="cpassword" style="top: 10px; position: relative;">Confirm Password</label>
         <input type="password" name="cpassword" id="cpass" placeholder="Confirm Password" >
    
         <input type="submit" name="tenant-modal-reset-submit" class="editTenant" value="Edit"> 
         <button type="close" class="close" data-dismiss="modal">Close</button>
         </form>
              ';  }  
    $output .= "</div></div>";  
    echo $output;  }

//Contact View
    if(isset($_POST["contact_id"]))  
    {
    $output = '';
    $query = "  SELECT * FROM contact_tbl
                JOIN status 
                ON contact_tbl.status_id = status.status_id
                WHERE con_id = '".$_POST["contact_id"]."'";

    $query2 = " UPDATE `contact_tbl` SET `status_id`='4' WHERE con_id = '".$_POST["contact_id"]."'";
    $result  = mysqli_query($con, $query);
    $result2 = mysqli_query($con, $query2); 
    $output .= '  
    <div class="contacts-rec" id="contactModal">
                    <div class="modal-contact-content">';
    while($row = mysqli_fetch_array($result))  
    {  
         $output .= '  

         <h3 for="con_datetime" style="float: right; position: relative; top: 17px; right: 20px; font-style: italic;">'.$row ['con_datetime'].'</h3>
         
         <h2 for="con_sub" style="top: 10px; position: relative; height: 5dvh;">Subject: </h2>
         <input type="see" name="con_sub" id="con_sub" placeholder="'.$row ['con_sub'].'" disabled>

         <h2 for="full_name" style="top: 10px; position: relative; height: 5dvh;">From: </h2>
         <input type="see" name="full_name" id="full_name" placeholder="'.$row ['full_name'].'" disabled>

         <h2 for="email" style="top: 10px; position: relative; height: 5dvh;">Email</h2>
         <input type="see" id="con_email" name="con_email" placeholder="'.$row ['con_email'].'" disabled>

         <textarea type="textbox" id="con_mes rows="4" cols="50" placeholder="'.$row ['con_mes'].'" disabled></textarea>
              ';  
    }  
    $output .= "</div></div>";  
    echo $output;  }

    //Tenant Admin Message Modal View
    if (isset($_POST["message_id"])) {
        $output = '';
        $query = "  SELECT m.message_id, 
                           CONCAT(t.first_name, ' ', t.last_name) AS tenant_name, 
                           m.subject, 
                           m.message, 
                           m.date_sent, 
                           s.status_type
                    FROM message_tbl m
                    JOIN tenant_tbl t ON m.tenant_id = t.tenant_id
                    JOIN status s ON m.status_id = s.status_id
                    WHERE m.message_id = '".$_POST["message_id"]."'";
    
        $result = mysqli_query($con, $query);  
        $output .= '  
        <div class="modal-message" id="viewModal">
            <div class="modal-message-content">';
    
        while ($row = mysqli_fetch_array($result)) {  
            $output .= '  
    
            <h3 for="message_date" style="float: right; position: relative; top: 17px; right: 20px; font-style: italic;">'.$row['date_sent'].'</h3>
    
            <h2 for="subject" style="top: 10px; position: relative; height: 5dvh;">Subject: </h2>
            <input type="see" name="subject" id="subject" placeholder="'.$row['subject'].'" disabled>
    
            <h2 for="from" style="top: 10px; position: relative; height: 5dvh;">From: </h2>
            <input type="see" name="from" id="from" placeholder="'.$row['tenant_name'].'" disabled>
    
            <h2 for="status" style="top: 10px; position: relative; height: 5dvh;">Status: </h2>
            <input type="see" name="status" id="status" placeholder="'.$row['status_type'].'" disabled>
    
            <h2 for="message" style="top: 10px; position: relative;">Message: </h2>
            <textarea type="textbox" id="message" rows="4" cols="50" placeholder="'.$row['message'].'" disabled></textarea>
            ';  
        }  
        $output .= "</div></div>";  
        echo $output;
    }

//Room Edit    
    if(isset($_POST["ro_id"]))  
    {   
    $output = '';
    $query = "SELECT room_tbl.*, tenant_tbl.user_type_id, user_type.user_type_id, user_type.user_type, status.status_id
            FROM room_tbl 
            INNER JOIN tenant_tbl 
            ON room_tbl.tenant_id = tenant_tbl.tenant_id 
            INNER JOIN user_type 
            ON tenant_tbl.user_type_id = user_type.user_type_id
            INNER JOIN status 
            ON room_tbl.status_id = status.status_id 
            WHERE room_id = '" . $_POST["ro_id"] . "'";

    $result = mysqli_query($con, $query);  
    $output .= '  
    <div class="room-edit" id="roomModal">
                    <div class="modal-room-content">';
    while($row = mysqli_fetch_array($result))  
    {  
         $output .= '  
         <form action="proc.php" method="POST">
         <input type="hidden" name="room_edit" value ="'.$row['room_id'].'">

         <label for="tenant_type" style="top: 10px; position: relative;">Tenant Type</label>
         <input type="text" name="tenant_type" id="tenant_type" value="'.$row['user_type'].'" disabled>

         <label for="room_number" style="top: 10px; position: relative;">Unit Number</label>
         <input type="text" name="room_number" id="room_number" value="'.$row['room_number'].'">

         <label for="room_floor" style="top: 10px; position: relative;">Unit Floor</label>
         <input type="text" name="room_floor" id="room_floor" value="'.$row['room_floor'].'">

         <label for="tenant_id" style="top: 10px; position: relative;">Tenant ID</label>
         <input type="text" name="tenant_id" id="tenant_id" value="'.$row['tenant_id'].'">

         <label for="status_type" style="top: 10px; position: relative;">Status</label>
            <select type="option" name="status_type" id="status_type">
                <option value="11"'. ($row['status_id'] == 11 ? ' selected' : '').'>ACTIVE</option>
                <option value="10"'. ($row['status_id'] == 10 ? ' selected' : '').'>INACTIVE</option>
                <option value="12"'. ($row['status_id'] == 12 ? ' selected' : '').'>DISABLE</option>
            </select>

         <input type="submit" name="room-modal-edit-submit" class="editRoom" value="Edit"> 
         <button type="close" class="close" data-dismiss="modal">Close</button>
         </form>
              ';  }  
    $output .= "</div></div>";  
    echo $output;  }

//Payment View
    if(isset($_POST["vi_id"]))  
    {
    $output = '';
    $query = "  SELECT * FROM payment_tbl
                INNER JOIN status 
                ON payment_tbl.status_id = status.status_id
                INNER JOIN tenant_tbl 
                ON payment_tbl.tenant_id = tenant_tbl.tenant_id
                INNER JOIN payment 
                ON payment_tbl.payment_type_id = payment.payment_type_id
                WHERE payment_id = '".$_POST["vi_id"]."'";

    $result  = mysqli_query($con, $query);
    $output .= '  
    <div class="view-pay" id="viewModal">
                    <div class="modal-view-pay-content">';
    while($row = mysqli_fetch_array($result))  
    {  
         $output .= '  
         
         <h6 for="payment_id" style="top: 10px; position: relative; height: 5dvh;">Payment ID:</h6>
         <input type="view" name="payment_id" id="payment_id" placeholder="'.$row ['payment_id'].'" disabled>

         <h6 for="tenant_id" style="top: 10px; position: relative; height: 5dvh;">Tenant ID:</h6>
         <input type="view" id="tenant_id" name="tenant_id" placeholder="'.$row ['tenant_id'].'" disabled>

         <h6 for="email" style="top: 10px; position: relative; height: 5dvh;">Email:</h6>
         <input type="view" name="email" id="email" placeholder="'.$row ['email'].'" disabled>

         <h6 for="name" style="top: 10px; position: relative; height: 5dvh;">Name:</h6>
         <input type="view" name="name" id="name" placeholder="'.$row ['first_name'].' '.$row['last_name'].'" disabled>

         <h6 for="contact_number" style="top: 10px; position: relative; height: 5dvh;">Contact Number:</h6>
         <input type="view" id="contact_number" name="contact_number" placeholder="'.$row ['contact_number'].'" disabled>

         <h6 for="amount" style="top: 10px; position: relative; height: 5dvh;">Amount:</h6>
         <input type="view" id="amount" name="amount" placeholder="'.$row ['amount'].'" disabled>

         <h6 for="payment" style="top: 10px; position: relative; height: 5dvh;">Payment Number:</h6>
         <input type="view" id="payment" name="payment" placeholder="'.$row ['payment_intent_id'].'" disabled>
         
         <h6 for="date_entry" style="top: 10px; position: relative; height: 5dvh;">Date Entry:</h6>
         <input type="view" id="date_entry" name="date_entry" placeholder="'.$row ['date_entry'].'" disabled>

         <h6 for="date_paid" style="top: 10px; position: relative; height: 5dvh;">Date Paid:</h6>
         <input type="view" id="date_paid" name="date_paid" placeholder="'.$row ['date_paid'].'" disabled>

         <h6 for="payment_type" style="top: 10px; position: relative; height: 5dvh;">Payment Type:</h6>
         <input type="view" id="payment_type" name="payment_type" placeholder="'.$row ['payment_type'].'" disabled>

         <h6 for="status_type" style="top: 10px; position: relative; height: 5dvh;">Status:</h6>
         <input type="view" id="payment_type" name="status_type" placeholder="'.$row ['status_type'].'" disabled>

         <button type="close" class="close" style="float: right;" data-dismiss="modal">Close</button>
              ';  
    }  
    $output .= "</div></div>";  
    echo $output;  }

//Payment Edit
    if(isset($_POST["ed_id"]))  
    {
    $output = '';
    $query = "  SELECT payment_tbl.* , tenant_tbl.tenant_id , tenant_tbl.email,
                       status.status_id, payment.payment_type, payment.payment_type_id
                FROM payment_tbl
                JOIN status 
                ON payment_tbl.status_id = status.status_id
                INNER JOIN tenant_tbl 
                ON payment_tbl.tenant_id = tenant_tbl.tenant_id
                INNER JOIN payment 
                ON payment_tbl.payment_type_id = payment.payment_type_id
                WHERE payment_id = '".$_POST["ed_id"]."'"; 
    $result = mysqli_query($con, $query);  

    $output .= '  
    <div class="edit-pay" id="editpayModal">
                    <div class="modal-edit-pay-content">';
    while($row = mysqli_fetch_array($result))  
    {  
         $output .= '  
         <form action="proc.php" method="POST">
         <input type="hidden" name="payment_edit" value ="'.$row['payment_id'].'">

         <label for="payment_id" style="top: 10px; position: relative;">Payment ID:</label>
         <input type="text" name="payment_id" id="payment_id" placeholder="'.$row ['payment_id'].'" disabled>

         <label for="tenant_id" style="top: 10px; position: relative;">Tenant ID:</label>
         <input type="text" id="tenant_id" name="tenant_id" placeholder="'.$row ['tenant_id'].'" disabled>

         <label for="email" style="top: 10px; position: relative;">Email:</label>
         <input type="text" name="email" id="email" placeholder="'.$row ['email'].'" disabled>

         <label for="contact_number" style="top: 10px; position: relative;">Contact Number:</label>
         <input type="text" id="contact_number" name="contact_number" placeholder="'.$row ['contact_number'].'" disabled>

         <label for="amount" style="top: 10px; position: relative;">Amount:</label>
         <input type="text" id="con_email" name="con_email" placeholder="'.$row ['amount'].'" disabled>

         <label for="amount" style="top: 10px; position: relative;">Payment Reference:</label>
         <input type="text" id="con_email" name="con_email" placeholder="'.$row ['payment_intent_id'].'" disabled>
         
         <label for="date_entry" style="top: 10px; position: relative">Date Entry:</label>
         <input type="text" id="date_entry" name="date_entry" placeholder="'.$row ['date_entry'].'" disabled>

         <label for="payment_type" style="top: 10px; position: relative;">Payment Type:</label>
         <input type="text" id="payment_type" name="payment_type" placeholder="'.$row ['payment_type'].'" disabled>

         <label for="status_type" style="top: 10px; position: relative;">Status</label>
         <select type="option" name="status_type" id="status_type">
                <option value="0"'. ($row['status_id'] == 0 ? ' selected' : '').'>Pending</option>
                <option value="2"'. ($row['status_id'] == 2 ? ' selected' : '').'>Cancel</option>
                <option value="5"'. ($row['status_id'] == 5 ? ' selected' : '').'>Paid</option>
                <option value="7"'. ($row['status_id'] == 7 ? ' selected' : '').'>Refund</option>
         </select>

         <button type="close" class="close" data-dismiss="modal">Close</button>
         <input type="submit" name="tenant-modal-edit-submit" class="editTenant" value="Edit"> 
         </form>
              ';  }  
    $output .= "</div></div>";  
    echo $output;  }

//Inventory Edit
    if(isset($_POST["inv_id"]))  
    {
    $output = '';
    $query = "SELECT * FROM inventory_tbl WHERE item_id = '" . $_POST["inv_id"] . "'"; 
    $result = mysqli_query($con, $query);  

    $output .= '  
    <div class="edit-inventory" id="editModal">
        <div class="modal-edit-content">';

    while($row = mysqli_fetch_array($result))  
    {  
        $output .= '  
        <form action="proc.php" method="POST">
            <input type="hidden" name="edit_item_id" value ="'.$row['item_id'].'">

            <label for="item_name" style="position: relative; top: 10px;">Item Name</label>
            <input type="text" autocomplete="off" id="item_name" name="item_name" value="'.$row['item_name'].'" required>

            <label for="quantity" style="position: relative; top: 10px;">Quantity</label>
            <input type="text" autocomplete="off" id="quantity" name="quantity" value="'.$row['quantity'].'" min="1" required>

            <label for="on_hand" style="position: relative; top: 10px;">On Hand</label>
            <input type="text" autocomplete="off" id="on_hand" name="on_hand" value="'.$row['on_hand'].'" min="1" required>
            
            <label for="owner" style="position: relative; top: 10px;">Ownership by</label>
            <input type="text" autocomplete="off" id="owner" name="owner" value="'.$row['owner'].'" required>

             <label for="status_type" style="top: 10px; position: relative;">Status</label>
            <select type="option" name="status_type" id="status_type">
                <option value="15"'. ($row['status_id'] == 15 ? ' selected' : '').'>Available</option>
                <option value="16"'. ($row['status_id'] == 16 ? ' selected' : '').'>Unavailable</option>
         </select>

            <input type="submit" name="edit-item-submit" class="editItem" value="Save Changes"> 
             <button type="close" class="close" data-dismiss="modal">Close</button>
        </form>';
    }  

    $output .= "</div></div>";  
    echo $output; }

//Borrow Edit
    if (isset($_POST["bor_id"])) {
    $output = '';
    $query = "SELECT borrow_tbl.*, inventory_tbl.item_id, inventory_tbl.item_name, inventory_tbl.quantity, 
                    inventory_tbl.on_hand, status.status_id, tenant_tbl.tenant_id, tenant_tbl.first_name
              FROM borrow_tbl
              JOIN inventory_tbl ON borrow_tbl.item_id = inventory_tbl.item_id
              JOIN status ON borrow_tbl.status_id = status.status_id
              JOIN tenant_tbl ON borrow_tbl.tenant_id = tenant_tbl.tenant_id
              WHERE borrow_id = '".$_POST["bor_id"]."'"; 

    $result = mysqli_query($con, $query);  

    $output .= '  
    <div class="edit-borrow" id="editbor">
        <div class="modal-edit-content">';

    while ($row = mysqli_fetch_array($result)) {  
        $output .= '  
        <form action="proc.php" method="POST">
            <input type="hidden" name="edit_borrow_id" value ="'.$row['borrow_id'].'">

            <label for="borrow_name" style="position: relative; top: 10px;">Borrower Name</label>
            <input type="text" autocomplete="off" id="borrow_name" name="borrow_name" value="'.$row['first_name'].'" disabled>

            <label for="item_name" style="position: relative; top: 10px;">Item Name</label>
            <input type="text" autocomplete="off" id="item_name" name="item_name" value="'.$row['item_name'].'" disabled>

            <label for="borrow_qty" style="position: relative; top: 10px;">Quantity Borrowed</label>
            <input type="number" autocomplete="off" id="borrow_qty" name="borrow_qty" value="' .$row['borrow_qty'].'" required min="1">

            <label for="borrow_on_hand" style="position: relative; top: 10px;">Borrow On Hand</label>
            <input type="number" autocomplete="off" id="borrow_on_hand" name="borrow_on_hand" value="' .$row['borrow_on_hand'].'" required min="0">

            <label for="status_type" style="top: 10px; position: relative;">Status</label>
            <select type="option" name="status_type" id="status_type" onchange="toggleFields(this.value)">
                <option value="17"'. ($row['status_id'] == 17 ? ' selected' : '').'>Borrowed</option>
                <option value="18"'. ($row['status_id'] == 18 ? ' selected' : '').'>Returned</option>
            </select>

            <input type="submit" name="edit-borrow-submit" class="editborrow" value="Save Changes"> 
        </form>';
    }  

    $output .= "</div></div>";  
    echo $output; 
    }

//Maintenance Edit
    if (isset($_POST["maintedit_id"])) {  
    $output = '';
    
    $query = "SELECT maintenance_tbl.*, 
                     tenant_tbl.first_name, tenant_tbl.last_name, 
                     room_tbl.room_number, room_tbl.room_floor, 
                     status.status_type
              FROM maintenance_tbl
              JOIN tenant_tbl ON maintenance_tbl.tenant_id = tenant_tbl.tenant_id
              JOIN status ON maintenance_tbl.status_id = status.status_id
              LEFT JOIN room_tbl ON maintenance_tbl.room_id = room_tbl.room_id
              WHERE maintenance_tbl.request_id = '".$_POST["maintedit_id"]."'";  

    $result = mysqli_query($con, $query);  

    $output .= '  
    <div class="edit-maintenance" id="editMaint">
        <div class="modal-edit-content">';

    while ($row = mysqli_fetch_array($result)) {  
        $output .= '  
        <form action="proc.php" method="POST">
            <input type="hidden" name="edit_maintenance_id" value ="'.$row['request_id'].'">

            <label for="tenant_name" style="position: relative; top: 10px;">Tenant Name</label>
            <input type="text" autocomplete="off" id="tenant_name" name="tenant_name" 
                   value="'.$row['first_name'].' '.$row['last_name'].'" disabled>

            <label for="room_info" style="position: relative; top: 10px;">Unit (Floor/Number)</label>
            <input type="text" autocomplete="off" id="room_info" name="room_info" 
                   value="'.$row['room_floor'].', '.$row['room_number'].'" disabled>

            <label for="issue" style="position: relative; top: 10px;">Issue</label>
            <input type="text" autocomplete="off" id="issue" name="issue" 
                   value="'.$row['issue'].'" disabled>

            <label for="status_type" style="top: 10px; position: relative;">Status</label>
            <select type="option" name="status_id" id="status_type">
                <option value="0"'. ($row['status_id'] == 0 ? ' selected' : '').'>Pending</option>
                <option value="2"'. ($row['status_id'] == 2 ? ' selected' : '').'>Cancel</option>
                <option value="13"'. ($row['status_id'] == 13 ? ' selected' : '').'>Completed</option>
            </select>

            <label for="description" style="position: relative; top: 10px;">Description</label>
            <textarea type="textbox" id="con_mes rows="4" cols="50" placeholder="'.$row ['description'].'" disabled></textarea>

            <input type="submit" name="edit-maintenance-submit" class="editMaintenance" value="Save Changes"> 
        </form>';
    }  

    $output .= "</div></div>";  
    echo $output;
}   

//Tenant Inbox
if(isset($_POST["messages_id"]))  
{
    $output = '';
    $query = "  SELECT message_tbl.* , status.* , tenant_tbl.tenant_id, tenant_tbl.email
                FROM message_tbl
                JOIN status 
                ON message_tbl.status_id = status.status_id
                JOIN tenant_tbl
                ON message_tbl.tenant_id = tenant_tbl.tenant_id
                WHERE message_id = '".$_POST["messages_id"]."'";

    $query2 = " UPDATE `message_tbl` SET `status_id`='4' WHERE message_id = '".$_POST["messages_id"]."'";
    $result  = mysqli_query($con, $query);
    $result2 = mysqli_query($con, $query2); 
    $output .= '  
    <div class="message-mo" id="messageModal">
          <div class="modal-message">';
    while($row = mysqli_fetch_array($result))  
    {  
         $output .= '  

         <h3 for="con_datetime" style="color: var(--color-dark);float: right;position: relative;top: -4px;right: 20px;font-style: italic;"">'.$row ['date_sent'].'</h3>
         
         <label for="subject" style=" font-size: 25px; color: var(--color-info-dark); font-weight: 650; bottom: 5px; position: relative;">Subject: </label>
         <input type="text" name="con_sub" id="con_sub" placeholder="'.$row ['subject'].'" disabled>

         <label for="email" style=" font-size: 25px; color: var(--color-info-dark); font-weight: 650; bottom: 5px; position: relative; ">From: </label>
         <input type="text" name="email" id="email" placeholder="'.$row ['send_by'].'" disabled>

         <label for="admin" style=" font-size: 25px; color: var(--color-info-dark); font-weight: 650; bottom: 5px; position: relative;">To: </label>
         <input type="text" name="admin" id="admin" placeholder="'.$row ['email'].'" disabled>

         <textarea type="text" id="con_mes rows="4" cols="50" placeholder="'.$row ['message'].'" disabled></textarea>
              ';  
    }  
    $output .= "</div></div>";  
    echo $output;  }

    // Tenant Message View 
    if (isset($_POST["t_message_id"])) {
    $output = '';
    $query = "SELECT tenant_mes_tbl.*, 
                     CONCAT(tenant_tbl.first_name, ' ', tenant_tbl.last_name) AS full_name, 
                     status.status_type 
              FROM tenant_mes_tbl
              LEFT JOIN tenant_tbl ON tenant_mes_tbl.tenant_id = tenant_tbl.tenant_id
              LEFT JOIN status ON tenant_mes_tbl.status_id = status.status_id
              WHERE t_message_id = '" . $_POST["t_message_id"] . "'";

    $query2 = "UPDATE tenant_mes_tbl SET status_id = '4' WHERE t_message_id = '" . $_POST["t_message_id"] . "'";
    $result  = mysqli_query($con, $query);
    $result2 = mysqli_query($con, $query2);

    $output .= '  
    <div class="message-view" id="viewModal">
        <div class="modal-message-content">';

    while ($row = mysqli_fetch_array($result)) {  
        $output .= '  
        <h3 style="float: right; position: relative; top: 17px; right: 20px; font-style: italic;">' . $row['date_sent'] . '</h3>

        <h2 style="top: 25px;position: relative;height: 5dvh;">From: </h2>
        <input type="text" name="full_name" id="full_name" placeholder="' . $row['full_name'] . '" disabled>
         
        <h2 style="top: 25px;position: relative;height: 5dvh;">Subject: </h2>
        <input type="text" name="subject" id="subject" placeholder="' . $row['subject'] . '" disabled>

        <h2 style="top: 30px;position: relative;height: 5dvh;">Message: </h2>
        <textarea type="textbox" id="message" rows="4" cols="50" placeholder="' . $row['tenant_message'] . '" disabled></textarea>';
    }

    $output .= "</div></div>";  
    echo $output;
    }
    
?>
      
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
