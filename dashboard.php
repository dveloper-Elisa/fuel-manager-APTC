<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);


session_start();

include("./connection.php");


if (!isset($_SESSION["phone"]) || !isset($_SESSION["name"])  || !isset($_SESSION["staff_code"])) {
    header("Location:./login.php");
}

$id =  $_SESSION['staff_code'];
$role = strtoupper($_SESSION["role"]);
$staff_code = $_SESSION["staff_code"];

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuel Request Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">

        <?php
        include("./components/side.php");

        ?>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <!-- Top Bar -->
            <div class="flex justify-between items-center bg-white p-4 rounded shadow-md">
                <h1 class="text-xl font-semibold text-lime-700 flex flex-row items-center gap-2"><i class="fa-solid fa-home"></i> <span class="lg:flex md:flex sm:flex hidden">Dashboard</span></h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600"><?php echo "<b>" . $_SESSION["name"] . "</b>" ?></span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-3 gap-6 my-6">
                <div class="bg-white p-6 rounded shadow-md text-center">
                    <h3 class="text-gray-500">Total Requests</h3>

                    <!-- CHECK THE STAFF ROLE -->
                    <?php
                    $sql = '';
                    if ($role === "D/CEO" || $role === "CEO") {
                        $sql = "SELECT COUNT(*) AS totalRequest FROM fuel_request WHERE verified_by !='-'";
                    } elseif ($role === 'LOGISTICS') {
                        $sql = "SELECT COUNT(*) AS totalRequest FROM fuel_request ";
                    } else {
                        $sql = "SELECT COUNT(*) AS totalRequest FROM fuel_request WHERE stf_code = '$id'";
                    }
                    $result = mysqli_query($db, $sql);
                    $row = mysqli_fetch_array($result);
                    ?>

                    <p class="text-2xl font-bold text-lime-700"> <?php echo $row['totalRequest'] ?></p>
                </div>
                <div class="bg-white p-6 rounded shadow-md text-center">
                    <h3 class="text-gray-500"><?php echo ($role === 'LOGISTICS') ? 'Verifiyed' : 'Approved' ?> Requests</h3>
                    <p class="text-2xl font-bold text-lime-700">

                        <?php
                        $status = 'approved';
                        $sql1 = '';
                        if ($role === "D/CEO" || $role === "CEO") {
                            $sql1 = "SELECT COUNT(*) AS approved FROM fuel_request WHERE verified_by !='-' AND status = '$status'";
                        } elseif ($role === 'LOGISTICS') {
                            $sql1 = "SELECT COUNT(*) AS approved FROM fuel_request WHERE verified_by !='-'";
                        } else {
                            $sql1 = "SELECT COUNT(*) AS approved FROM fuel_request WHERE status = '$status' AND stf_code = '$id'";
                        }
                        $approved = mysqli_query($db, $sql1);
                        $Arows = mysqli_fetch_array($approved);
                        echo $Arows['approved'];
                        ?>

                    </p>
                </div>
                <div class="bg-white p-6 rounded shadow-md text-center">
                    <h3 class="text-gray-500">Pending Requests</h3>
                    <p class="text-2xl font-bold text-lime-700">
                        <?php
                        $status = 'pending';
                        $sql2 = '';
                        if ($role === "D/CEO" || $role === "CEO") {
                            $sql2 = "SELECT COUNT(*) AS pendings FROM fuel_request WHERE verified_by !='-' AND status = '$status'";
                        } elseif ($role === 'LOGISTICS') {
                            $sql2 = "SELECT COUNT(*) AS pendings FROM fuel_request WHERE verified_by ='-' AND status != 'rejected'";
                        } else {
                            $sql2 = "SELECT COUNT(*) AS pendings FROM fuel_request WHERE status = '$status' AND stf_code = '$id'";
                        }
                        $pending = mysqli_query($db, $sql2);
                        $Prows = mysqli_fetch_array($pending);
                        echo $Prows['pendings'];
                        ?>
                    </p>
                </div>

            </div>

            <!-- Recent Requests Table -->
            <div class="bg-white p-6 rounded shadow-md">
                <h2 class="text-lg font-semibold text-lime-700 mb-4">Recent Fuel Requests</h2>
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-lime-700 text-white">
                            <th class="p-2">ID</th>
                            <th class="p-2">Requester</th>
                            <th class="p-2">Amount (L)</th>
                            <th class="p-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $requests = '';
                        if ($role === "D/CEO" || $role === "CEO") {
                            $requests = "SELECT * FROM fuel_request WHERE verified_by != '-' ORDER BY created_at DESC LIMIT 3";
                        } elseif ($role === 'LOGISTICS') {
                            $requests = "SELECT * FROM fuel_request WHERE status != 'approved' ORDER BY created_at ASC LIMIT 5 ";
                        } else {
                            $requests = "SELECT * FROM fuel_request WHERE stf_code = '$id' ORDER BY created_at ASC LIMIT 3 ";
                        }
                        $result = mysqli_query($db, $requests);
                        $i = 0;
                        while ($row = mysqli_fetch_array($result)) {
                            $i++;
                            echo "
                        <tr class='border-b border-gray-200'>
                            <td class='p-2 text-center'>" . $i . "</td>
                            <td class='p-2 text-center'>" . $row['head_mission'] . "</td>
                            <td class='p-2 text-center'>" . $row['requested_qty'] . "</td>
                            <td class='p-2 text-center text-lime-700'>" . $row['status'] . "</td>
                        </tr>
                        ";
                        }

                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>