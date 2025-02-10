<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

if (!isset($_SESSION["phone"]) || !isset($_SESSION["name"])) {
    header("Location:./login.php");
}

include("./connection.php");

// Check if 'id' is set to generate PDF
if (isset($_GET['id'])) {
    require('fpdf/fpdf.php');

    $sql = mysqli_query($db, 'SELECT * FROM fuel_request WHERE req_id =' . $_GET['id'] . '');
    // Create PDF object
    $fuelDocument = new FPDF();
    $fuelDocument->AddPage();
    $fuelDocument->SetFont('Times', '', 12);
    $fuelDocument->Image('./img/image.png', 60, 10, 100,);
    $fuelDocument->Ln(30);
    $fuelDocument->SetFont('Times', 'B', 16);
    $fuelDocument->Cell(0, 10, 'Fuel Request Document', 0, 1, 'C');
    $fuelDocument->Ln(15);

    // Document formatting
    while ($row = mysqli_fetch_array($sql)) {
        if ($row) {
            // Set font and add a new page if needed
            $fuelDocument->SetFont('Times', '', 10);
            if ($fuelDocument->GetY() > 250) { // Adjust this value if necessary
                $fuelDocument->AddPage();
            }

            // Header of Mission
            $fuelDocument->Cell(95, 10, 'Header of Mission: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['head_mission'], 0, 'L');

            // Signature
            $fuelDocument->SetFont('Times', '', 10);
            $fuelDocument->Cell(95, 10, 'Signature: ', 0, 0, 'L');
            if (!empty($row['signature'])) {
                $fuelDocument->Image($row['signature'], 100, $fuelDocument->GetY(), 20);
            }
            $fuelDocument->Ln(10);

            // Driver Name
            $fuelDocument->Cell(95, 10, 'Driver Name: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['driver_name'], 0, 'L');

            // Vehicle Type
            $fuelDocument->SetFont('Times', '', 10);
            $fuelDocument->Cell(95, 10, 'Vehicle Type: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['vehicle_type'], 0, 'L');

            // Plate Number
            $fuelDocument->SetFont('Times', '', 10);
            $fuelDocument->Cell(95, 10, 'Plate Number: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['plate_number'], 0, 'L');

            // Location From and To
            $fuelDocument->SetFont('Times', '', 10);
            $fuelDocument->Cell(95, 10, 'From: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['location_from'], 0, 'L');

            $fuelDocument->SetFont('Times', '', 10);
            $fuelDocument->Cell(95, 10, 'Destination: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['location_to'], 0, 'L');

            // Date of Departure and Return
            $fuelDocument->SetFont('Times', '', 10);
            $fuelDocument->Cell(95, 10, 'Date of Departure: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['date_from'], 0, 'L');

            $fuelDocument->SetFont('Times', '', 10);
            $fuelDocument->Cell(95, 10, 'Date of Return: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['date_to'], 0, 'L');

            // Fuel Type
            $fuelDocument->SetFont('Times', '', 10);
            $fuelDocument->Cell(95, 10, 'Type of Fuel: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['fuel_type'], 0, 'L');

            // Requested & Received Fuel
            $fuelDocument->SetFont('Times', '', 10);
            $fuelDocument->Cell(95, 10, 'Requested Fuel: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['requested_qty'] . ' Liters', 0, 'L');

            $fuelDocument->SetFont('Times', '', 10);
            $fuelDocument->Cell(95, 10, 'Received Fuel: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['received_qty'] . ' Liters', 0, 'L');

            // Final Section
            $fuelDocument->Ln(20);
            $fuelDocument->Cell(95, 10, 'Prepared by: ', 0, 0, 'L');
            $fuelDocument->Cell(95, 10, 'Approved by: ', 0, 1, 'L');

            $fuelDocument->Cell(95, 10, $row['verified_by'], 0, 0, 'L');
            $fuelDocument->Cell(95, 10, $row['approved_by'], 0, 1, 'L');

            $fuelDocument->Cell(95, 10, 'H/LOGISTIC APTC', 0, 0, 'L');
            $fuelDocument->Cell(95, 10, 'D/CEO & DAF APTC', 0, 1, 'L');
        } else {
            echo 'No Request found';
        }
    }

    // Output the PDF as a download
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="Fuel Report.pdf"');
    $fuelDocument->Output('D', 'Fuel Report.pdf');
    exit;
}

// Query to fetch fuel requests
$id = $_SESSION['staff_code'];
$query = (strtoupper($_SESSION['role']) === 'D/CEO' || strtoupper($_SESSION['role']) === 'CEO') ? "SELECT * FROM fuel_request" : "SELECT * FROM fuel_request WHERE stf_code = '$id'";
$sql = mysqli_query($db, $query);

if (!$sql) {
    die("Error in query: " . mysqli_error($db));
}

// GETING REQUEST DETAILS ON POPUP
if (isset($_GET['req_id'])) {
    $req_id = $_GET['req_id'];
    // Fetch request details from the database
    $query = "SELECT * FROM fuel_request WHERE req_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $req_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row); // Send JSON response
    } else {
        echo json_encode(['error' => 'Request not found']);
    }
    exit;
}

// APPROVE REQUEST ON BEHALF OG LOGISTICS
if (isset($_GET['verify'])) {
    $req_id = $_GET['verify'];
    $verifier = $_SESSION['name'];

    $query = 'UPDATE fuel_request SET verified_by = ? WHERE req_id = ?';
    $stmt = $db->prepare($query);
    $stmt->bind_param('si', $verifier, $req_id);
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Request Verifiyed']);
    } else {
        echo json_encode(['message' => 'Request not Verifiyed']);
    }
    exit;
}

/* 

CANCELING REQUEST on BEHALF OF LOGISTICS
AND ON BEHAKF OF CEO or D/CEO

*/
if (isset($_GET['reject']) || isset($_GET['cancel'])) {
    $req_id = isset($_GET['reject']) ? $_GET['reject'] : $_GET['cancel'];
    $status = 'rejected';
    $query = 'UPDATE fuel_request SET status = ? WHERE req_id = ?';
    $stmt = $db->prepare($query);
    $stmt->bind_param('si', $status, $req_id);

    if (isset($_GET['cancel'])) {
?>
        <script>
            alert("Request Rejected")
            location = './requests.php'
        </script>

<?php
        return;
    }

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Request rejected']);
    } else {
        echo json_encode(['message' => 'Request failed to reject']);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include("./components/side.php"); ?>

        <div class="flex-1 p-6">
            <!-- Top Bar -->
            <div class="flex justify-between items-center bg-white p-4 rounded shadow-md">
                <h1 class="text-xl font-semibold text-lime-700 flex flex-row items-center gap-2"><i class="fa-solid fa-home"></i> <span class="lg:flex md:flex sm:flex hidden">Dashboard</span></h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600"> <?php echo "<b>" . $_SESSION["name"] . "</b>"; ?></span>
                    <?php echo (strtoupper($_SESSION['role']) == 'D/CEO' || strtoupper($_SESSION['role']) == 'CEO') ?
                        "" : '<a href="./index.php" class="bg-lime-700 text-white p-1 rounded-md" alt="Send Request"> New Request </a>'; ?>
                </div>
            </div>

            <!-- Status Filter Links -->
            <div class="mt-4 flex space-x-4">
                <a href="requests.php?status=approved" class="<?php echo (isset($_GET['status']) && $_GET['status'] === 'approved') ? 'border-t-4 border-zinc-900 px-2 py-1 bg-lime-700 text-white rounded-md' : 'px-2 py-1 bg-lime-700 text-white rounded-md'; ?>">✅ Approved</a>
                <a href="requests.php?status=pending" class="<?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'border-t-4 border-zinc-900 px-2 py-1 bg-yellow-600 text-white rounded-md' : 'px-2 py-1 bg-yellow-600 text-white rounded-md' ?>">⏳ Pending</a>
                <a href="requests.php?status=rejected" class="<?php echo (isset($_GET['status']) && $_GET['status'] === 'rejected') ? 'border-t-4 border-zinc-900 px-2 py-1 bg-red-500 text-white rounded-md' : 'px-2 py-1 bg-red-700 text-white rounded-md' ?>">🚫 Canceled</a>
            </div>

            <?php
            // Fetch status from URL
            $status = isset($_GET['status']) ? $_GET['status'] : 'all';
            $id = $_SESSION['staff_code'];

            // Pagination settings
            $itemsPerPage = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $itemsPerPage;

            // SQL query based on status
            if ($status === 'all') {
                $query = (strtoupper($_SESSION['role']) == 'D/CEO' || strtoupper($_SESSION['role']) == 'CEO' || strtolower($_SESSION['role']) == 'logistics') ?
                    "SELECT * FROM fuel_request ORDER BY created_at DESC LIMIT ? OFFSET ?" :
                    "SELECT * FROM fuel_request WHERE stf_code = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
            } else {
                $query = (strtoupper($_SESSION['role']) == 'D/CEO' || strtoupper($_SESSION['role']) == 'CEO' || strtolower($_SESSION['role']) == 'logistics') ?
                    "SELECT * FROM fuel_request WHERE status = ? ORDER BY created_at DESC LIMIT ? OFFSET ?" :
                    "SELECT * FROM fuel_request WHERE status = ? AND stf_code = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
            }

            // Prepare and execute the query
            if ($stmt = $db->prepare($query)) {
                if ($status === 'all') {
                    if (strtoupper($_SESSION['role']) == 'D/CEO' || strtoupper($_SESSION['role']) == 'CEO' || strtolower($_SESSION['role']) == 'logistics') {
                        $stmt->bind_param('ii', $itemsPerPage, $offset);
                    } else {
                        $stmt->bind_param('iii', $id, $itemsPerPage, $offset);
                    }
                } else {
                    if (strtoupper($_SESSION['role']) == 'D/CEO' || strtoupper($_SESSION['role']) == 'CEO' || strtolower($_SESSION['role']) == 'logistics') {
                        $stmt->bind_param('sii', $status, $itemsPerPage, $offset);
                    } else {
                        $stmt->bind_param('siii', $status, $id, $itemsPerPage, $offset);
                    }
                }
                $stmt->execute();
                $result = $stmt->get_result();
            }

            // Fetch total number of records
            $countQuery = ($status === 'all') ? "SELECT COUNT(*) as total FROM fuel_request" : "SELECT COUNT(*) as total FROM fuel_request WHERE status = ?";
            $countStmt = $db->prepare($countQuery);
            if ($status !== 'all') {
                $countStmt->bind_param('s', $status);
            }
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $totalRecords = $countResult->fetch_assoc()['total'];
            $totalPages = ceil($totalRecords / $itemsPerPage);

            // Render the HTML table with overflow-x-scroll
            echo '<div class="w-full overflow-x-scroll mt-4">';
            echo '<table class="w-full border-collapse border border-black text-left text-gray-700">';
            echo '<thead class="bg-lime-700 text-white">';
            echo '<tr>';
            echo '<th class="p-2 border text-[12px]">Mission Header</th>';
            echo '<th class="p-2 border text-[12px]">Mission Driver</th>';
            echo '<th class="p-2 border text-[12px]">Destination</th>';
            echo '<th class="p-2 border text-[12px]">Date to go</th>';
            echo '<th class="p-2 border text-[12px]">Fuel Requested</th>';

            if (strtoupper($_SESSION['role']) === 'D/CEO' || strtoupper($_SESSION['role']) === 'CEO') {
                echo '<th class="p-2 border text-[12px]">Actions</th>';
            }
            if (strtolower($_SESSION['role']) === 'logistics') {
                echo (isset($_GET['status']) && $_GET['status'] === 'rejected') ? '' : '<th class="p-2 border text-[12px]">Verify</th>';
            }

            echo '</tr></thead><tbody class="bg-white">';

            while ($row = $result->fetch_assoc()) {
                echo '<tr class="border border-b border-black bg-zinc-200 hover:bg-zinc-300">';
                echo '<td class="text-[12px] border pl-2 text-black">' . htmlspecialchars($row['head_mission']) . '</td>';
                echo '<td class="text-[12px] border pl-2 text-black">' . htmlspecialchars($row['driver_name']) . '</td>';
                echo '<td class="text-[12px] border pl-2 text-black">' . htmlspecialchars($row['location_to']) . '</td>';
                echo '<td class="text-[12px] border pl-2 text-black">' . htmlspecialchars($row['date_from']) . '</td>';
                echo '<td class="text-[12px] border pl-2 text-black">' . htmlspecialchars($row['fuel_type']) . '</td>';

                if (strtoupper($_SESSION['role']) === 'D/CEO' || strtoupper($_SESSION['role']) === 'CEO') {
                    echo '<td class="p-1 border">';
                    if ($row['status'] === 'approved') {
                        echo '<span class="text-green-500 font-bold ml-2 cursor-not-allowed">Approved</span>';
                    } else if ($row['status'] === 'rejected') {
                        echo '<span class="font-bold ml-2 text-red-500 cursor-not-allowed">Rejected</span>';
                    } else if ($row['status'] === 'pending') {
                        echo '<a href="approve.php?approve=' . urlencode($row['req_id']) . '" class="text-red-500 hover:underline ml-2">Approve</a>';
                    } else {
                        echo '<a href="requests.php?cancel=' . urlencode($row['req_id']) . '" class="text-red-500 hover:underline">Reject</a>';
                    }

                    if ($row['status'] === 'approved') {
                        echo '<a href="?id=' . urlencode($row['req_id']) . '" class="text-blue-500 hover:underline ml-2">⬇️ PDF</a>';
                    } else {
                        echo '<span class="text-gray-400 ml-2 cursor-not-allowed">⬇️ PDF</span>';
                    }
                    echo '</td>';
                }

                if (strtolower($_SESSION['role']) === 'logistics') {
                    if (isset($_GET['status']) && $_GET['status'] === 'rejected') {
                        echo '';
                    } else {
                        if ($row['verified_by'] == '-') {
                            echo '<td class="p-3 border">
                                    <button onclick="viewRequest(' . $row['req_id'] . ')" class="text-blue-500 hover:underline">
                                        Verify
                                    </button>
                                  </td>';
                        } else {
                            echo '<td class="text-blue-800 p-3 border">Verified</td>';
                        }
                    }
                }

                echo '</tr>';
            }

            echo '</tbody></table> </div>';


            // Pagination controls
            echo '<div class="mt-4">';
            for ($i = 1; $i <= $totalPages; $i++) {
                echo '<a href="?page=' . $i . '&status=' . $status . '" class="px-3 py-1 mx-1 border rounded ' . ($page == $i ? 'bg-lime-700 text-white' : 'bg-white text-lime-700') . '">' . $i . '</a>';
            }
            echo '</div>';
            ?>


            <!-- Modal -->
            <div id="requestModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex justify-center items-center">
                <div class="bg-white p-6 rounded-lg w-1/3">
                    <h2 class="text-xl font-bold text-gray-700">Request Details</h2>
                    <p id="requestDetails" class="mt-4 text-gray-600"></p>
                </div>
            </div>

        </div>
    </div>

    <script>
        // FETCHING VIEW REQUEST OF POPUP
        function viewRequest(id) {
            fetch(`requests.php?req_id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("requestDetails").innerHTML =
                        `<b>Satatus:</b> <span class='text-blue-800 capitalize p-x-2 font-light rounded-md text-center'>${data.status}</span><br>
                        <b>Mission:</b> ${data.head_mission}<br>
                         <b>Driver:</b> ${data.driver_name}<br>
                         <b>Vehicle:</b> ${data.vehicle_type}<br>
                         <b>Plate Number:</b> ${data.plate_number}<br>
                         <b>From:</b> ${data.location_from}<br>
                         <b>Destination:</b> ${data.location_to}<br>
                         <b>Departure:</b> ${data.date_from}<br>
                         <b>Return:</b> ${data.date_to}<br>
                         <b>Requested Fuel:</b> ${data.requested_qty} L <br>
                         <b>Fuel:</b> ${data.fuel_type}<br>
                            <div class="mt-4 flex justify-between"> 
                                <button onclick='verifyRequest(${data.req_id})' ${data.status.toLowerCase() === 'approved'? 'disabled' : ''} class="disable bg-green-500 text-white px-4 py-2 rounded">${data.status.toLowerCase() === 'approved'? 'Approved' : 'Verify'}</button>
                                <button onclick="cancelRequest(${data.req_id})" class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
                                <button onclick="closeModal(${data.req_id})" class="bg-gray-500 text-white px-4 py-2 rounded">X</button>
                            </div>
                         `;
                    document.getElementById("requestModal").classList.remove("hidden");
                });
        }

        function closeModal() {
            document.getElementById("requestModal").classList.add("hidden");
        }

        function verifyRequest(id) {
            fetch(`requests.php?verify=${id}`)
                .then(response => response.json())
                .then(data => alert(data.message))
            closeModal();
        }

        function cancelRequest(id) {
            fetch(`requests.php?reject=${id}`)
                .then(response => response.json())
                .then(data => alert(data.message))
            closeModal();
        }
    </script>
</body>

</html>