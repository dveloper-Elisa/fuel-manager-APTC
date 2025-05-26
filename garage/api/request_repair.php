<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
/**
 * Including connection
 */

include "../../connection.php";

header('Content-Type: application/json');

function get_all_request($db)
{
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'all_requests') {
        $result = mysqli_query($db, "SELECT * FROM request_repair INNER JOIN staff ON request_repair.user_id = staff.stf_code");
        $fuel_data = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $fuel_data[] = [
                "user" => $row['stf_names'],
                "service" => $row['service'],
                "createdAt" => $row['createdAt'],
            ];
        }

        echo json_encode($fuel_data);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SESSION['staff_code'])) {
            echo json_encode(["error" => "User not logged in."]);
            exit;
        }

        $user_id = mysqli_real_escape_string($db, $_SESSION['staff_code']);
        $services = $_POST['service'] ?? [];

        if (empty($services)) {
            echo json_encode(["error" => "No services provided."]);
            exit;
        }

        $successCount = 0;
        foreach ($services as $service) {
            $service_clean = mysqli_real_escape_string($db, $service);
            if (!empty($service_clean)) {
                $query = "INSERT INTO request_repair (user_id, service, createdAt) VALUES ('$user_id', '$service_clean', NOW())";
                if (mysqli_query($db, $query)) {
                    $successCount++;
                }
            }
        }

        echo json_encode([
            "message" => $successCount > 0 ? "$successCount request(s) sent successfully." : null,
            "error" => $successCount === 0 ? "No requests were submitted." : null
        ]);
        exit;
    }


    // Unsupported method
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed."]);
    exit;
}

// Call the function
get_all_request($db);
