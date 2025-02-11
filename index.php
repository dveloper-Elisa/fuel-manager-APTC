<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

if (!isset($_SESSION["phone"])) {
    header("Location:./login.php");
}

include("./connection.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuel Request Management System</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="./fuel.css">
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php
        include("./components/side.php");
        ?>

        <!-- <a href="./requests.php" target="_blank" rel="noopener noreferrer">View Requests</a> -->
        <form method="post" enctype="multipart/form-data" class="overflow-y-scroll">
            <h2>Fuel Request Management System</h2>
            <div>
                <p id="response" class="text-red-500"></p>
            </div>
            <input type="text" placeholder="Driver Name" name="Dnames" id="driverName" required>
            <input type="text" placeholder="Vehicle Type" name="Vtype" id="vehicleType" required>
            <input type="text" placeholder="Plate Number" name="Pnumber" id="plateNumber" required>
            <input type="text" placeholder="From" name="from" id="from" required>
            <input type="text" placeholder="Destination" name="Destination" id="destin" required>
            <label for="departure">Date of Departure</label>
            <input type="date" name="departure" id="departure" required>
            <label for="return">Date of Return</label>
            <input type="date" name="return" id="return" required>
            <label for="select">Fuel Type</label>
            <select name="fuel" id="select" class="input-field text-sm sm:text-base" required>
                <option value="" placeholder="Select Options">Select Options</option>
                <option value="Diesel">Diesel</option>
                <option value="Petrol">Petrol</option>
            </select>
            <label for="quantinty">Quantity in Liters</label>
            <input type="number" placeholder="Fuel Quantity" name="quantinty" id="quantinty" required>
            <label for="signature">Head of Mission's Signature</label>
            <input type="file" name="signature" id="signature" accept=".jpg, .jpeg, .png">
            <input type="submit" value="Submit" name="btn">
        </form>

        <?php
        if (isset($_POST["btn"])) {
            // Sanitize and escape inputs
            $Hnames = $_SESSION["name"];
            $Dnames = mysqli_real_escape_string($db, $_POST["Dnames"]);
            $Vtype = mysqli_real_escape_string($db, $_POST["Vtype"]);
            $Pnumber = mysqli_real_escape_string($db, $_POST["Pnumber"]);
            $from = mysqli_real_escape_string($db, $_POST["from"]);
            $Destination = mysqli_real_escape_string($db, $_POST["Destination"]);
            $departure = mysqli_real_escape_string($db, $_POST["departure"]);
            $return = mysqli_real_escape_string($db, $_POST["return"]);
            $fuelType = mysqli_real_escape_string($db, $_POST["fuel"]);
            $quantinty = mysqli_real_escape_string($db, $_POST["quantinty"]);

            // Handle file upload
            if (isset($_FILES["signature"]) && $_FILES["signature"]["error"] === 0) {
                $uploadDir = "uploads/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = $_FILES["signature"]["name"];
                $fileTmpName = $_FILES["signature"]["tmp_name"];
                $fileSize = $_FILES["signature"]["size"];
                $fileError = $_FILES["signature"]["error"];

                $allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
                $fileType = mime_content_type($fileTmpName);

                if (in_array($fileType, $allowedTypes)) {
                    if ($fileSize <= 2 * 1024 * 1024) { // Maximum file size: 2MB
                        $uniqueName = uniqid("signature_", true) . "." . pathinfo($fileName, PATHINFO_EXTENSION);
                        $fileDestination = $uploadDir . $uniqueName;

                        $staff_code = $_SESSION['staff_code'];

                        // GETING THE PRICE OF FUELS FROM DATABASE
                        $sqlPrice = "SELECT uplt FROM fuel WHERE type = ? AND status = 'active'";
                        $resultPrice = $db->prepare($sqlPrice);
                        $resultPrice->bind_param("s", $fuelType);
                        $resultPrice->execute();
                        $result = $resultPrice->get_result();
                        if ($row = $result->fetch_assoc()) {
                            $realPrice = $row["uplt"] * $quantinty;

                            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                                // Insert data into the database
                                $sql = "INSERT INTO fuel_request (stf_code, requested_date, location_from, location_to, date_from, date_to, head_mission, vehicle_type, requested_qty, received_qty, price, driver_name, fuel_type, plate_number, verified_by, approved_by, `signature`, `status`, created_at ) 
                    VALUES ('$staff_code',now(), '$from', '$Destination','$departure', '$return', '$Hnames', '$Vtype', '$quantinty', 0, $realPrice, '$Dnames', '$fuelType', '$Pnumber', '-', '-', '$fileDestination', 'pending', now())";

                                if (mysqli_query($db, $sql)) {
        ?>
                                    <!-- SENDING ALTER OF SUCCESS REQUEST -->
                                    <script>
                                        alert("request Sent sucessfull")
                                        window.location = "./requests.php";
                                    </script>
                                <?php
                                } else {
                                ?>
                                    <script>
                                        document.getElementById('response').innerText = 'Error'
                                        <?php echo mysqli_error($db); ?>
                                    </script>
                                <?php
                                }
                            } else {
                                ?>
                                <script>
                                    document.getElementById('response').innerText = "Error uploading Signature.";
                                </script>
                            <?php
                            }
                        } else {
                            ?>
                            <script>
                                document.getElementById('response').innerText = "File size exceeds the maximum limit of 2MB.";
                            </script>
                        <?php
                        }
                    } else {
                        ?>
                        <script>
                            document.getElementById('response').innerText = "Invalid file type. Please upload a JPEG or PNG image.";
                        </script>
                    <?php
                    }
                } else {
                    ?>
                    <script>
                        document.getElementById('response').innerText = "File not uploaded or there was an error.";
                    </script>
        <?php
                }
            }
        }
        ?>
    </div>


    <style>
        #select {
            width: 100%;
            padding: 12px;
            margin: 6px 0;
            border: 1px solid #2b2929;
            border-radius: 5px;
            font-size: 14px;
            background-color: #fff;
            color: #333;
            cursor: pointer;
            outline: none;
            transition: all 0.3s ease-in-out;
        }

        /* Add hover effect */
        #select:hover {
            border-color: #1e1e1e;
        }

        /* Add focus effect */
        #select:focus {
            border-color: #4caf50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        /* Style dropdown arrow */
        #select::-ms-expand {
            display: none;
        }

        #select {
            appearance: none;
            /* Remove default styling */
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%232b2929' d='M2 0L0 2h4z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 8px;
        }
    </style>
</body>

</html>