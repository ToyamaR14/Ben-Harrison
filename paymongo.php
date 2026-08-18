<?php
require 'vendor/autoload.php'; // Make sure Guzzle is installed

use GuzzleHttp\Client;

$secretKey = 'sk_test_mMqDusjZyei6BSNw5wzBbbbS'; // Replace with your PayMongo secret key
$webhookUrl = 'http://localhost/benharrison/webhook.php'; // Your webhook URL

$client = new Client([
    'base_uri' => 'https://api.paymongo.com/v1/',
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
        'Content-Type'  => 'application/json'
    ]
]);

try {
    $response = $client->post('webhooks', [
        'json' => [
            'data' => [
                'attributes' => [
                    'url'    => $webhookUrl,
                    'events' => ["payment_intent.succeeded", "payment_intent.failed"]
                ]
            ]
        ]
    ]);

    $body = json_decode($response->getBody(), true);
    echo "Webhook created successfully: " . json_encode($body);
} catch (Exception $e) {
    echo "Error creating webhook: " . $e->getMessage();
}
?>
