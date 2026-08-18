<?php
require('db.php'); // Database connection
include('auth_session.php'); // User authentication

$user_id = $_SESSION['tenant_id'];
$query = "UPDATE payment_tbl SET status_id = 5 WHERE tenant_id = ?";
$stmt = $con->prepare($query);

// Bind the parameters
$stmt->bind_param("s", $user_id);

if ($stmt->execute()) {
    $message = "Your payment has been successfully processed!";
} else {
    $message = "Error updating payment status: " . $stmt->error;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #ddffdd;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        .countdown {
            font-size: 20px;
            font-weight: bold;
            color: green;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: green;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
    <script>
        let countdown = 10; // Redirect in 10 seconds
        function updateCountdown() {
            document.getElementById("timer").innerText = countdown;
            if (countdown === 0) {
                window.location.href = "payments.php"; // Redirect to the payments page
            } else {
                countdown--;
                setTimeout(updateCountdown, 1000);
            }
        }
        window.onload = updateCountdown;
    </script>
</head>
<body>

    <div class="container">
        <h1>Payment Successful 🎉</h1>
        <p><?php echo $message; ?></p>
        <p>If payment remained PENDING, please wait for 24 hours or contact the landlord. Thank you!</p>
        <p>Redirecting to your payment page in <span id="timer" class="countdown">10</span> seconds...</p>
        <a href="payments.php" class="btn">Go to Dashboard</a>
    </div>

</body>
</html>
