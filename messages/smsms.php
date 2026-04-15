
<?php
$role = strtoupper($_SESSION['role']);

// TESTING TWILIO
$logisticsMessage = "Dear Logistic Officer, I have submitted a fuel request and am awaiting your verification. Kindly review it at your earliest convenience. Best regards.";
$deputyMessage = "Dear D/CEO, I have verified the fuel request and we are now awaiting your approval. Best regards.";

require_once './vendor/autoload.php';

use Twilio\Rest\Client;

$sid    = $_ENV['SID'];
$token  = $_ENV['TOKEN'];
$twilio = new Client($sid, $token);

$message = $twilio->messages
    ->create(
        $recipients = ($role !== 'LOGISTICS' && ($role !== 'CEO' || $role !== 'D/CEO')) ? "+250726982830" : ($role == 'LOGISTICS' ? '+250787647168' : exit),
        array(
            "from" => "+18143478556",
            "body" => $message = ($role !== 'LOGISTICS' && ($role !== 'CEO' || $role !== 'D/CEO')) ? "$logisticsMessage" : ($role == 'LOGISTICS' ? "$deputyMessage" : exit)
        )
    );

print($message->sid);
