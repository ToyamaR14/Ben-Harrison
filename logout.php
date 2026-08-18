<?php 
session_start();
require('db.php');

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $logType = 2;
    $dateEntry = date('Y-m-d H:i:s');

    $userIp = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $userIp = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $userIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    $userIp = filter_var($userIp, FILTER_VALIDATE_IP);

    $insertLogQuery = "INSERT INTO log_tbl (log_type_id, tenant_id, date_entry, user_ip) 
                       VALUES ('$logType', (SELECT tenant_id FROM tenant_tbl WHERE email='$email'), '$dateEntry', ?)";

    $stmt = mysqli_prepare($con, $insertLogQuery);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $userIp); // Bind IP address parameter
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to the homepage
header("location: homepage.php");
exit();
?>
