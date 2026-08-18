<?php
require('db.php');
require 'vendor/autoload.php'; // Ensure you have Guzzle installed
include('auth_session.php');

use GuzzleHttp\Client;

if (!isset($_SESSION['email'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['email']; 

// Fetch user's status_id from the database
$stmt = $con->prepare("SELECT status_id FROM payment_tbl WHERE tenant_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($status_id);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST["first_name"]; 
    $last_name = $_POST["last_name"];
    $name = $first_name . ' ' . $last_name; 
    $tenant_id = $_POST["tenant_id"];
    $email = $_POST["email_address"];
    $phone = $_POST["contact_number"];
    $amount = intval(floatval($_POST["amount"])); // Convert to centavos
    $purpose_id = $_POST["purpose"];
    $payment_type_id = $_POST["payment_type"];
    
    // Validate required fields
    if (!$tenant_id || !$purpose_id || !$payment_type_id) {
        die("Error: Missing required fields.");
    }

    if ($amount < 200) {
        echo "<script>
                alert('Error: The minimum allowed payment is ₱200.00.');
                window.location.href = 'payments.php';
              </script>";
        exit();
    }

    // Fetch purpose name
    $stmt = $con->prepare("SELECT purpose_type FROM purpose WHERE purpose_id = ?");
    if (!$stmt) {
        die("Error preparing statement: " . $con->error);
    }
    
    $stmt->bind_param("i", $purpose_id);
    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }
    
    $stmt->bind_result($purpose);
    if (!$stmt->fetch()) {
        die("Error: No matching purpose found for ID: $purpose_id");
    }
    $stmt->close();

    if (!$purpose) {
        die("Error: Invalid purpose ID.");
    }

    $api_key = "sk_test_mMqDusjZyei6BSNw5wzBbbbS"; // Replace with your actual PayMongo Test Secret Key
    $client = new Client();

    try {
        // ✅ Create Payment Intent
        $response = $client->post("https://api.paymongo.com/v1/payment_intents", [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($api_key . ':'), 
                'Content-Type'  => 'application/json'
            ],
            'json' => [
                'data' => [
                    'attributes' => [
                        'amount' => $amount*100, // Already in centavos
                        'payment_method_allowed' => ['gcash'],
                        'currency' => 'PHP',
                        'capture_type' => 'automatic',
                        'description' => "Payment for $purpose"
                    ]
                ]
            ]
        ]);
        
        $body = json_decode($response->getBody(), true);
        
        if (!isset($body['data']['id'])) {
            die("Error: Could not create payment intent.");
        }
        
        $payment_intent_id = $body['data']['id'];

        // ✅ Create Checkout Session
        $checkout_response = $client->post("https://api.paymongo.com/v1/checkout_sessions", [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($api_key . ':'), 
                'Content-Type'  => 'application/json'
            ],
            'json' => [
                'data' => [
                    'attributes' => [
                        'billing' => [
                            'name' => $name,
                            'email' => $email,
                            'phone' => $phone
                        ],
                        'line_items' => [
                            [
                                'currency' => 'PHP',
                                'amount' => $amount*100, // Corrected amount in centavos
                                'name' => "Payment for $purpose",
                                'quantity' => 1
                            ]
                        ],
                        'payment_method_types' => ['gcash'],
                        'reference_number' => $payment_intent_id,
                        'send_email_receipt' => true,
                        'show_description' => true,
                        'description' => "Payment for $purpose",
                        'cancel_url' => "https://purple-dragonfly-294859.hostingersite.com/payment_failed.php",
                        'success_url' => "https://purple-dragonfly-294859.hostingersite.com/payment_success.php?payment_intent_id=$payment_intent_id"
                    ]
                ]
            ]
        ]);
        
        $checkout_body = json_decode($checkout_response->getBody(), true);
        
        if (!isset($checkout_body['data']['attributes']['checkout_url'])) {
            die("Error: Could not create checkout session.");
        }   
        
        $checkout_url = $checkout_body['data']['attributes']['checkout_url'];

        // ✅ Save Payment to Database
        $stmt = $con->prepare("INSERT INTO payment_tbl (tenant_id, email_address, contact_number, amount, purpose_id, payment_intent_id, payment_type_id, status_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, '0')");
        $stmt->bind_param("sssdsss", $tenant_id, $email, $phone, $amount, $purpose_id, $payment_intent_id, $payment_type_id);

        if ($stmt->execute()) {
            // ✅ Redirect to PayMongo checkout session
            header("Location: $checkout_url");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

$con->close();
?>
