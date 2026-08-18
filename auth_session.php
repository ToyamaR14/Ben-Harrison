<?php
require ('db.php');
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: homepage.php');
    exit();
}

$timeout_duration = 1800; // 30 minutes

if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    if ($elapsed_time > $timeout_duration) {
         
        $logType = 10; // 10 represents SESSION END
        $dateEntry = date('Y-m-d H:i:s');
        
        $email = $_SESSION['email'];
        $getTenantIdQuery = "SELECT tenant_id FROM tenant_tbl WHERE email = '$email'";
        $result = mysqli_query($con, $getTenantIdQuery);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $tenant_id = $row['tenant_id'];
            
            $insertLogQuery = "INSERT INTO log_tbl (log_type_id, tenant_id, date_entry, user_ip) 
                               VALUES ('$logType', '$tenant_id', '$dateEntry', '-')";
            $logResult = mysqli_query($con, $insertLogQuery);
            
        session_unset();
        session_destroy();
            if (!$logResult) {
            }
        }
        echo "<script>
                alert('Your session has ended due to inactivity. You will be redirected to the login page.');
                window.location.href = 'login.php?timeout=true';
              </script>";
        exit();
    }
}

$_SESSION['last_activity'] = time();
