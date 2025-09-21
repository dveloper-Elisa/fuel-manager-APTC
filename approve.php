<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

if ((!isset($_SESSION["phone"]) || !isset($_SESSION["name"])) && (strtoupper($_SESSION['role']) !== 'D/CEO' || strtoupper($_SESSION['role']) !== 'CEO' || strtoupper($_SESSION['role']) !== 'LOGISTICS')) {
    header("Location: login.php");
}

include("./connection.php");

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (isset($_GET["approve"])) {

    // HANDLE FORM SUBMISSION FOR APPROVAL
    if (strtoupper($_SESSION['role']) == 'D/CEO' || strtoupper($_SESSION['role']) == 'CEO') {
        if (isset($_POST['approve'])) {
            $granted_quantinty = $_POST['received'];

            $id = $_GET["approve"];
            $approved_by = $_SESSION['name'];

            $FuuelType = "SELECT * FROM  fuel_request WHERE req_id = ?";
            $state = $db->prepare($FuuelType);
            $state->bind_param("i", $id);
            $state->execute();
            $resqult = $state->get_result();
            if ($row = $resqult->fetch_assoc()) {
                $fuelType = $row["fuel_type"];

                // GETING PRICE OF FUEL
                $stmt = $db->prepare("SELECT uplt FROM fuel WHERE type = ? AND status = 'active'");
                $stmt->bind_param("s", $fuelType);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if the price exists
                if ($row = $result->fetch_assoc()) {
                    $realPrice = $row["uplt"] * $granted_quantinty;
                }

                $approve = mysqli_query($db, "UPDATE fuel_request SET received_qty = '$granted_quantinty', price = '$realPrice', approved_by = '$approved_by', status = 'approved' WHERE req_id = '$id'");

                // Close statement
                $stmt->close();

                if ($approve) {
                    // INCLUDING FILE FOR SENDING SMS TO THE PHONES
                    include "./messages/sendSms.php";
?>
                    <script>
                        alert("Request Approved Successfully")
                        window.location = "./requests.php"
                    </script>
                <?php
                }
            }
        }
    }
    $realPrice = 0;
    if (strtoupper($_SESSION['role']) == 'LOGISTICS') {
        if (isset($_POST['verify'])) {
            $granted_quantinty = $_POST['received'];

            $id = $_GET["approve"];
            $verified_by = $_SESSION['name'];

            // GETING FUEL TYPE FROM fuel_request
            $FuuelType = "SELECT * FROM  fuel_request WHERE req_id = ?";
            $state = $db->prepare($FuuelType);
            $state->bind_param("i", $id);
            $state->execute();
            $resqult = $state->get_result();
            if ($row = $resqult->fetch_assoc()) {
                $fuelType = $row["fuel_type"];

                // GETING PRICE OF FUEL
                $stmt = $db->prepare("SELECT uplt FROM fuel WHERE type = ? AND status = 'active'");
                $stmt->bind_param("s", $fuelType);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if the price exists
                if ($row = $result->fetch_assoc()) {
                    $realPrice = (int)$row["uplt"] * (int)$granted_quantinty;
                }

                $approve = mysqli_query($db, "UPDATE fuel_request SET received_qty = '$granted_quantinty', price = '$realPrice', verified_by = '$verified_by' WHERE req_id = '$id'");

                // Close statement
                $stmt->close();
                if ($approve) {

                    // INCLUDING FILE FOR SENDING SMS TO THE PHONES
                    include "./messages/sendSms.php";
                ?>
                    <script>
                        setTimeout(() => {
                            document.getElementById('verify').innerHTML = 'Request verified Successfully'
                        }, 2000)
                        window.location = "./requests.php"
                    </script>
    <?php
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
        <title>Approve request</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <!-- <body class="bg-gray-100 flex items-center justify-center min-h-screen"> -->

    <body class="bg-gray-100">
        <div class="flex h-screen">
            <!-- including side bar -->
            <?php
            include "./components/side.php"

            ?>

            <!-- Main Content -->
            <div class="flex-1 p-6">
                <!-- Top Bar -->
                <div class="flex justify-between items-center bg-white p-4 rounded shadow-md">
                    <h1 class="text-xl font-semibold text-lime-700 flex flex-row items-center gap-2"><i class="fa-solid fa-home"></i> <span class="lg:flex md:flex sm:flex hidden">Approve</span></h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600"> <?php echo "<b>" . $_SESSION["name"] . "</b>" ?></span>
                    </div>
                </div>


                <form action="" method="post" class="bg-white p-6 rounded-lg shadow-md w-full max-w-md mt-5">
                    <?php
                    $id = $_GET["approve"];
                    $sql = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM fuel_request WHERE req_id = '$id' "));
                    ?>
                    <div class="text-blue-500 p-2" id="verify"></div>

                    <label for="requested" class="block text-gray-700 font-semibold">Estimated Fuel (L)</label>
                    <input type="number" name="" id="requested" value=<?php echo $sql['requested_qty'] ?> disabled class="w-full p-2 border border-black rounded mb-4">
                    <?php
                    if (strtoupper($_SESSION['role']) === 'D/CEO' || strtoupper($_SESSION['role'] === 'CEO')) {
                    ?>
                        <label for="received" class="block text-gray-700 font-semibold">Granted by Logistic</label>
                        <input type="number" value=<?php echo $sql['received_qty'] ?> id="received" disabled class="w-full p-2 border border-black rounded mb-4">

                    <?php
                    }
                    ?>
                    <label for="received" class="block text-gray-700 font-semibold">Granted Quantity (L)</label>
                    <input type="number" name="received" id="received" placeholder="Quantity in Liters (L)" required class="w-full p-2 border border-black rounded mb-4">

                    <?php echo (strtoupper($_SESSION['role']) == 'LOGISTICS') ? '<button type="submit" name="verify" class="w-full bg-lime-700 text-white p-2 rounded hover:bg-lime-800">Verify</button>' : (strtoupper($_SESSION['role']) == 'D/CEO' || strtoupper($_SESSION['role']) == 'CEO' ? '<button type="submit" name="approve" class="w-full bg-lime-700 text-white p-2 rounded hover:bg-lime-800">Approve</button>' : '') ?>
                </form>

            <?php

        } else {
            header("Location: ./requests.php");
        }

            ?>
            </div>
        </div>
    </body>

    </html>