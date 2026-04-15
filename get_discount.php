<?php

include "./connection.php";
if (isset($_GET['fuelType'])) {
    $fuelType = $_GET['fuelType'];

    // Fetch discount for selected fuel type
    $query = mysqli_query($db, "SELECT discount FROM fuel WHERE type = '$fuelType'");
    $result = mysqli_fetch_assoc($query);

    // Return JSON response
    echo json_encode(['discount' => $result['discount'] ?? 0]);
}
