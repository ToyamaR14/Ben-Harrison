<?php
require('db.php'); // Database connection
include('auth_session.php'); // User authentication
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #ffdddd;
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
            color: red;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: red;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
    <script>
        let countdown = 5; // Redirect in 5 seconds
        function updateCountdown() {
            document.getElementById("timer").innerText = countdown;
            if (countdown === 0) {
                window.location.href = "payments.php";
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
        <h1>Payment Failed</h1>
        <p>Oops! Your payment was not successful. Please try again.</p>
        <p>Redirecting to payments page in <span id="timer" class="countdown">5</span> seconds...</p>
        <a href="payments.php" class="btn">Go to Payments Page</a>
    </div>

</body>
</html>
