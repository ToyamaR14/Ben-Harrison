<?php
$api_key = "sk_test_mMqDusjZyei6BSNw5wzBbbbS"; // Replace with your PayMongo secret key
$payment_intent_id = $_GET['payment_intent_id']; // Get from URL

$ch = curl_init("https://api.paymongo.com/v1/payment_intents/$payment_intent_id");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic " . base64_encode($api_key . ":"),
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data["data"]["attributes"]["status"])) {
    echo "<h2>Payment Status</h2>";
    echo "<p><strong>Status:</strong> " . $data["data"]["attributes"]["status"] . "</p>";
} else {
    echo "<p style='color:red;'>Error fetching payment status.</p>";
}
?>
