<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("location:login.php");
}

if (strtoupper(($_SESSION['role']) != "D/CEO" || strtoupper($_SESSION['role']) != "CEO") && strtoupper($_SESSION['role']) != "LOGISTICS") {
    header("location:dashboard.php");
}


$role = strtoupper($_SESSION['role']);
include "./connection.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operation report</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include("./components/side.php"); ?>
        <div class="flex-1 p-6">
            <!-- Top Bar -->
            <div class="flex justify-between items-center bg-white p-4 rounded shadow-md">
                <h1 class="text-xl font-semibold text-lime-700 flex flex-row items-center gap-2"><i class="fa-solid fa-home"></i> <span class="lg:flex md:flex sm:flex hidden">Operation Report</span></h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600"> <?php echo "<b>" . $_SESSION["name"] . "</b>"; ?></span>
                    <!-- <?php echo (strtoupper($_SESSION['role']) == 'LOGISTICS') ?
                                '<button id="showFormBtn" class="bg-lime-700 text-white py-2 px-4 rounded-lg hover:bg-lime-800 transition" alt="Send Quick"> Quick Act </button>' : ''; ?> -->
                </div>
            </div>


            <!-- 
                DISPLAYING OPEARTION REPORT
                -->
            <?php

            $result = mysqli_query($db, "SELECT * FROM operation_report");
            if (!$result) {
                return;
            }


            // Render the HTML table with overflow-x-scroll
            echo '<div class="w-[100%] overflow-x-auto mt-4 border border-gray-300">';
            echo '<table class="min-w-max w-full border-collapse border border-gray-400 text-left text-gray-700">';
            echo '<thead class="bg-lime-700 text-white">';
            echo '<tr>';
            echo '<th class="p-2 border text-[12px]">Mission Driver</th>';
            echo '<th class="p-2 border text-[12px]">From</th>';
            echo '<th class="p-2 border text-[12px]">To</th>';
            echo '<th class="p-2 border text-[12px]">Date</th>';
            echo '<th class="p-2 border text-[12px]">Description</th>';
            // echo '<th class="p-2 border text-[12px]">Acton</th>';

            if (strtoupper($_SESSION['role']) === 'D/CEO' || strtoupper($_SESSION['role']) === 'CEO') {
                // echo '<th class="p-2 border text-[12px]">"Actions"</th>';
                echo "";
            }
            if (strtolower($_SESSION['role']) === 'logistics') {
                echo (isset($_GET['status']) && $_GET['status'] === 'rejected') ? '<th class="p-2 border text-[12px]">Status</th>' : '<th class="p-2 border text-[12px]">Actions</th>';
            }

            echo '</tr></thead><tbody class="bg-white">';

            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr class="border border-b border-black bg-zinc-200 hover:bg-zinc-300">';
                echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['driver']) . '</td>';
                echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['op_from']) . '</td>';
                echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['op_to']) . '</td>';
                echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['date']) . '</td>';
                echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['description']) . '</td>';

                // if (strtoupper($_SESSION['role']) === 'D/CEO' || strtoupper($_SESSION['role']) === 'CEO') {
                //     echo '<td class="p-1 border">';
                //     if ($row['status'] === 'approved') {
                //         echo '<span class="text-green-500 font-bold ml-2 cursor-not-allowed">Approved</span>';
                //     } else if ($row['status'] === 'rejected') {
                //         echo '<span class="font-bold ml-2 text-red-500 cursor-not-allowed">Rejected</span>';
                //     } else if ($row['status'] === 'pending') {
                //         echo '<button onclick="viewRequest(' . $row['req_id'] . ')" class="text-red-500 hover:underline ml-2">Approve</button>';
                //     } else {
                //         echo '<a href="requests.php?cancel=' . urlencode($row['req_id']) . '" class="text-red-500 hover:underline">Reject</a>';
                //     }

                //     if ($row['status'] === 'approved') {
                //         echo '<a href="?id=' . urlencode($row['req_id']) . '" class="text-blue-500 hover:underline ml-2">⬇️ PDF</a>';
                //     } else {
                //         echo '<span class="text-gray-400 ml-2 cursor-not-allowed">⬇️ PDF</span>';
                //     }
                //     echo '</td>';
                // }

                if (strtolower($_SESSION['role']) === 'logistics') {
                    // if (isset($_GET['status']) && $_GET['status'] === 'rejected') {
                    //     echo '<td class="p-3 border text-red-500 hover:underline">
                    //                 Rejected
                    //           </td>';
                    // } else {
                    //     if ($row['verified_by'] == '-' && $row['status'] == "pending") {
                    //         echo '<td class="p-3 border">
                    //             <button onclick="viewRequest(' . $row['req_id'] . ')" class="text-blue-500 hover:underline">
                    //                 Verify
                    //             </button>
                    //           </td>';
                    //     } elseif ($row['status'] == "rejected") {
                    //         echo '<td class="text-red-600 p-3 border">Rejected</td>';
                    //     } elseif ($row['status'] == "approved") {
                    //         echo '<td class="text-green-500 font-bold p-3 border">Approved 
                    //     <a href="?id=' . urlencode($row['req_id']) . '" class="text-blue-500 hover:underline ml-2">⬇️ PDF</a>
                    //     </td>';
                    //     } else {
                    //         echo '<td class="text-blue-800 p-3 border">Verified</td>';
                    //     }
                    // }

                    echo "";
                }

                echo '</tr>';
            }

            echo '</tbody></table> </div>';
            ?>


        </div>
</body>

</html>