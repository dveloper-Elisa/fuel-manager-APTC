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
$stmt = $db->prepare("INSERT INTO service_records (license_plate, make, model, notes, total_labor, total_parts, tax_amount, grand_total, service_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "ssssdddss",
    $input['license_plate'],
    $input['make'],
    $input['model'],
    $input['notes'],
    $input['total_labor_cost'],
    $input['total_parts_cost'],
    $input['tax_amount'],
    $input['grand_total'],
    $input['service_date']
);

if ($stmt->execute()) {
    $record_id = $stmt->insert_id;

    // Insert services
    $stmt_service = $db->prepare("INSERT INTO services (record_id, service_type, description, labor_hours, labor_rate) VALUES (?, ?, ?, ?, ?)");
    foreach ($input['services'] as $srv) {
        $stmt_service->bind_param("issdd", $record_id, $srv['service_type'], $srv['service_description'], $srv['labor_hours'], $srv['labor_rate']);
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
