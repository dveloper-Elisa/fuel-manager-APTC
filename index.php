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
    <link rel="stylesheet" href="fuel.css">
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
                <p id="response" class="text-red-600"></p>
            </div>
            <input type="text" placeholder="Head of mission" name="Hnames" id="headName" required>
            <input type="text" placeholder="Driver Name" name="Dnames" id="driverName" required>
            <input type="text" placeholder="Vehicle Type" name="Vtype" id="vehicleType" required>
            <input type="text" placeholder="Plate Number" name="Pnumber" id="plateNumber" required>
            <input type="text" placeholder="From" name="from" id="from" required>
            <input type="text" placeholder="Destination" name="Destination" id="destin" required>
            <label for="departure">Date of Departure</label>
            <input type="date" name="departure" id="departure" required>
            <label for="return">Date of Return</label>
            <input type="date" name="return" id="return" required>
            <input type="text" placeholder="Type of Fuel" name="fuel" id="fuel" required>
            <label for="quantinty">Quantity in Liters</label>
            <input type="number" placeholder="Fuel Quantity" name="quantinty" id="quantinty" required>
            <label for="signature">Head of Mission's Signature</label>
            <input type="file" name="signature" id="signature" accept=".jpg, .jpeg, .png">
            <input type="submit" value="Submit" name="btn">
        </form>

        <?php
        if (isset($_POST["btn"])) {
            // Sanitize and escape inputs
            $Hnames = mysqli_real_escape_string($db, $_POST["Hnames"]);
            $Dnames = mysqli_real_escape_string($db, $_POST["Dnames"]);
            $Vtype = mysqli_real_escape_string($db, $_POST["Vtype"]);
            $Pnumber = mysqli_real_escape_string($db, $_POST["Pnumber"]);
            $from = mysqli_real_escape_string($db, $_POST["from"]);
            $Destination = mysqli_real_escape_string($db, $_POST["Destination"]);
            $departure = mysqli_real_escape_string($db, $_POST["departure"]);
            $return = mysqli_real_escape_string($db, $_POST["return"]);
            $fuel = mysqli_real_escape_string($db, $_POST["fuel"]);
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

                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            // Insert data into the database
                            $sql = "INSERT INTO fuel_request (stf_code, requested_date, location_from, location_to, date_from, date_to, head_mission, vehicle_type, requested_qty, received_qty, driver_name, fuel_type, plate_number, verified_by, approved_by, `signature`, `status`, created_at ) 
                    VALUES ('$staff_code',now(), '$from', '$Destination','$departure', '$return', '$Hnames', '$Vtype', '$quantinty', 0, '$Dnames', '$fuel', '$Pnumber', '-', '-', '$fileDestination', 'pending', now())";

                            if (mysqli_query($db, $sql)) {
        ?>
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
        ?>
    </div>
</body>

</html>