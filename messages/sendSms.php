<?php
$role = strtoupper($_SESSION['role']);


require 'vendor/autoload.php';


// Load Africa's Talking SDK
$username = $_ENV['USERNAME'];
$apiKey = $_ENV['API_KEY'];

$AT = new AfricasTalking\SDK\AfricasTalking($username, $apiKey);

// Define SMS service
$sms = $AT->sms();

$logisticsMessage = "Dear Logistic Officer, I have submitted a fuel request and am awaiting your verification. Kindly review it at your earliest convenience. Best regards.";
$deputyMessage = "Dear D/CEO, I have verified the fuel request and we are now awaiting your approval. Best regards.";

// Define message parameters
$recipients = ($role !== 'LOGISTICS' && ($role !== 'CEO' || $role !== 'D/CEO')) ? "+250726982830" : ($role == 'LOGISTICS' ? "+250787647168" : exit);
$message = ($role !== 'LOGISTICS' && ($role !== 'CEO' || $role !== 'D/CEO')) ? "$logisticsMessage" : ($role == 'LOGISTICS' ? "$deputyMessage" : exit);
$from = null;


try {
    $response = $sms->send([
        "to" => $recipients,
        "message" => $message,
        "from" => $from,
    ]);

    print_r($response);

    if (isset($response['status']) && $response['status'] === 'success') {
        echo "✅ SMS Sent Successfully!";
    } else {
        echo "❌ Failed to send SMS. Response: " . print_r($response, true);
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
