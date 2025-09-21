<?php

session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
/**
 * Including connection
 */

include "../../connection.php";
header("Content-Type: application/json");


function get_garage_report($db) {
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $result = mysqli_query($db, "SELECT * FROM service_records 
                                   INNER JOIN services ON service_records.id = services.record_id 
                                   INNER JOIN parts ON service_records.id = parts.record_id");
        
        if (!$result) {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed']);
            exit;
        }
        
        $report_data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $report_data[] = [
                "license_plate" => $row['license_plate'],
                "make" => $row['make'],
                "model" => $row['model'],
                "service_type" => $row['service_type'],
                "service_description" => $row['description'],
                "part_name" => $row['part_name'],
                "quantity" => $row['quantity'],
                "unit_price" => $row['unit_price'],
                // "total_labor_cost" => $row['total_labor'],
                "total_parts_cost" => $row['total_parts'],
                // "tax_amount" => $row['tax_amount'],
                "grand_total" => $row['grand_total'],
                "service_date" => $row['service_date']
            ];
        }

        echo json_encode($report_data);
        exit;
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
}

// Call the function with your database connection
get_garage_report($db);

// Close the database connection
mysqli_close($db);
?>