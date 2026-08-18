<?php
session_start();
if (isset($_SESSION['payment_intent_id'])) {
    // You may cancel the payment intent here if needed
    // Example: Log the abandoned payment or update the database
    unset($_SESSION['payment_intent_id']); // Remove session data
}
?>
