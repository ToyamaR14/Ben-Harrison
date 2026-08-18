<?php
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

if (isset($data['data']['attributes']['status'])) {
    $status = $data['data']['attributes']['status'];
    $paymentIntentId = $data['data']['id'];

    file_put_contents("webhook_logs.txt", "Payment ID: $paymentIntentId - Status: $status\n", FILE_APPEND);

    http_response_code(200);
    echo json_encode(["message" => "Webhook received successfully"]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid Webhook"]);
}
?>
