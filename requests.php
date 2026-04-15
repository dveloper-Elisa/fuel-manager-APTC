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
    $fuelDocument->Ln(40);
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
            $fuelDocument->Cell(95, 10, 'Estimated Fuel: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['requested_qty'] . ' Liters', 0, 'L');

            $fuelDocument->SetFont('Times', '', 10);
            $fuelDocument->Cell(95, 10, 'Received Fuel: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['received_qty'] . ' Liters', 0, 'L');

            $fuelDocument->SetFont('Times', '', 10);
            $fuelDocument->Cell(95, 10, 'Price of Fuel: ', 0, 0, 'L');
            $fuelDocument->SetFont('Times', 'B', 10);
            $fuelDocument->MultiCell(0, 10, $row['price'] . ' RWF', 0, 'L');

            // Final Section
            $fuelDocument->Ln(20);
            $fuelDocument->Cell(95, 10, 'Prepared by: ', 0, 0, 'L');
            $fuelDocument->Cell(95, 10, 'Approved by: ', 0, 1, 'L');

            $fuelDocument->Cell(95, 10, $row['verified_by'], 0, 0, 'L');
            $fuelDocument->Cell(95, 10, $row['approved_by'], 0, 1, 'L');

            $fuelDocument->Cell(95, 10, 'H/LOGISTIC REG', 0, 0, 'L');
            $fuelDocument->Cell(95, 10, 'D/CEO & DAF REG', 0, 1, 'L');
        } else {
            echo 'No Request found';
        }
    }

    // Output the PDF as a download
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="Fuel Report.pdf"');
    $fuelDocument->Output();
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

        // return;
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

