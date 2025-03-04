<?php
session_start();
if (!isset($_SESSION["role"]) || strtoupper($_SESSION["role"]) != 'LOGISTICS') {
    header('Location: dashboard.php');
    exit();
}

require 'connection.php';

$errors = [];
if (isset($_POST['quick_request'])) {
    $header = trim($_POST['header']);
    $driver = trim($_POST['driver']);
    $plate = strtoupper(trim($_POST['plate']));
    $fueltype = trim($_POST['fueltype']);
    $fuel_littel = trim($_POST['fuel_littel']);
    $origin = trim($_POST['origin']);
    $destination = trim($_POST['destin']);
    $description = trim($_POST['description']);
    $prepared_by = $_SESSION['name'];

    // Input validation
    if (empty($header) || empty($driver) || empty($plate) || empty($fuel_littel) || empty($origin) || empty($destination) || empty($description)) {
        $errors[] = "All fields are required.";
    } elseif (!is_numeric($fuel_littel) || $fuel_littel <= 0) {
        $errors[] = "Fuel quantity must be a positive number.";
    }

    if (empty($errors)) {

        // CALCULATING PRICE
        $stmt = $db->prepare("SELECT uplt FROM fuel WHERE type = ? AND status = 'active'");
        $stmt->bind_param("s", $fueltype);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the price exists
        if ($row = $result->fetch_assoc()) {
            $realPrice = $row["uplt"] * $fuel_littel;
        } else {
            $realPrice = 0;
        }

        // Close statement
        $stmt->close();

        $sql = "INSERT INTO `quick_action`(`head_mission`, `driver`, `plate_no`, `fuel`, `price`, `origin`, `destination`, `description`, `prepared_by`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $statement = $db->prepare($sql);
        $statement->bind_param("sssidssss", $header, $driver, $plate, $fuel_littel, $realPrice, $origin, $destination, $description, $prepared_by);

        if ($statement->execute()) {
            $success = "Request submitted successfully.";
        } else {
            $errors[] = "Failed to submit request.";
        }
    }
}
?>

<!-- UPDATING FUEL PRICE -->
<?php
$errors = [];
$success = "";

