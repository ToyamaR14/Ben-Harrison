<link rel="stylesheet" href="styler.css"/>
<body>

<?php
require ('db.php');
include ('auth_session.php');

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


    if (isset($_POST["t_message_id"])) {
    $output = '';
    $message_id = $_POST["t_message_id"];

    // Fetch message details from tenant_mes_tbl
    $query = "SELECT tenant_mes_tbl.*, status.*, tenant_tbl.tenant_id, tenant_tbl.email
              FROM tenant_mes_tbl
              JOIN status ON tenant_mes_tbl.status_id = status.status_id
              JOIN tenant_tbl ON tenant_mes_tbl.tenant_id = tenant_tbl.tenant_id
              WHERE tenant_mes_tbl.t_message_id = ?";

    // Prepare and execute query for fetching the message
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $output .= '  
    <div class="message-mo" id="messageModal">
          <div class="modal-message">';

    while ($row = $result->fetch_assoc()) {  
        $output .= '  
         <h3 for="con_datetime" style="color: var(--color-dark);float: right;position: relative;top: -4px;right: 20px;font-style: italic;">' . 
         htmlspecialchars(date("F j, Y", strtotime($row['date_sent']))) . '</h3>
         
         <label for="subject" style=" font-size: 25px; color: var(--color-info-dark); font-weight: 650; bottom: 5px; position: relative;">Subject: </label>
         <input type="text" name="con_sub" id="con_sub" placeholder="' . htmlspecialchars($row['subject']) . '" disabled>

         <label for="email" style=" font-size: 25px; color: var(--color-info-dark); font-weight: 650; bottom: 5px; position: relative; ">From: </label>
         <input type="text" name="email" id="email" placeholder="' . htmlspecialchars($row['email']) . '" disabled>

         <label for="admin" style=" font-size: 25px; color: var(--color-info-dark); font-weight: 650; bottom: 5px; position: relative;">To: </label>
         <input type="text" name="admin" id="admin" placeholder="Admin" disabled>

         <label for="message" style=" font-size: 25px; color: var(--color-info-dark); font-weight: 650; bottom: 5px; position: relative;">Message: </label>
         <textarea type="text" id="con_mes" rows="4" cols="50" placeholder="' . htmlspecialchars($row['tenant_message']) . '" disabled></textarea>';
    }  
    $output .= "</div></div>";  
    echo $output;  
    }

 
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>