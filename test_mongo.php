<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

$api_key = "sk_test_mMqDusjZyei6BSNw5wzBbbbS"; // Your test key

$client = new Client();

try {
    $response = $client->get("https://api.paymongo.com/v1/sources", [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode($api_key . ':'),
            'Content-Type'  => 'application/json'
        ]
    ]);

    $body = json_decode($response->getBody(), true);
    echo "<pre>";
    print_r($body);
    echo "</pre>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
