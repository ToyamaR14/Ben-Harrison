<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reciept Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .receipt {
            width: 50%;
            padding: 20px;
            border: 1px solid #ccc;
            transform: translateX(50%);
            background-color: #fff;
        }
        .receipt h6 {
            text-align: center;
            margin-top: 10px;
            position: relative;
            height: 2dvh;
        }
        .receipt input[type="text"] {
            text-align: center;
            margin-bottom: 10px;
            display: block;
            width: 100%;
            padding: 5px;
            position: relative;
            left: -7px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <h6 for="payment_id">Payment ID:</h6>
        <input type="text" name="payment_id" id="payment_id" value="<?php echo htmlspecialchars($_GET['payment_id'], ENT_QUOTES, 'UTF-8'); ?>" disabled>

        <h6 for="tenant_id">Tenant ID:</h6>
        <input type="text" id="tenant_id" name="tenant_id" value="<?php echo htmlspecialchars($_GET['tenant_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>

        <h6 for="email">Email:</h6>
        <input type="text" name="email" id="email" value="<?php echo htmlspecialchars($_GET['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>

        <h6 for="contact_number">Contact Number:</h6>
        <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($_GET['contact_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>

        <h6 for="amount">Amount:</h6>
        <input type="text" id="amount" name="amount" value="<?php echo htmlspecialchars($_GET['amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>

        <h6 for="date_paid">Date Entry:</h6>
        <input type="text" id="date_paid" name="date_paid" value="<?php echo htmlspecialchars($_GET['date_paid'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>

        <h6 for="payment_type">Payment Type:</h6>
        <input type="text" id="payment_type" name="payment_type" value="<?php echo htmlspecialchars($_GET['payment_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>

        <button type="button" onclick="window.print()">Print</button>
    </div>
</body>
</html>
