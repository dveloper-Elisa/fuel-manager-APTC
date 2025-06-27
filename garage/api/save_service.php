<?php
header('Content-Type: application/json');

include "../../connection.php";

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
    exit;
}

// Insert into service_records table
$stmt = $db->prepare("INSERT INTO service_records (license_plate, make, model, notes, total_parts, grand_total, service_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "ssssdss",
    $input['license_plate'],
    $input['make'],
    $input['model'],
    $input['notes'],
    $input['total_parts_cost'],
    $input['grand_total'],
    $input['service_date']
);

if ($stmt->execute()) {
    $record_id = $stmt->insert_id;

    // Insert services
    $stmt_service = $db->prepare("INSERT INTO services (record_id, service_type, description) VALUES (?, ?, ?)");
    foreach ($input['services'] as $srv) {
        $stmt_service->bind_param("iss", $record_id, $srv['service_type'], $srv['service_description']);
        $stmt_service->execute();
    }

    // Insert parts
    $stmt_part = $db->prepare("INSERT INTO parts (record_id, part_name, quantity, unit_price) VALUES (?, ?, ?, ?)");
    foreach ($input['parts'] as $prt) {
        $stmt_part->bind_param("isid", $record_id, $prt['part_name'], $prt['quantity'], $prt['unit_price']);
        $stmt_part->execute();
    }

    echo json_encode(['status' => 'success', 'message' => 'Record saved successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save record']);
}