<body class="bg-gray-100 w-[100%]">
    <div class="flex h-screen">
        <?php include "./components/side.php"; ?>

        <div class="flex-1 p-6">
            <!-- Top Bar -->
            <div class="flex justify-between items-center bg-white p-4 rounded shadow-md">
                <h1 class="text-xl font-semibold text-lime-700 flex flex-row items-center gap-2"><i class="fa-solid fa-home"></i> <span class="lg:flex md:flex sm:flex hidden">Requests</span></h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600"> <?php echo "<b>" . $_SESSION["name"] . "</b>"; ?></span>
                    <?php echo (strtoupper($_SESSION['role']) == 'D/CEO' || strtoupper($_SESSION['role']) == 'CEO') ?
                        "" : '<a href="./index.php" class="bg-lime-700 text-white p-1 rounded-md" alt="Send Request"> New Request </a>'; ?>
                </div>
            </div>

            <!-- Status Filter Links -->
            <div class="mt-4 flex space-x-4">
                <a href="requests.php?status=approved" class="<?php echo (isset($_GET['status']) && $_GET['status'] === 'approved') ? 'border-t-4 border-zinc-900 px-2 py-1 bg-lime-700 text-white rounded-md' : 'px-2 py-1 bg-lime-700 text-white rounded-md'; ?>"> <?php echo strtolower($_SESSION['role']) === "logistics" ? "✅ Verifyed" : "✅ Approved"  ?></a>
                <a href="requests.php?status=pending" class="<?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'border-t-4 border-zinc-900 px-2 py-1 bg-yellow-600 text-white rounded-md' : 'px-2 py-1 bg-yellow-600 text-white rounded-md' ?>">⏳ Pending</a>
                <a href="requests.php?status=rejected" class="<?php echo (isset($_GET['status']) && $_GET['status'] === 'rejected') ? 'border-t-4 border-zinc-900 px-2 py-1 bg-red-500 text-white rounded-md' : 'px-2 py-1 bg-red-700 text-white rounded-md' ?>">🚫 Rejected</a>
            </div>
            <?php
            // Fetch status from URL
            $status = isset($_GET['status']) ? $_GET['status'] : 'all';
            $id = $_SESSION['staff_code'];
            $role = strtolower($_SESSION['role']);

            // Pagination settings
            $itemsPerPage = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $itemsPerPage;

            // Default value for verified_by
            $verify = '-';

            // Prepare SQL query based on role and status
            $query = '';

            if ($role == 'd/ceo' || $role == 'ceo') {
                // CEO or D/CEO
                if ($status === 'all') {
                    $query = "SELECT * FROM fuel_request WHERE verified_by != ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
                } else {
                    $query = "SELECT * FROM fuel_request WHERE verified_by != ? AND status = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
                }
            } elseif ($role == 'logistics') {
                // Logistics
                if ($status === 'all') {
                    $query = "SELECT * FROM fuel_request ORDER BY created_at DESC LIMIT ? OFFSET ?";
                } else {
                    $query = "SELECT * FROM fuel_request WHERE status = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
                }
            } else {
                // Other roles (fetch based on stf_code)
                if ($status === 'all') {
                    $query = "SELECT * FROM fuel_request WHERE stf_code = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
                } else {
                    $query = "SELECT * FROM fuel_request WHERE stf_code = ? AND status = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
                }
            }

            // Prepare and execute the query
            if ($stmt = $db->prepare($query)) {
                if ($role == 'd/ceo' || $role == 'ceo') {
                    if ($status === 'all') {
                        $stmt->bind_param('sii', $verify, $itemsPerPage, $offset);
                    } else {
                        $stmt->bind_param('ssii', $verify, $status, $itemsPerPage, $offset);
                    }
                } elseif ($role == 'logistics') {
                    if ($status === 'all') {
                        $stmt->bind_param('ii', $itemsPerPage, $offset);
                    } else {
                        $stmt->bind_param('sii', $status, $itemsPerPage, $offset);
                    }
                } else {
                    if ($status === 'all') {
                        $stmt->bind_param('iii', $id, $itemsPerPage, $offset);
                    } else {
                        $stmt->bind_param('isii', $id, $status, $itemsPerPage, $offset);
                    }
                }

                $stmt->execute();
                $result = $stmt->get_result();
            }

            /**
             * Fetch total number of records
             **/
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
            echo '<div id="printableTable" class="w-[100%] overflow-x-auto mt-4 border border-gray-300">';
            ?>
            <button onclick="printPdf()">Print Report</button>
            <?php
            echo '<table class="min-w-max w-full border-collapse border border-gray-400 text-left text-gray-700">';
            echo '<thead class="bg-lime-700 text-white">';
            echo '<tr>';
            echo '<th class="p-2 border text-[12px]">Mission Header</th>';
            echo '<th class="p-2 border text-[12px]">Mission Driver</th>';
            echo '<th class="p-2 border text-[12px]">From</th>';
            echo '<th class="p-2 border text-[12px]">Destination</th>';
            echo '<th class="p-2 border text-[12px]">Date to go</th>';
            echo '<th class="p-2 border text-[12px]">Fuel Requested</th>';
            echo '<th class="p-2 border text-[12px]" colspan="2">Status</th>';

            if (strtoupper($_SESSION['role']) === 'D/CEO' || strtoupper($_SESSION['role']) === 'CEO') {
                echo '<th class="p-2 border text-[12px] no-print">Actions</th>';
            }
            if (strtolower($_SESSION['role']) === 'logistics') {
                echo (isset($_GET['status']) && $_GET['status'] === 'rejected')
                    ? '<th class="p-2 border text-[12px] no-print">Status</th>'
                    : '<th class="p-2 border text-[12px] no-print">Verify</th>';
            }

            echo '</tr></thead><tbody class="bg-white">';
            $i = 1;
            while ($row = $result->fetch_assoc()) {
                $strip = ($i % 2 == 0) ? 'bg-gray-100' : ' bg-gray-200';
                echo '<tr class="border border-b border-black ' . $strip . ' hover:bg-zinc-300">';
                echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['head_mission']) . '</td>';
                echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['driver_name']) . '</td>';
                echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['location_from']) . '</td>';
                echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['location_to']) . '</td>';
                echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['date_from']) . '</td>';
                echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['fuel_type']) . '</td>';

                if (strtoupper($_SESSION['role']) === 'D/CEO' || strtoupper($_SESSION['role']) === 'CEO') {
                    echo '<td class="p-1 border">';
                    if ($row['status'] === 'approved') {
                        echo '<span class="text-green-500 font-bold ml-2 cursor-not-allowed">Approved</span>';
                    } else if ($row['status'] === 'rejected') {
                        echo '<span class="font-bold ml-2 text-red-500 cursor-not-allowed">Rejected</span>';
                    } else if ($row['status'] === 'pending') {
                        echo '<button onclick="viewRequest(' . $row['req_id'] . ')" class="text-red-500 hover:underline ml-2">Approve</button>';
                    } else {
                        echo '<a href="requests.php?cancel=' . urlencode($row['req_id']) . '" class="text-red-500 hover:underline">Reject</a>';
                    }

                    if ($row['status'] === 'approved') {
                        echo '<a target="_blank" href="?id=' . urlencode($row['req_id']) . '" class="text-blue-500 hover:underline ml-2 flex flex-row gap-2 items-center"><span class="material-icons text-[20px text-blue-500" title="Download Pdf">picture_as_pdf</span></a>';
                    } else {
                        echo '<span class="text-gray-400 ml-2 cursor-not-allowed">⬇️ PDF</span>';
                    }
                    echo '</td>';
                }

                // if (strtolower($_SESSION['role']) === 'logistics' ) {
                    if (isset($_GET['status']) && $_GET['status'] === 'rejected') {
                        echo '<td class="p-3 border text-red-500 hover:underline">
                                        Rejected
                                  </td>';
                    } else {
                        if ($row['verified_by'] == '-' && $row['status'] == "pending") {
                            echo '<td class="p-3 border">
                                    <button onclick="viewRequest(' . $row['req_id'] . ')" class="text-blue-500 hover:underline">
                                        Verify
                                    </button>
                                  </td>';
                        } elseif ($row['status'] == "rejected") {
                            echo '<td class="text-red-600 p-3 border">Rejected</td>';
                        } elseif ($row['status'] == "approved") {
                            echo '<td class="text-green-500 font-bold p-3 border">Approved 
                            <a target="_blank" href="?id=' . urlencode($row['req_id']) . '" class="text-blue-500 hover:underline ml-2 flex flex-row gap-2 items-center"><span class="material-icons text-[20px text-blue-500" title="Download Pdf">picture_as_pdf</span></a>
                            </td>';
                            echo '<td class="text-green-500 font-bold p-3 border">Approved 
                            <a target="_blank" href="?id=' . urlencode($row['req_id']) . '" class="text-blue-500 hover:underline ml-2 flex flex-row gap-2 items-center"><span class="material-icons text-[20px text-blue-500" title="Download Pdf">picture_as_pdf</span></a>
                            </td>';
                        } else {
                            echo '<td class="text-blue-800 p-3 border">Verified</td>';
                        }
                    }
                // }

                echo '</tr>';
                $i++;
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

            <!-- Satatus Modal -->
            <div id="statusModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex justify-center items-center">
                <div class="bg-white p-6 rounded-lg w-1/3">
                    <h2 class="text-center text-5 font-bold text-gray-700">✅</h2>
                    <p id="status" class="text-center text-10 capitalize mt-4 text-gray-600"></p>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Creating print pdf
        function printPdf() {
            var originalTable = document.querySelector("#printableTable table"); // Find the table inside the div

            // Clone the table so we don't change the original
            var clonedTable = originalTable.cloneNode(true);

            // Find the "Verify" or "Actions" column index
            var headerCells = clonedTable.querySelectorAll("thead th");
            let columnIndexToRemove = -1;

            headerCells.forEach(function(th, index) {
                if (th.innerText.trim().toLowerCase() === "verify" || th.innerText.trim().toLowerCase() === "actions") {
                    columnIndexToRemove = index;
                }
            });

            if (columnIndexToRemove !== -1) {
                // Remove header cell
                headerCells[columnIndexToRemove].remove();

                // Remove each corresponding cell in tbody
                var rows = clonedTable.querySelectorAll("tbody tr");
                rows.forEach(function(row) {
                    var cells = row.querySelectorAll("td");
                    if (cells[columnIndexToRemove]) {
                        cells[columnIndexToRemove].remove();
                    }
                });
            }

            // Now open a new page and print the cleaned table
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Fuel Request Report</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { padding: 20px; font-family: Arial, sans-serif; }');
            printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-top: 20px; }');
            printWindow.document.write('th, td { border: 1px solid black; padding: 8px; text-align: left; font-size: 14px; }');
            printWindow.document.write('thead { background-color: #4d7c0f; color: white; }');
            printWindow.document.write('tr:nth-child(even) { background-color: #f2f2f2; }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h2>Fuel Request Report</h2>');
            printWindow.document.write(clonedTable.outerHTML);
            printWindow.document.write('</body></html>');

            printWindow.document.close();
        }
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
                         <b>Distance:</b> ${data.kilometer} km<br>
                         <b>Departure:</b> ${data.date_from}<br>
                         <b>Return:</b> ${data.date_to}<br>
                         <b>Fuel:</b> ${data.fuel_type}<br>
                         <b>Estimated Fuel:</b> ${data.requested_qty} L <br>
                         <b>Fuel Price:</b> ${data.price} RWF<br>
                         <div class="mt-4 flex justify-between"> 
                                <a href='./approve.php?approve=${data.req_id}' ${data.status.toLowerCase() === 'approved'? 'disabled' : ''} class="disable bg-green-500 text-white px-4 py-2 rounded">${data.status.toLowerCase() === 'approved' ? 'Approved' : 'Approve'}</a>
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

        /**
         * function for verifying request is removed and placed to approve.php and
         * they it differ according to their role LOGISTICS, CEO or D/CEO
         */

        function cancelRequest(id) {
            fetch(`requests.php?reject=${id}`)
                .then(response => response.json())
                .then(data => {
                    alert(data.message)
                    window.location = "requests.php"
                })
            closeModal();
        }
    </script>
</body>

</html>