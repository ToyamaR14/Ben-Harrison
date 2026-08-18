<?php
require 'db.php';

$query = "
    SELECT 
        (SELECT COUNT(*) FROM contact_tbl WHERE status_id = '3') AS count_contact,
        (SELECT COUNT(*) FROM reserve_tbl WHERE status_id = '0') AS count_reserve,
        (SELECT COUNT(*) FROM maintenance_tbl WHERE status_id = '0') AS count_maintenance,
        (SELECT COUNT(*) FROM tenant_mes_tbl WHERE status_id = '3') AS count_tenant,
        (SELECT COUNT(*) FROM payment_tbl WHERE status_id = '0') AS count_payment
";

$result = mysqli_query($con, $query);

if (!$result) {
    die(json_encode(["error" => "Query failed: " . mysqli_error($con)]));
}

$row = mysqli_fetch_assoc($result);

$notifications = [];

if ($row['count_contact'] > 0) {
    $notifications[] = [
        "message" => "You have {$row['count_contact']} unread contact messages",
        "count" => $row['count_contact'],
        "url" => "notification.php" 
    ];
}
if ($row['count_reserve'] > 0) {
    $notifications[] = [
        "message" => "You have {$row['count_reserve']} pending reservations",
        "count" => $row['count_reserve'],
        "url" => "reservation.php" // Change this to your actual reservations page
    ];
}
if ($row['count_maintenance'] > 0) {
    $notifications[] = [
        "message" => "You have {$row['count_maintenance']} pending maintenance requests",
        "count" => $row['count_maintenance'],
        "url" => "maintenance.php" // Change this to your actual maintenance page
    ];
}
if ($row['count_tenant'] > 0) {
    $notifications[] = [
        "message" => "You have {$row['count_tenant']} unread tenant messages",
        "count" => $row['count_tenant'],
        "url" => "inbox.php"
    ];
}
if ($row['count_payment'] > 0) {
    $notifications[] = [
        "message" => "You have {$row['count_payment']} pending payments",
        "count" => $row['count_payment'],
        "url" => "payment.php" 
    ];
}

header('Content-Type: application/json');
echo json_encode($notifications, JSON_PRETTY_PRINT);
?>