if (isset($_POST['setPrice'])) {
    $fuelType = $_POST['fuelType'] ?? '';
    $price = $_POST['price'] ?? '';
    $discount = $_POST['discount'] ?? '';

    // CALCULATE PRICE DISCOUNT of 40 rwf PER LITER

    if (empty($price) || $price == "") {
        $errors[] = "Please Select fuel type";
    }




    if (empty($fuelType) || empty($price)) {
        $errors[] = "Please select a fuel type and enter a valid price.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $errors[] = "Invalid price entered.";
    } else {
        $price -= $discount;
        try {
            // Prepare SQL statement
            $sql = "UPDATE `fuel` SET `uplt` = ?,  `discount` = ?, `status` = 'active', `date` = NOW() WHERE `type` = ?";
            $statement = $db->prepare($sql);

            if (!$statement) {
                throw new Exception("Failed to prepare the SQL statement.");
            }

            // Bind parameters (corrected types: price is `double`, fuelType is `string`)
            $statement->bind_param("dds", $price, $discount, $fuelType);

            // Execute query
            if ($statement->execute()) {
                $success = "Fuel price updated successfully.";
            } else {
                throw new Exception("Failed to update fuel price.");
            }

            // Close statement
            $statement->close();
        } catch (Exception $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}



/**
 * SENDING OPERATION FUEL
 */

if (isset($_GET['operation'])) {
    if (isset($_POST['operation'])) {
        $fuel = $_POST['fuelType'];
        $litter = $_POST['litter'];
        $car = $_POST['car'];
        $description = $_POST['description'];

        if (empty($fuel) || $fuel == "" || empty($litter) || $litter == "" || empty($description) || $description == "" || empty($car) || $car == "") {
            $errors[] = "Please Fill all fields";
        } else {

            try {
                $status = 'pending';
                $sql = "INSERT INTO operation (`fuel`, `litter`, `car`, `description`, `status`) VALUES (?, ?, ?, ?, ?)";
                $statement = $db->prepare($sql);

                if ($statement) {
                    $statement->bind_param('sssss', $fuel, $litter, $car, $description, $status);
                    if ($statement->execute()) {
                        $success = "Operationg Sent Success full";
                        sleep(3);
                        return header("location:quick_request.php");
                    }
                } else {
                    $errors[] = "Failed to prepare the SQL statement.";
                }
            } catch (PDOException $e) {
                $errors[] = $e->getMessage();
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuel Request</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include("./components/side.php"); ?>
        <div class="flex-1 p-6">
            <!-- Top Bar -->
            <div class="flex justify-between items-center bg-white p-4 rounded shadow-md">
                <h1 class="text-xl font-semibold text-lime-700 flex flex-row items-center gap-2"><i class="fa-solid fa-home"></i> <span class="lg:flex md:flex sm:flex hidden">Quick Actions</span></h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600"> <?php echo "<b>" . $_SESSION["name"] . "</b>"; ?></span>
                    <?php echo (strtoupper($_SESSION['role']) == 'LOGISTICS') ?
                        '<button id="showFormBtn" class="bg-lime-700 text-white py-2 px-4 rounded-lg hover:bg-lime-800 transition" alt="Send Quick"> Quick Act </button>' : ''; ?>
                </div>
            </div>

            <!-- DISPLAYING THE DATA FROM QUICK_ACTION IN CARDS AND ADD BUTTON FOR DOWNLOADING PDF -->
            <?php
            $limit = 6;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Fetch total records for pagination
            $totalQuery = "SELECT COUNT(*) AS total FROM `quick_action` ORDER BY created_at DESC";
            $totalResult = $db->query($totalQuery);
            $totalRow = $totalResult->fetch_assoc();
            $totalPages = ceil($totalRow['total'] / $limit);

            // Fetch paginated records
            $sql = "SELECT * FROM `quick_action` LIMIT $limit OFFSET $offset";
            $result = $db->query($sql);
            ?>
            <div class="flex flex-row gap-3 my-2">
                <a href="./quick_request.php?price" class="block text-white bg-lime-700 px-2 hover:bg-lime-600 w-fit rounded">
                    <span class="text-white text-lg">💲</span>Manage Price
                </a>
                <a href="./quick_request.php?operation" class="block text-white bg-lime-700 px-2 hover:bg-lime-600 w-fit rounded">
                    <span class="text-white text-lg"><span class="hover:animate-spin">⚙️</span></span>Operation
                </a>
            </div>

            <!-- 
            DISPLAYING OPERATION STATUS 
            AND DISABELING OTHER OUTPUTS
            FOR QUICK ACTIONS
            -->
            <?php if (isset($_GET['operation-status'])) {

                $quick = "SELECT * FROM operation";
                $statement = $db->prepare($quick);

                if ($statement):
                    $statement->execute();
                    $result = $statement->get_result();
            ?>

                    <div class="grid lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1 grid-cols-1 gap-4 p-4">
                        <?php while ($operation = $result->fetch_assoc()) : ?>
                            <div class="bg-white shadow-lg rounded-lg p-4 border border-gray-200 flex flex-row items-center justify-between">

                                <div class="w-fit">
                                    <h3 class="text-md font-bold text-lime-700 capitalize">
                                        <?php echo htmlspecialchars($operation['status']); ?>
                                    </h3>
                                    <p class="text-gray-700"><strong>Plate No:</strong> <?php echo htmlspecialchars($operation['car']); ?></p>
                                    <p class="text-gray-700"><strong>Fuel:</strong> <?php echo htmlspecialchars($operation['fuel']); ?> Liters</p>
                                    <p class="text-gray-700"><strong>Description:</strong>
                                        <?php
                                        $descript = htmlspecialchars($operation['description']);
                                        echo (mb_strlen($descript) > 100) ? mb_substr($descript, 0, 50) . '...' : $descript;
                                        ?>
                                    </p>
                                </div>

                                <!-- Download PDF Button -->
                                <!-- <div class="text-center mt-6">
                                    <a href="download.php?pdf_id=<?php echo htmlspecialchars($operation['action_id']); ?>" target="_blank"
                                        class="bg-blue-900 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                                        ⬇️ PDF
                                    </a>
                                </div> -->

                            </div>
                        <?php endwhile; ?>
                    </div>

                <?php
                endif; // End of if statement
            } else {
                ?>



                ?>

                <div class="grid lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1 grid-cols-1 gap-4 p-4">
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <div class="bg-white shadow-lg rounded-lg p-4 border border-gray-200 flex flex-row items-center justify-between">

                            <div class="w-fit">
                                <h3 class="text-md font-bold text-lime-700 capitalize"><?php echo htmlspecialchars($row['head_mission']); ?></h3>
                                <p class="text-gray-700"><strong>Driver:</strong> <?php echo htmlspecialchars($row['driver']); ?></p>
                                <p class="text-gray-700"><strong>Plate No:</strong> <?php echo htmlspecialchars($row['plate_no']); ?></p>
                                <p class="text-gray-700"><strong>Fuel:</strong> <?php echo htmlspecialchars($row['fuel']); ?> Liters</p>
                                <p class="text-gray-700"><strong>Price:</strong> <?php echo htmlspecialchars($row['price']); ?> RWF</p>
                                <p class="text-gray-700"><strong>From:</strong> <?php echo htmlspecialchars($row['origin']); ?></p>
                                <p class="text-gray-700"><strong>To:</strong> <?php echo htmlspecialchars($row['destination']); ?></p>
                                <p class="text-gray-700"><strong>Description:</strong>
                                    <?php
                                    $descript = (htmlspecialchars($row['description']));
                                    echo (mb_strlen($descript > 100)) ? mb_substr($descript, 0, 50) . '...' : $descript;
                                    ?>
                                </p>
                            </div>
                            <!-- Download PDF Button -->
                            <div class="text-center mt-6">
                                <a href="download.php?pdf_id=<?php echo $row['action_id'] ?>" target="_blank" class="bg-blue-900 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                                    ⬇️ PDF
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>

                </div>

                <!-- Pagination Controls -->
                <div class="flex space-x-2 mt-4">
                    <?php if ($page > 1) : ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 bg-gray-700 text-white rounded-md hover:bg-gray-500">⏮️</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <a href="?page=<?php echo $i; ?>" class="px-3 py-1 rounded-md <?php echo $i == $page ? 'bg-lime-700 text-white' : 'bg-gray-200 hover:bg-gray-300'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages) : ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-1 bg-gray-700 text-white rounded-md hover:bg-gray-500">⏭️</a>
                    <?php endif; ?>
                </div>



                <!-- FORM CONTENT AHEAD -->
                <!-- Main Content -->
                <div id="popupForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden p-4">
                    <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 md:p-8 w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl relative max-h-[90vh] overflow-y-auto">
                        <button id="closeFormBtn" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl">&times;</button>
                        <h2 class="text-lg sm:text-xl md:text-2xl font-semibold text-lime-700 text-center mb-4 sm:mb-6">
                            Quick Fuel Request Form
                        </h2>

                        <?php if (!empty($errors)): ?>
                            <div class="bg-red-100 text-red-700 p-2 sm:p-3 rounded mb-4 text-sm sm:text-base">
                                <?php foreach ($errors as $error) {
                                    echo "<p>$error</p>";
                                } ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="bg-green-100 text-green-700 p-2 sm:p-3 rounded mb-4 text-sm sm:text-base">
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="post" class="flex flex-col gap-3 sm:gap-4">
                            <input type="text" name="header" placeholder="Event Header" class="capitalize input-field text-sm sm:text-base">
                            <input type="text" name="driver" placeholder="Driver Name" class="capitalize input-field text-sm sm:text-base">
                            <input type="text" name="plate" placeholder="Plate Number" class="uppercase input-field text-sm sm:text-base">
                            <select name="fueltype" id="" class="input-field text-sm sm:text-base">
                                <option value="" placeholder="Select Options">-- Select Options --</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Petrol">Petrol</option>
                            </select>
                            <input type="number" min=1 name="fuel_littel" placeholder="Fuel Litter" class="input-field text-sm sm:text-base">
                            <input type="text" name="origin" placeholder="From" class="input-field text-sm sm:text-base">
                            <input type="text" name="destin" placeholder="Destination" class="input-field text-sm sm:text-base">
                            <textarea name="description" placeholder="Write Description" class="input-field h-20 sm:h-24 text-sm sm:text-base"></textarea>
                            <button type="submit" name="quick_request" class="bg-lime-700 text-white py-2 rounded-lg hover:bg-lime-800 transition text-sm sm:text-base">
                                Send Request
                            </button>
                        </form>
                    </div>
                </div>

                <!-- POPUP FOR MANAGING FUEL PRICE -->

                <div id="popupPrice" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center 
                <?php echo isset($_GET['price']) ? '' : 'hidden'; ?> p-4">
                    <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 md:p-8 w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl relative max-h-[90vh] overflow-y-auto">
                        <button id="closeFormBt" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl">&times;</button>
                        <h2 class="text-lg sm:text-xl md:text-2xl font-semibold text-lime-700 text-center mb-4 sm:mb-6">
                            Update Fuel prices
                        </h2>

                        <?php if (!empty($errors)): ?>
                            <div class="bg-red-100 text-red-700 p-2 sm:p-3 rounded mb-4 text-sm sm:text-base">
                                <?php foreach ($errors as $error) {
                                    echo "<p>$error</p>";
                                } ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="bg-green-100 text-green-700 p-2 sm:p-3 rounded mb-4 text-sm sm:text-base">
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="post" class="flex flex-col gap-3 sm:gap-4">
                            <select name="fuelType" id="fuelType" class="input-field text-sm sm:text-base">
                                <option value="">-- Select Fuel --</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Petrol">Petrol</option>
                            </select>
                            <input type="number" min=1 name="price" placeholder="Update price" class="input-field text-sm sm:text-base">
                            <input type="number" min=1 name="discount" id="discount" placeholder="Discount" class="input-field text-sm sm:text-base">
                            <button type="submit" name="setPrice" class="bg-lime-700 text-white py-2 rounded-lg hover:bg-lime-800 transition text-sm sm:text-base">
                                Set Price
                            </button>
                        </form>
                    </div>
                </div>

                <!-- MANAGE OPERATION POPUP -->
                <div id="popupOperation" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center 
                <?php echo isset($_GET['operation']) ? '' : 'hidden'; ?> p-4">
                    <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 md:p-8 w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl relative max-h-[90vh] overflow-y-auto">
                        <button id="closeFormBtn" class="hover:text-red-500 hover:font-bold absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl z-9">&times;</button>
                        <h2 class="text-lg sm:text-xl md:text-2xl font-semibold text-lime-700 text-center mb-4 sm:mb-6">
                            Request for Operation fuel
                        </h2>

                        <?php if (!empty($errors)): ?>
                            <div class="bg-red-100 text-red-700 p-2 sm:p-3 rounded mb-4 text-sm sm:text-base">
                                <?php foreach ($errors as $error) {
                                    echo "<p>$error</p>";
                                }
                                sleep(3);
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="bg-green-100 text-green-700 p-2 sm:p-3 rounded mb-4 text-sm sm:text-base">
                                <?php echo $success;
                                sleep(3);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="post" class="flex flex-col gap-3 sm:gap-4">
                            <select name="fuelType" id="fuelType" class="input-field text-sm sm:text-base">
                                <option value="">-- Select Fuel --</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Petrol">Petrol</option>
                            </select>
                            <input type="number" min=1 name="litter" placeholder="Number of Littres" class="input-field text-sm sm:text-base">
                            <select name="car" id="fuelType" class="input-field text-sm sm:text-base">
                                <option value="">-- Select Car --</option>
                                <option value="RDF697S">RDF697S</option>
                                <option value="RDF198P">RDF198P</option>
                            </select>
                            <textarea name="description" class="input-field h-20 sm:h-24 text-sm sm:text-base"></textarea>
                            <button type="submit" name="operation" class="bg-lime-700 text-white py-2 rounded-lg hover:bg-lime-800 transition text-sm sm:text-base">
                                Send Request
                            </button>
                        </form>
                    </div>
                </div>

            <?php } ?>
        </div>
    </div>

    <style>
        .input-field {
            border: 1px solid #d1d5db;
            padding: 10px;
            border-radius: 8px;
            outline: none;
            width: 100%;
            transition: border-color 0.3s;
        }

        .input-field:focus {
            border-color: #84cc16;
        }
    </style>

    <script>
        document.getElementById("showFormBtn").addEventListener("click", function() {
            document.getElementById("popupForm").classList.remove("hidden");
        });

        document.getElementById("closeFormBtn").addEventListener("click", function() {
            document.getElementById("popupForm").classList.add("hidden");
        });

        // Close popup when clicking outside the form
        document.getElementById("popupForm").addEventListener("click", function(event) {
            if (event.target === this) {
                this.classList.add("hidden");
            }
        });

        document.getElementById('closeFormBt').addEventListener('click', function() {
            document.getElementById('popupPrice').classList.add('hidden');

        });

        document.getElementById('closeFormBtn').addEventListener("click", () => {
            alert("Clicked clox")
        })

        // UPDATING PRICES WITHIN FORM

        document.getElementById('fuelType').addEventListener('change', function() {
            let fuelType = this.value;

            if (fuelType) {
                fetch('get_discount.php?fuelType=' + fuelType)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('discount').value = data.discount || 0;
                        document.getElementById('discount').disabled = false;
                    })
                    .catch(error => console.error('Error fetching discount:', error));
            } else {
                document.getElementById('discount').value = '';
                document.getElementById('discount').disabled = true;
            }
        });
    </script>
</body>

</html>